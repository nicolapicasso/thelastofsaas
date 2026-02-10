<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Helpers\Slug;

/**
 * WordPress XML Importer Service
 * Imports blog posts from WordPress WXR export files
 */
class WordPressImporter
{
    private Post $postModel;
    private Category $categoryModel;
    private string $uploadDir;
    private array $attachments = [];
    private array $importedPosts = [];
    private array $errors = [];
    private array $categoryCache = [];

    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        $this->uploadDir = dirname(__DIR__, 2) . '/public/uploads/blog';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Import posts from WordPress XML file
     */
    public function import(string $xmlFilePath): array
    {
        if (!file_exists($xmlFilePath)) {
            return [
                'success' => false,
                'error' => 'El archivo XML no existe',
                'imported' => 0,
                'errors' => []
            ];
        }

        // Load XML
        $xml = $this->loadXml($xmlFilePath);
        if (!$xml) {
            return [
                'success' => false,
                'error' => 'Error al parsear el archivo XML',
                'imported' => 0,
                'errors' => []
            ];
        }

        // First pass: collect all attachments (images)
        $this->collectAttachments($xml);

        // Second pass: import posts
        $imported = $this->importPosts($xml);

        return [
            'success' => true,
            'imported' => count($imported),
            'posts' => $imported,
            'errors' => $this->errors
        ];
    }

    /**
     * Load and parse XML file
     */
    private function loadXml(string $filePath): ?\SimpleXMLElement
    {
        libxml_use_internal_errors(true);

        $content = file_get_contents($filePath);
        if (!$content) {
            return null;
        }

        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            foreach (libxml_get_errors() as $error) {
                $this->errors[] = "XML Error: " . trim($error->message);
            }
            libxml_clear_errors();
            return null;
        }

