<?php
/**
 * SEO Service
 * Handles meta tags, Schema.org, Open Graph, and sitemap generation
 * Omniwallet CMS
 */

namespace App\Services;

class SEOService
{
    private string $title = '';
    private string $description = '';
    private string $canonical = '';
    private string $image = '';
    private string $type = 'website';
    private string $robots = 'index, follow';
    private array $schemas = [];
    private array $openGraph = [];
    private array $twitterCard = [];

    private string $siteName = '';
    private string $baseUrl = '';
    private string $defaultImage = '/assets/images/og-default.jpg';
    private string $twitterHandle = '';

    private array $supportedLanguages = ['es', 'en', 'it', 'fr', 'de'];
    private string $currentLanguage = 'es';
    private string $defaultLanguage = 'es';

    /**
     * Set page title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set meta description
     */
    public function setDescription(string $description): self
    {
        $this->description = substr($description, 0, 160);
        return $this;
    }

    /**
     * Set canonical URL
     */
    public function setCanonical(string $url): self
    {
        $this->canonical = $url;
        return $this;
    }

    /**
     * Set Open Graph image
     */
    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Set Open Graph type
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set robots meta
     */
    public function setRobots(string $robots): self
    {
        $this->robots = $robots;
        return $this;
    }

    /**
     * Set current language
     */
    public function setLanguage(string $language): self
    {
        $this->currentLanguage = $language;
        return $this;
    }

    /**
     * Set site name (for OG and meta tags)
     */
    public function setSiteName(string $name): self
    {
        $this->siteName = $name;
        return $this;
    }

