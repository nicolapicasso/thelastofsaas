<?php
/**
 * Partner Model
 * Omniwallet CMS
 */

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

class Partner extends Model
{
    protected string $table = 'partners';

    /**
     * Partner types
     */
    public const TYPES = [
        'agency' => 'Agencia',
        'tech_partner' => 'Tech Partner'
    ];

    /**
     * Get all active partners
     */
    public function getActive(int $limit = 100): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1
            ORDER BY is_featured DESC, sort_order ASC, name ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get featured partners
     */
    public function getFeatured(int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1 AND is_featured = 1
            ORDER BY sort_order ASC, name ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get certified partners
     */
    public function getCertified(int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1 AND is_certified = 1
            ORDER BY is_featured DESC, sort_order ASC, name ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get partners with logos (for carousel)
     */
    public function getWithLogos(int $limit = 30): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1 AND logo IS NOT NULL AND logo != ''
            ORDER BY is_featured DESC, sort_order ASC, name ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE slug = ? AND is_active = 1
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get by partner type
     */
    public function getByType(string $type): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1 AND partner_type = ?
            ORDER BY is_featured DESC, sort_order ASC, name ASC
        ");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    /**
     * Get by country
     */
    public function getByCountry(string $country): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1 AND country = ?
            ORDER BY city ASC, name ASC
        ");
        $stmt->execute([$country]);
        return $stmt->fetchAll();
    }

    /**
     * Get by city
     */
    public function getByCity(string $city): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1 AND city = ?
            ORDER BY name ASC
        ");
        $stmt->execute([$city]);
        return $stmt->fetchAll();
    }

    /**
     * Get unique countries with partner counts
     */
    public function getCountriesWithCounts(): array
    {
        $stmt = $this->db->query("
            SELECT country, COUNT(*) as partner_count
            FROM {$this->table}
            WHERE is_active = 1
              AND country IS NOT NULL
              AND country != ''
            GROUP BY country
            HAVING partner_count > 0
            ORDER BY country ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get unique cities with partner counts
     */
    public function getCitiesWithCounts(?string $country = null): array
    {
        if ($country) {
            $stmt = $this->db->prepare("
                SELECT city, COUNT(*) as partner_count
                FROM {$this->table}
                WHERE is_active = 1
                  AND country = ?
                  AND city IS NOT NULL
                  AND city != ''
                GROUP BY city
                HAVING partner_count > 0
                ORDER BY city ASC
            ");
            $stmt->execute([$country]);
        } else {
            $stmt = $this->db->query("
                SELECT city, country, COUNT(*) as partner_count
                FROM {$this->table}
                WHERE is_active = 1
                  AND city IS NOT NULL
                  AND city != ''
                GROUP BY city, country
                HAVING partner_count > 0
                ORDER BY country ASC, city ASC
            ");
        }
        return $stmt->fetchAll();
    }

    /**
     * Get partner types with counts
     */
    public function getTypesWithCounts(): array
    {
        $stmt = $this->db->query("
            SELECT partner_type, COUNT(*) as partner_count
            FROM {$this->table}
            WHERE is_active = 1
            GROUP BY partner_type
            HAVING partner_count > 0
            ORDER BY partner_type ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Search partners with filters
     */
    public function search(
        ?string $query = null,
        ?string $country = null,
        ?string $city = null,
        ?string $type = null,
        ?bool $certified = null
    ): array {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $params = [];

        if ($query) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($country) {
            $sql .= " AND country = ?";
            $params[] = $country;
        }

        if ($city) {
            $sql .= " AND city = ?";
            $params[] = $city;
        }

        if ($type) {
            $sql .= " AND partner_type = ?";
            $params[] = $type;
        }

        if ($certified !== null) {
            $sql .= " AND is_certified = ?";
            $params[] = $certified ? 1 : 0;
        }

        $sql .= " ORDER BY is_featured DESC, sort_order ASC, name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Count total active partners
     */
    public function countActive(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) as total FROM {$this->table}
            WHERE is_active = 1
        ");
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    /**
     * Create partner with auto-slug
     */
    public function createPartner(array $data): int
    {
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Slug::generate($data['name'], $this->table);
        }
        return parent::create($data);
    }

    /**
     * Update partner with auto-slug
     */
    public function updatePartner(int $id, array $data): bool
    {
        if (isset($data['slug']) && empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Slug::generate($data['name'], $this->table, $id);
        }
        return parent::update($id, $data);
    }

    /**
     * Get all for admin (including inactive)
     */
    public function getAllForAdmin(): array
    {
        return $this->db->query("
            SELECT * FROM {$this->table}
            ORDER BY is_active DESC, is_featured DESC, sort_order ASC, name ASC
        ")->fetchAll();
    }

    /**
     * Get related partners (same country or type)
     */
    public function getRelated(int $excludeId, ?string $country = null, ?string $type = null, int $limit = 4): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 AND id != ?";
        $params = [$excludeId];

        if ($country && $type) {
            $sql .= " AND (country = ? OR partner_type = ?)";
            $params[] = $country;
            $params[] = $type;
        } elseif ($country) {
            $sql .= " AND country = ?";
            $params[] = $country;
        } elseif ($type) {
            $sql .= " AND partner_type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY is_featured DESC, RAND() LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get for Schema.org structured data
     */
    public function getForSchema(array $partner): array
    {
        $schema = [
            '@type' => 'Organization',
            'name' => $partner['name'],
        ];

        if (!empty($partner['website'])) {
            $schema['url'] = $partner['website'];
        }

        if (!empty($partner['logo'])) {
            $schema['logo'] = $partner['logo'];
        }

        if (!empty($partner['email'])) {
            $schema['email'] = $partner['email'];
        }

        if (!empty($partner['phone'])) {
            $schema['telephone'] = $partner['phone'];
        }

        if (!empty($partner['country']) || !empty($partner['city'])) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
            ];
            if (!empty($partner['city'])) {
                $schema['address']['addressLocality'] = $partner['city'];
            }
            if (!empty($partner['country'])) {
                $schema['address']['addressCountry'] = $partner['country'];
            }
        }

        return $schema;
    }
}
