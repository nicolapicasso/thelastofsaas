<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * User Model
 * Omniwallet CMS
 */
class User extends Model
{
    protected string $table = 'users';

    protected array $fillable = [
        'email',
        'password',
        'name',
        'role',
        'avatar',
        'is_active',
        'last_login',
    ];

    protected array $hidden = ['password'];

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE email = ? LIMIT 1";

        return $this->db->fetch($sql, [$email]);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash password
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(int $userId): void
    {
        $this->db->update(
            $this->table,
            ['last_login' => date('Y-m-d H:i:s')],
            'id = ?',
            [$userId]
        );
    }

    /**
     * Get active users
     */
    public function getActive(): array
    {
        return $this->where(['is_active' => 1], ['name' => 'ASC']);
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): array
    {
        return $this->where(['role' => $role, 'is_active' => 1], ['name' => 'ASC']);
    }

    /**
     * Create user with hashed password
     */
    public function createUser(array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }

        return $this->create($data);
    }

    /**
     * Update user password
     */
    public function updatePassword(int $userId, string $password): bool
    {
        return $this->update($userId, [
            'password' => $this->hashPassword($password),
        ]);
    }
}
