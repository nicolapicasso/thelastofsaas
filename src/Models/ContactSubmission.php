<?php
/**
 * Contact Submission Model
 * Stores form submissions
 * Omniwallet CMS
 */

namespace App\Models;

use App\Core\Model;

class ContactSubmission extends Model
{
    protected string $table = 'contact_submissions';

    protected array $fillable = [
        'block_id',
        'data',
        'ip_address',
        'user_agent',
        'page_url',
        'is_read',
        'created_at',
    ];

    /**
     * Get all submissions, optionally filtered by block
     */
    public function getAll(?int $blockId = null, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT cs.*, b.content as block_content
                FROM `{$this->table}` cs
                LEFT JOIN page_blocks b ON b.id = cs.block_id";

        $params = [];

        if ($blockId) {
            $sql .= " WHERE cs.block_id = ?";
            $params[] = $blockId;
        }

        $sql .= " ORDER BY cs.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $results = $this->db->fetchAll($sql, $params);

        // Parse JSON data
        return array_map(function($row) {
            $row['data'] = json_decode($row['data'], true) ?? [];
            $row['block_content'] = json_decode($row['block_content'], true) ?? [];
            return $row;
        }, $results);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(): int
    {
        return (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM `{$this->table}` WHERE is_read = 0"
        );
    }

    /**
     * Mark as read
     */
    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(): void
    {
        $this->db->query("UPDATE `{$this->table}` SET is_read = 1 WHERE is_read = 0");
    }

    /**
     * Delete old submissions (cleanup)
     */
    public function deleteOlderThan(int $days): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $stmt = $this->db->query(
            "DELETE FROM `{$this->table}` WHERE created_at < ?",
            [$date]
        );
        return $stmt->rowCount();
    }

    /**
     * Count submissions by date range
     */
    public function countByDateRange(string $startDate, string $endDate): int
    {
        return (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM `{$this->table}` WHERE created_at BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
    }
}
