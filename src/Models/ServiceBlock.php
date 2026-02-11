<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Service Block Model
 * Extends Block for service-specific blocks
 * We're Sinapsis CMS
 */
class ServiceBlock extends Block
{
    protected string $table = 'service_blocks';

    protected array $fillable = [
        'service_id',
        'type',
        'sort_order',
        'content',
        'settings',
        'is_active',
    ];

    /**
     * Get blocks for service
     */
    public function getForService(int $serviceId): array
    {
        return $this->where(
            ['service_id' => $serviceId, 'is_active' => 1],
            ['sort_order' => 'ASC']
        );
    }

    /**
     * Get all blocks for service (including inactive) - for admin
     */
    public function getAllForService(int $serviceId): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE service_id = ? ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, [$serviceId]);
    }

    /**
     * Get active blocks for service with decoded JSON
     */
    public function getActiveForService(int $serviceId): array
    {
        $blocks = $this->getForService($serviceId);

        return array_map(function ($block) {
            $block['content'] = json_decode($block['content'], true) ?? [];
            $block['settings'] = json_decode($block['settings'], true) ?? [];
            // Mark as service_block for translation system to use correct entity_type
            $block['block_source'] = 'service_block';
            return $block;
        }, $blocks);
    }

    /**
     * Create block with JSON encoding
     */
    public function createBlock(array $data): int
    {
        // Convert block_type to type if present
        if (isset($data['block_type'])) {
            $data['type'] = $data['block_type'];
            unset($data['block_type']);
        }

        if (isset($data['content']) && is_array($data['content'])) {
            $data['content'] = json_encode($data['content'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = json_encode($data['settings'], JSON_UNESCAPED_UNICODE);
        }

        // Get next sort_order
        if (!isset($data['sort_order'])) {
            $maxOrder = $this->db->fetchColumn(
                "SELECT MAX(sort_order) FROM `{$this->table}` WHERE service_id = ?",
                [$data['service_id']]
            );
            $data['sort_order'] = ($maxOrder ?? -1) + 1;
        }

        return $this->create($data);
    }

    /**
     * Reorder blocks
     */
    public function reorder(int $serviceId, array $blockIds): void
    {
        foreach ($blockIds as $sortOrder => $blockId) {
            $this->db->update(
                $this->table,
                ['sort_order' => $sortOrder],
                'id = ? AND service_id = ?',
                [$blockId, $serviceId]
            );
        }
    }
}
