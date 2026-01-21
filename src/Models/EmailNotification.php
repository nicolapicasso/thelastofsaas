<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * EmailNotification Model
 * TLOS - The Last of SaaS
 *
 * Logs all email notifications sent by the system
 */
class EmailNotification extends Model
{
    protected string $table = 'email_notifications';

    protected array $fillable = [
        'event_id',
        'type',
        'recipient_email',
        'status',
        'extra_data',
    ];

    /**
     * Log an email notification
     */
    public function log(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Get notifications by event
     */
    public function getByEvent(int $eventId): array
    {
        return $this->where(['event_id' => $eventId], ['created_at' => 'DESC']);
    }

    /**
     * Get notifications by type
     */
    public function getByType(string $type, ?int $eventId = null): array
    {
        $conditions = ['type' => $type];
        if ($eventId !== null) {
            $conditions['event_id'] = $eventId;
        }
        return $this->where($conditions, ['created_at' => 'DESC']);
    }

    /**
     * Get failed notifications
     */
    public function getFailed(?int $eventId = null): array
    {
        $conditions = ['status' => 'failed'];
        if ($eventId !== null) {
            $conditions['event_id'] = $eventId;
        }
        return $this->where($conditions, ['created_at' => 'DESC']);
    }

    /**
     * Get notification stats for an event
     */
    public function getStats(int $eventId): array
    {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    type
                FROM {$this->table}
                WHERE event_id = ?
                GROUP BY type";

        return $this->db->fetchAll($sql, [$eventId]);
    }
}
