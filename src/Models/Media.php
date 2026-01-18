<?php
/**
 * Media Model
 * Omniwallet CMS
 */

namespace App\Models;

use App\Core\Model;

class Media extends Model
{
    protected string $table = 'media';

    /**
     * Allowed mime types
     */
    public const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'application/pdf' => 'pdf',
        'video/mp4' => 'mp4',
        'video/webm' => 'webm'
    ];

    /**
     * Max file size (10MB)
     */
    public const MAX_SIZE = 10485760;

    /**
     * Get all media with pagination
     */
    public function getPaginated(int $page = 1, int $perPage = 24, ?string $type = null, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($type) {
            if ($type === 'image') {
                $sql .= " AND filetype LIKE 'image/%'";
                $countSql .= " AND filetype LIKE 'image/%'";
            } elseif ($type === 'video') {
                $sql .= " AND filetype LIKE 'video/%'";
                $countSql .= " AND filetype LIKE 'video/%'";
            } elseif ($type === 'document') {
                $sql .= " AND filetype = 'application/pdf'";
                $countSql .= " AND filetype = 'application/pdf'";
            }
        }

        if ($search) {
            $sql .= " AND (filename LIKE ? OR alt_text LIKE ?)";
            $countSql .= " AND (filename LIKE ? OR alt_text LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        // Get total count
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch()['total'];

        // Get items
        $sql .= " ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => ceil($total / $perPage),
            'current_page' => $page
        ];
    }

    /**
     * Get recent media
     */
    public function getRecent(int $limit = 12): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get images only
     */
    public function getImages(int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE filetype LIKE 'image/%'
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Find by filename
     */
    public function findByFilename(string $filename): ?array
    {
        return $this->findWhere(['filename' => $filename]);
    }

    /**
     * Get storage statistics
     */
    public function getStatistics(): array
    {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'images' => 0,
            'videos' => 0,
            'documents' => 0
        ];

        $stmt = $this->db->query("
            SELECT
                COUNT(*) as total_files,
                SUM(filesize) as total_size,
                SUM(CASE WHEN filetype LIKE 'image/%' THEN 1 ELSE 0 END) as images,
                SUM(CASE WHEN filetype LIKE 'video/%' THEN 1 ELSE 0 END) as videos,
                SUM(CASE WHEN filetype = 'application/pdf' THEN 1 ELSE 0 END) as documents
            FROM {$this->table}
        ");

        $result = $stmt->fetch();

        if ($result) {
            $stats['total_files'] = (int)$result['total_files'];
            $stats['total_size'] = (int)$result['total_size'];
            $stats['images'] = (int)$result['images'];
            $stats['videos'] = (int)$result['videos'];
            $stats['documents'] = (int)$result['documents'];
        }

        return $stats;
    }

    /**
     * Format file size
     */
    public static function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl(array $media): string
    {
        if (strpos($media['filetype'], 'image/') === 0) {
            // For images, use the image itself or a thumbnail version
            return $media['url'];
        }

        // For other types, return a placeholder based on type
        if (strpos($media['filetype'], 'video/') === 0) {
            return '/assets/images/placeholders/video.png';
        }

        if ($media['filetype'] === 'application/pdf') {
            return '/assets/images/placeholders/pdf.png';
        }

        return '/assets/images/placeholders/file.png';
    }
}
