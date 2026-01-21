<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * CompanyContact Model
 * TLOS - The Last of SaaS
 */
class CompanyContact extends Model
{
    protected string $table = 'company_contacts';

    protected array $fillable = [
        'company_id',
        'name',
        'position',
        'email',
        'phone',
        'is_primary',
        'notes',
    ];

    /**
     * Get all contacts for a company
     */
    public function getByCompany(int $companyId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE company_id = ? ORDER BY is_primary DESC, name ASC";
        return $this->db->fetchAll($sql, [$companyId]);
    }

    /**
     * Get primary contact for a company
     */
    public function getPrimaryContact(int $companyId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE company_id = ? AND is_primary = 1 LIMIT 1";
        return $this->db->fetch($sql, [$companyId]) ?: null;
    }

    /**
     * Set primary contact (unset others)
     */
    public function setPrimary(int $contactId, int $companyId): bool
    {
        // Unset all primary contacts for this company
        $this->db->query(
            "UPDATE {$this->table} SET is_primary = 0 WHERE company_id = ?",
            [$companyId]
        );

        // Set the new primary contact
        $this->db->query(
            "UPDATE {$this->table} SET is_primary = 1 WHERE id = ?",
            [$contactId]
        );

        return true;
    }

    /**
     * Delete all contacts for a company
     */
    public function deleteByCompany(int $companyId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE company_id = ?";
        $this->db->query($sql, [$companyId]);
        return true;
    }

    /**
     * Get contact emails as array for a company
     */
    public function getEmailsArray(int $companyId): array
    {
        $contacts = $this->getByCompany($companyId);
        $emails = [];

        foreach ($contacts as $contact) {
            if (!empty($contact['email'])) {
                $emails[] = trim($contact['email']);
            }
        }

        return $emails;
    }

    /**
     * Count contacts for a company
     */
    public function countByCompany(int $companyId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE company_id = ?";
        return (int) $this->db->fetchColumn($sql, [$companyId]);
    }
}
