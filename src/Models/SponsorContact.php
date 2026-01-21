<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * SponsorContact Model
 * TLOS - The Last of SaaS
 */
class SponsorContact extends Model
{
    protected string $table = 'sponsor_contacts';

    protected array $fillable = [
        'sponsor_id',
        'name',
        'position',
        'email',
        'phone',
        'is_primary',
        'notes',
    ];

    /**
     * Get all contacts for a sponsor
     */
    public function getBySponsor(int $sponsorId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE sponsor_id = ? ORDER BY is_primary DESC, name ASC";
        return $this->db->fetchAll($sql, [$sponsorId]);
    }

    /**
     * Get primary contact for a sponsor
     */
    public function getPrimaryContact(int $sponsorId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE sponsor_id = ? AND is_primary = 1 LIMIT 1";
        return $this->db->fetch($sql, [$sponsorId]) ?: null;
    }

    /**
     * Set primary contact (unset others)
     */
    public function setPrimary(int $contactId, int $sponsorId): bool
    {
        // Unset all primary contacts for this sponsor
        $this->db->query(
            "UPDATE {$this->table} SET is_primary = 0 WHERE sponsor_id = ?",
            [$sponsorId]
        );

        // Set the new primary contact
        $this->db->query(
            "UPDATE {$this->table} SET is_primary = 1 WHERE id = ?",
            [$contactId]
        );

        return true;
    }

    /**
     * Delete all contacts for a sponsor
     */
    public function deleteBySponsor(int $sponsorId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE sponsor_id = ?";
        $this->db->query($sql, [$sponsorId]);
        return true;
    }

    /**
     * Get contact emails as array for a sponsor
     */
    public function getEmailsArray(int $sponsorId): array
    {
        $contacts = $this->getBySponsor($sponsorId);
        $emails = [];

        foreach ($contacts as $contact) {
            if (!empty($contact['email'])) {
                $emails[] = trim($contact['email']);
            }
        }

        return $emails;
    }

    /**
     * Count contacts for a sponsor
     */
    public function countBySponsor(int $sponsorId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE sponsor_id = ?";
        return (int) $this->db->fetchColumn($sql, [$sponsorId]);
    }
}