    /**
     * Set base URL (for canonical, OG, sitemap)
     */
    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = rtrim($url, '/');
        return $this;
    }

    /**
     * Set Twitter handle
     */
    public function setTwitterHandle(string $handle): self
    {
        $this->twitterHandle = $handle;
        return $this;
    }

    /**
     * Set default OG image
     */
    public function setDefaultImage(string $image): self
    {
        $this->defaultImage = $image;
        return $this;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Add Schema.org structured data
     */
    public function addSchema(array $schema): self
    {
        $schema['@context'] = 'https://schema.org';
        $this->schemas[] = $schema;
        return $this;
    }

    /**
     * Get page title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Render all meta tags
     */
    public function renderMetaTags(): string
    {
        $output = [];

        // Basic meta
        $output[] = "<title>{$this->escape($this->title)}</title>";
        $output[] = "<meta name=\"description\" content=\"{$this->escape($this->description)}\">";
        $output[] = "<meta name=\"robots\" content=\"{$this->robots}\">";

        // Canonical (with language prefix if not default)
        if ($this->canonical) {
            $fullCanonical = $this->getFullUrl($this->canonical, $this->currentLanguage);
            $output[] = "<link rel=\"canonical\" href=\"{$fullCanonical}\">";
        }

        // Hreflang tags for SEO
        $output[] = $this->renderHreflangTags();

        // Open Graph
        $output[] = $this->renderOpenGraph();

        // Twitter Card
        $output[] = $this->renderTwitterCard();

        return implode("\n    ", $output);
    }

    /**
     * Render hreflang tags for multi-language SEO
     */
    private function renderHreflangTags(): string
    {
        if (empty($this->canonical)) {
            return '';
        }

        $tags = [];

        // Language mapping for hreflang (ISO 639-1 codes)
        $hreflangMap = [
            'es' => 'es',
            'en' => 'en',
            'it' => 'it',
            'fr' => 'fr',
            'de' => 'de'
        ];

        foreach ($this->supportedLanguages as $lang) {
            $url = $this->getFullUrl($this->canonical, $lang);
            $hreflang = $hreflangMap[$lang] ?? $lang;
            $tags[] = "<link rel=\"alternate\" hreflang=\"{$hreflang}\" href=\"{$url}\">";
        }

        // Add x-default pointing to default language (Spanish)
        $defaultUrl = $this->getFullUrl($this->canonical, $this->defaultLanguage);
        $tags[] = "<link rel=\"alternate\" hreflang=\"x-default\" href=\"{$defaultUrl}\">";

        return implode("\n    ", $tags);
    }

    /**
     * Get full URL with language prefix
     */
    private function getFullUrl(string $path, string $language): string
    {
        // Default language doesn't need prefix
        if ($language === $this->defaultLanguage) {
            return $this->baseUrl . $path;
        }

        return $this->baseUrl . '/' . $language . $path;
    }

    /**
     * Render Open Graph tags
     */
    private function renderOpenGraph(): string
    {
        $og = [];
        $og[] = "<meta property=\"og:site_name\" content=\"{$this->escape($this->siteName)}\">";
        $og[] = "<meta property=\"og:title\" content=\"{$this->escape($this->title)}\">";
        $og[] = "<meta property=\"og:description\" content=\"{$this->escape($this->description)}\">";
        $og[] = "<meta property=\"og:type\" content=\"{$this->type}\">";

        if ($this->canonical) {
            $og[] = "<meta property=\"og:url\" content=\"{$this->baseUrl}{$this->canonical}\">";
        }

        $image = $this->image ?: $this->defaultImage;
        if ($image) {
            $imageUrl = strpos($image, 'http') === 0 ? $image : $this->baseUrl . $image;
            $og[] = "<meta property=\"og:image\" content=\"{$imageUrl}\">";
        }

        // Locale based on current language
        $localeMap = [
            'es' => 'es_ES',
            'en' => 'en_US',
            'it' => 'it_IT',
            'fr' => 'fr_FR',
            'de' => 'de_DE'
        ];
        $currentLocale = $localeMap[$this->currentLanguage] ?? 'es_ES';
        $og[] = "<meta property=\"og:locale\" content=\"{$currentLocale}\">";

        // Add alternate locales
        foreach ($this->supportedLanguages as $lang) {
            if ($lang !== $this->currentLanguage) {
                $altLocale = $localeMap[$lang] ?? '';
                if ($altLocale) {
                    $og[] = "<meta property=\"og:locale:alternate\" content=\"{$altLocale}\">";
                }
            }
        }

        return implode("\n    ", $og);
    }

    /**
     * Render Twitter Card tags
     */
    private function renderTwitterCard(): string
    {
        $twitter = [];
        $twitter[] = "<meta name=\"twitter:card\" content=\"summary_large_image\">";
        $twitter[] = "<meta name=\"twitter:site\" content=\"{$this->twitterHandle}\">";
        $twitter[] = "<meta name=\"twitter:title\" content=\"{$this->escape($this->title)}\">";
        $twitter[] = "<meta name=\"twitter:description\" content=\"{$this->escape($this->description)}\">";

        $image = $this->image ?: $this->defaultImage;
        if ($image) {
            $imageUrl = strpos($image, 'http') === 0 ? $image : $this->baseUrl . $image;
            $twitter[] = "<meta name=\"twitter:image\" content=\"{$imageUrl}\">";
        }

        return implode("\n    ", $twitter);
    }

    /**
     * Render Schema.org JSON-LD
     */
    public function renderSchemas(): string
    {
        if (empty($this->schemas)) {
            return '';
        }

        $output = [];
        foreach ($this->schemas as $schema) {
            $output[] = '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
        }

        return implode("\n", $output);
    }

    /**
     * Generate sitemap XML
     */
    public static function generateSitemap(?string $siteUrl = null): string
    {
        $db = \App\Core\Database::getInstance()->getConnection();

        // Try to get site_url from settings if not provided
        if (!$siteUrl) {
            try {
                $stmt = $db->prepare("SELECT value FROM settings WHERE `key` = 'site_url' LIMIT 1");
                $stmt->execute();
                $siteUrl = $stmt->fetchColumn() ?: '';
            } catch (\Exception $e) {
                $siteUrl = '';
            }
        }
        $baseUrl = rtrim($siteUrl, '/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static pages
        $staticPages = ['/', '/funcionalidades', '/precios', '/casos-de-exito', '/blog', '/ayuda', '/contacto'];
        foreach ($staticPages as $page) {
            $xml .= self::sitemapUrl($baseUrl . $page, date('Y-m-d'), 'daily', $page === '/' ? '1.0' : '0.8');
        }

        // Dynamic pages
        $stmt = $db->query("SELECT slug, updated_at FROM pages WHERE status = 'published'");
        foreach ($stmt->fetchAll() as $row) {
            if (!in_array('/' . $row['slug'], $staticPages)) {
                $xml .= self::sitemapUrl($baseUrl . '/' . $row['slug'], $row['updated_at'], 'weekly', '0.7');
            }
        }

        // Blog posts
        if (self::tableExists($db, 'posts')) {
            $stmt = $db->query("SELECT slug, updated_at FROM posts WHERE status = 'published'");
            foreach ($stmt->fetchAll() as $row) {
                $xml .= self::sitemapUrl($baseUrl . '/blog/' . $row['slug'], $row['updated_at'], 'weekly', '0.6');
            }
        }

        // Success cases
        if (self::tableExists($db, 'success_cases')) {
            $stmt = $db->query("SELECT slug, updated_at FROM success_cases WHERE status = 'published'");
            foreach ($stmt->fetchAll() as $row) {
                $xml .= self::sitemapUrl($baseUrl . '/casos-de-exito/' . $row['slug'], $row['updated_at'], 'monthly', '0.6');
            }
        }

        // Knowledge articles
        if (self::tableExists($db, 'knowledge_articles')) {
            $stmt = $db->query("SELECT slug, updated_at FROM knowledge_articles WHERE status = 'published'");
            foreach ($stmt->fetchAll() as $row) {
                $xml .= self::sitemapUrl($baseUrl . '/ayuda/' . $row['slug'], $row['updated_at'], 'weekly', '0.5');
            }
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Check if a table exists in the database
     */
    private static function tableExists(\PDO $db, string $tableName): bool
    {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $stmt->execute([$tableName]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate single sitemap URL entry
     */
    private static function sitemapUrl(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        $lastmod = date('Y-m-d', strtotime($lastmod));
        return "  <url>\n    <loc>{$loc}</loc>\n    <lastmod>{$lastmod}</lastmod>\n    <changefreq>{$changefreq}</changefreq>\n    <priority>{$priority}</priority>\n  </url>\n";
    }

    /**
     * Escape HTML
     */
    private function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate FAQ Schema from FAQs
     */
    public static function getFAQSchema(array $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($faq['answer'])
                    ]
                ];
            }, $faqs)
        ];
    }

    /**
     * Generate Breadcrumb Schema
     */
    public static function getBreadcrumbSchema(array $items): array
    {
        $listItems = [];
        foreach ($items as $i => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems
        ];
    }
}