        return $xml;
    }

    /**
     * Collect all attachments (images) from XML
     */
    private function collectAttachments(\SimpleXMLElement $xml): void
    {
        $namespaces = $xml->getNamespaces(true);
        $wp = $namespaces['wp'] ?? 'http://wordpress.org/export/1.2/';

        foreach ($xml->channel->item as $item) {
            $wpData = $item->children($wp);
            $postType = (string)$wpData->post_type;

            if ($postType === 'attachment') {
                $postId = (string)$wpData->post_id;
                $attachmentUrl = (string)$wpData->attachment_url;

                if ($postId && $attachmentUrl) {
                    $this->attachments[$postId] = $attachmentUrl;
                }
            }
        }
    }

    /**
     * Import posts from XML
     */
    private function importPosts(\SimpleXMLElement $xml): array
    {
        $imported = [];
        $namespaces = $xml->getNamespaces(true);
        $wp = $namespaces['wp'] ?? 'http://wordpress.org/export/1.2/';
        $content_ns = $namespaces['content'] ?? 'http://purl.org/rss/1.0/modules/content/';
        $dc = $namespaces['dc'] ?? 'http://purl.org/dc/elements/1.1/';

        foreach ($xml->channel->item as $item) {
            $wpData = $item->children($wp);
            $postType = (string)$wpData->post_type;

            // Only import posts (not pages, attachments, etc.)
            if ($postType !== 'post') {
                continue;
            }

            // Get status
            $status = (string)$wpData->status;
            if (!in_array($status, ['publish', 'draft'])) {
                continue;
            }

            try {
                $postData = $this->extractPostData($item, $wpData, $content_ns, $dc);

                if (!$postData) {
                    continue;
                }

                // Check if post already exists by slug
                $existingPost = $this->postModel->findBySlug($postData['slug']);
                if ($existingPost) {
                    $this->errors[] = "Post ya existe: {$postData['title']}";
                    continue;
                }

                // Create the post
                $postId = $this->postModel->createWithSlug($postData);

                if ($postId) {
                    $imported[] = [
                        'id' => $postId,
                        'title' => $postData['title'],
                        'slug' => $postData['slug']
                    ];
                }
            } catch (\Exception $e) {
                $this->errors[] = "Error importando post: " . $e->getMessage();
            }
        }

        return $imported;
    }

    /**
     * Extract post data from XML item
     */
    private function extractPostData(
        \SimpleXMLElement $item,
        \SimpleXMLElement $wpData,
        string $content_ns,
        string $dc
    ): ?array {
        // Get basic data
        $title = (string)$item->title;
        $link = (string)$item->link;
        $pubDate = (string)$item->pubDate;
        $creator = (string)$item->children($dc)->creator;

        // Get content
        $contentNode = $item->children($content_ns);
        $content = (string)$contentNode->encoded;

        if (empty($title) || empty($content)) {
            return null;
        }

        // Clean WordPress block markup
        $cleanContent = $this->cleanWordPressContent($content);

        // Extract slug from URL
        $slug = $this->extractSlugFromUrl($link) ?: Slug::generate($title);

        // Get category
        $categoryId = $this->getCategoryFromItem($item);

        // Get featured image
        $heroImage = $this->getFeaturedImage($wpData);

        // Generate excerpt from content
        $excerpt = $this->generateExcerpt($cleanContent);

        // Parse date
        $publishedAt = $pubDate ? date('Y-m-d H:i:s', strtotime($pubDate)) : date('Y-m-d H:i:s');

        // Map status
        $status = ((string)$wpData->status === 'publish') ? 'published' : 'draft';

        return [
            'title' => $title,
            'slug' => $slug,
            'content' => $cleanContent,
            'excerpt' => $excerpt,
            'category_id' => $categoryId,
            'hero_image' => $heroImage,
            'thumbnail' => $heroImage, // Use same as hero
            'status' => $status,
            'published_at' => $publishedAt,
            'meta_title' => $title,
            'meta_description' => $excerpt,
            'author_id' => 1, // Default admin user
        ];
    }

    /**
     * Clean WordPress block markup from content
     */
    private function cleanWordPressContent(string $content): string
    {
        // Remove WordPress block comments
        $content = preg_replace('/<!-- wp:[^>]+-->/s', '', $content);
        $content = preg_replace('/<!-- \/wp:[^>]+-->/s', '', $content);

        // Remove empty paragraphs
        $content = preg_replace('/<p>\s*<\/p>/', '', $content);

        // Remove extra whitespace
        $content = preg_replace('/\n{3,}/', "\n\n", $content);

        // Clean up WordPress-specific classes but keep the HTML structure
        $content = preg_replace('/class="wp-block-[^"]*"/', '', $content);
        $content = preg_replace('/class="has-[^"]*"/', '', $content);

        // Clean empty class attributes
        $content = preg_replace('/\s*class="\s*"/', '', $content);

        // Fix table formatting
        $content = preg_replace('/<figure[^>]*class="[^"]*wp-block-table[^"]*"[^>]*>/', '<div class="table-responsive">', $content);
        $content = str_replace('</figure>', '</div>', $content);

        return trim($content);
    }

    /**
     * Extract slug from WordPress URL
     */
    private function extractSlugFromUrl(string $url): ?string
    {
        // Parse URL and get path
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        // Remove trailing slash and get last segment
        $path = rtrim($path, '/');
        $segments = explode('/', $path);
        $slug = end($segments);

        return !empty($slug) ? $slug : null;
    }

    /**
     * Get or create category from item
     */
    private function getCategoryFromItem(\SimpleXMLElement $item): ?int
    {
        foreach ($item->category as $category) {
            $domain = (string)$category['domain'];
            if ($domain === 'category') {
                $nicename = (string)$category['nicename'];
                $name = (string)$category;

                if (!empty($nicename)) {
                    return $this->getOrCreateCategory($name, $nicename);
                }
            }
        }

        return null;
    }

    /**
     * Get existing category or create new one
     */
    private function getOrCreateCategory(string $name, string $slug): int
    {
        // Check cache first
        if (isset($this->categoryCache[$slug])) {
            return $this->categoryCache[$slug];
        }

        // Try to find existing category by slug
        $existing = $this->categoryModel->findBy('slug', $slug);
        if ($existing) {
            $this->categoryCache[$slug] = $existing['id'];
            return $existing['id'];
        }

        // Create new category
        $categoryId = $this->categoryModel->create([
            'name' => $name,
            'slug' => $slug,
            'is_active' => 1,
            'display_order' => 0
        ]);

        $this->categoryCache[$slug] = $categoryId;
        return $categoryId;
    }

    /**
     * Get featured image from post metadata
     */
    private function getFeaturedImage(\SimpleXMLElement $wpData): ?string
    {
        $thumbnailId = null;

        // Find _thumbnail_id in postmeta
        foreach ($wpData->postmeta as $meta) {
            $metaKey = (string)$meta->meta_key;
            if ($metaKey === '_thumbnail_id') {
                $thumbnailId = (string)$meta->meta_value;
                break;
            }
        }

        if (!$thumbnailId || !isset($this->attachments[$thumbnailId])) {
            return null;
        }

        // Download the image
        $remoteUrl = $this->attachments[$thumbnailId];
        return $this->downloadImage($remoteUrl);
    }

    /**
     * Download image from URL and save locally
     */
    private function downloadImage(string $url): ?string
    {
        try {
            // Get image content
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Mozilla/5.0 (compatible; TLOS Importer/1.0)'
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);

            $imageContent = @file_get_contents($url, false, $context);
            if (!$imageContent) {
                $this->errors[] = "No se pudo descargar imagen: {$url}";
                return null;
            }

            // Determine file extension
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $extension = 'jpg';
            }

            // Generate unique filename
            $filename = 'wp_' . uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . '/' . $filename;

            // Save file
            if (file_put_contents($filepath, $imageContent) === false) {
                $this->errors[] = "No se pudo guardar imagen: {$filename}";
                return null;
            }

            return '/uploads/blog/' . $filename;

        } catch (\Exception $e) {
            $this->errors[] = "Error descargando imagen {$url}: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Generate excerpt from content
     */
    private function generateExcerpt(string $content, int $length = 200): string
    {
        // Strip HTML tags
        $text = strip_tags($content);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Truncate
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length);
            // Cut at last word boundary
            $text = substr($text, 0, strrpos($text, ' '));
            $text .= '...';
        }

        return $text;
    }

    /**
     * Get stats about the XML file without importing
     */
    public function analyze(string $xmlFilePath): array
    {
        if (!file_exists($xmlFilePath)) {
            return ['error' => 'El archivo XML no existe'];
        }

        $xml = $this->loadXml($xmlFilePath);
        if (!$xml) {
            return ['error' => 'Error al parsear el archivo XML'];
        }

        $namespaces = $xml->getNamespaces(true);
        $wp = $namespaces['wp'] ?? 'http://wordpress.org/export/1.2/';

        $stats = [
            'posts' => 0,
            'pages' => 0,
            'attachments' => 0,
            'categories' => [],
            'authors' => []
        ];

        // Collect authors
        foreach ($xml->channel->children($wp)->author as $author) {
            $displayName = (string)$author->children($wp)->author_display_name;
            if ($displayName) {
                $stats['authors'][] = $displayName;
            }
        }

        // Collect items
        foreach ($xml->channel->item as $item) {
            $wpData = $item->children($wp);
            $postType = (string)$wpData->post_type;

            switch ($postType) {
                case 'post':
                    $stats['posts']++;
                    // Collect categories
                    foreach ($item->category as $category) {
                        if ((string)$category['domain'] === 'category') {
                            $catName = (string)$category;
                            if (!in_array($catName, $stats['categories'])) {
                                $stats['categories'][] = $catName;
                            }
                        }
                    }
                    break;
                case 'page':
                    $stats['pages']++;
                    break;
                case 'attachment':
                    $stats['attachments']++;
                    break;
            }
        }

        return $stats;
    }
}
