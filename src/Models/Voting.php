<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Voting Model
 * TLOS - The Last of SaaS
 */
class Voting extends Model
{
    protected string $table = 'votings';

    protected array $fillable = [
        'event_id',
        'title',
        'slug',
        'description',
        'status',
        'show_vote_counts',
        'show_ranking',
        'allow_multiple_votes',
        'voting_start',
        'voting_end',
        'featured_image',
    ];

    /**
     * Get voting by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get active votings
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM votings
                WHERE status = 'active'
                AND (voting_start IS NULL OR voting_start <= NOW())
                AND (voting_end IS NULL OR voting_end >= NOW())
                ORDER BY created_at DESC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get votings for an event
     */
    public function getByEvent(int $eventId): array
    {
        return $this->where(['event_id' => $eventId], ['created_at' => 'DESC']);
    }

    /**
     * Check if voting is open
     */
    public function isOpen(array $voting): bool
    {
        if ($voting['status'] !== 'active') {
            return false;
        }

        $now = time();

        if (!empty($voting['voting_start']) && strtotime($voting['voting_start']) > $now) {
            return false;
        }

        if (!empty($voting['voting_end']) && strtotime($voting['voting_end']) < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get candidates for a voting
     */
    public function getCandidates(int $votingId, bool $activeOnly = false): array
    {
        $sql = "SELECT * FROM voting_candidates WHERE voting_id = ?";
        if ($activeOnly) {
            $sql .= " AND active = 1";
        }
        $sql .= " ORDER BY display_order ASC, votes DESC";

        return $this->db->fetchAll($sql, [$votingId]);
    }

    /**
     * Get candidates with ranking
     */
    public function getCandidatesRanked(int $votingId): array
    {
        $sql = "SELECT *, (votes + base_votes) as total_votes
                FROM voting_candidates
                WHERE voting_id = ? AND active = 1
                ORDER BY total_votes DESC, display_order ASC";

        return $this->db->fetchAll($sql, [$votingId]);
    }

    /**
     * Add candidate
     */
    public function addCandidate(int $votingId, array $data): int
    {
        return $this->db->insert('voting_candidates', [
            'voting_id' => $votingId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'logo_url' => $data['logo_url'] ?? null,
            'website_url' => $data['website_url'] ?? null,
            'votes' => 0,
            'base_votes' => $data['base_votes'] ?? 0,
            'display_order' => $data['display_order'] ?? 0,
            'active' => $data['active'] ?? 1,
        ]);
    }

    /**
     * Update candidate
     */
    public function updateCandidate(int $candidateId, array $data): bool
    {
        $allowedFields = ['name', 'description', 'logo_url', 'website_url', 'base_votes', 'display_order', 'active'];
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($filteredData)) {
            return false;
        }

        $affected = $this->db->update('voting_candidates', $filteredData, 'id = ?', [$candidateId]);
        return $affected > 0;
    }

    /**
     * Delete candidate
     */
    public function deleteCandidate(int $candidateId): bool
    {
        $affected = $this->db->delete('voting_candidates', 'id = ?', [$candidateId]);
        return $affected > 0;
    }

    /**
     * Get candidate
     */
    public function getCandidate(int $candidateId): ?array
    {
        $sql = "SELECT * FROM voting_candidates WHERE id = ?";
        return $this->db->fetch($sql, [$candidateId]);
    }

    /**
     * Check if user has already voted
     */
    public function hasVoted(int $votingId, ?string $ip = null, ?string $fingerprint = null, ?string $cookie = null): bool
    {
        if ($ip) {
            $sql = "SELECT COUNT(*) FROM votes WHERE voting_id = ? AND voter_ip = ?";
            if ((int) $this->db->fetchColumn($sql, [$votingId, $ip]) > 0) {
                return true;
            }
        }

        if ($fingerprint) {
            $sql = "SELECT COUNT(*) FROM votes WHERE voting_id = ? AND voter_fingerprint = ?";
            if ((int) $this->db->fetchColumn($sql, [$votingId, $fingerprint]) > 0) {
                return true;
            }
        }

        if ($cookie) {
            $sql = "SELECT COUNT(*) FROM votes WHERE voting_id = ? AND voter_cookie = ?";
            if ((int) $this->db->fetchColumn($sql, [$votingId, $cookie]) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register a vote
     */
    public function vote(int $votingId, int $candidateId, ?string $ip = null, ?string $fingerprint = null, ?string $cookie = null, ?string $email = null): bool
    {
        // Check if voting is open
        $voting = $this->find($votingId);
        if (!$voting || !$this->isOpen($voting)) {
            return false;
        }

        // Check for duplicate votes (if not allowing multiple)
        if (!$voting['allow_multiple_votes']) {
            if ($this->hasVoted($votingId, $ip, $fingerprint, $cookie)) {
                return false;
            }
        }

        // Register the vote
        $this->db->insert('votes', [
            'voting_id' => $votingId,
            'candidate_id' => $candidateId,
            'voter_ip' => $ip,
            'voter_fingerprint' => $fingerprint,
            'voter_cookie' => $cookie,
            'voter_email' => $email,
        ]);

        // Increment candidate votes
        $sql = "UPDATE voting_candidates SET votes = votes + 1 WHERE id = ?";
        $this->db->query($sql, [$candidateId]);
        return true;
    }

    /**
     * Get results
     */
    public function getResults(int $votingId): array
    {
        $voting = $this->find($votingId);
        if (!$voting) {
            return [];
        }

        $candidates = $this->getCandidatesRanked($votingId);

        $totalVotes = array_sum(array_column($candidates, 'total_votes'));

        return [
            'voting' => $voting,
            'candidates' => $candidates,
            'total_votes' => $totalVotes,
        ];
    }

    /**
     * Get total votes for a voting
     */
    public function getTotalVotes(int $votingId): int
    {
        $sql = "SELECT SUM(votes + base_votes) FROM voting_candidates WHERE voting_id = ?";
        return (int) $this->db->fetchColumn($sql, [$votingId]);
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'Borrador',
            'active' => 'Activa',
            'inactive' => 'Inactiva',
            'finished' => 'Finalizada',
        ];
    }
}
