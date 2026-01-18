<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Voting;

/**
 * Public Voting Controller
 * TLOS - The Last of SaaS
 *
 * Public voting pages for awards and polls
 */
class VotingController extends Controller
{
    private Voting $votingModel;

    public function __construct()
    {
        parent::__construct();
        $this->votingModel = new Voting();
    }

    /**
     * Show voting page
     */
    public function show(string $slug): void
    {
        $voting = $this->votingModel->findBySlug($slug);

        if (!$voting) {
            $this->notFound();
            return;
        }

        // Check if voting is accessible
        if ($voting['status'] === 'draft') {
            $this->notFound();
            return;
        }

        // Check voting period
        $now = new \DateTime();
        $canVote = true;
        $message = null;

        if ($voting['status'] === 'inactive' || $voting['status'] === 'finished') {
            $canVote = false;
            $message = 'Esta votación ha finalizado';
        } elseif ($voting['voting_start'] && new \DateTime($voting['voting_start']) > $now) {
            $canVote = false;
            $message = 'La votación aún no ha comenzado';
        } elseif ($voting['voting_end'] && new \DateTime($voting['voting_end']) < $now) {
            $canVote = false;
            $message = 'La votación ha finalizado';
        }

        // Check if user already voted (if multiple votes not allowed)
        $hasVoted = false;
        if (!$voting['allow_multiple_votes']) {
            $hasVoted = $this->hasUserVoted($voting['id']);
        }

        if ($hasVoted) {
            $canVote = false;
            $message = 'Ya has votado en esta votación';
        }

        // Get candidates
        $candidates = $this->votingModel->getCandidates($voting['id']);

        // Calculate totals for display
        $totalVotes = array_sum(array_map(function($c) {
            return $c['votes'] + $c['base_votes'];
        }, $candidates));

        // Sort by votes if show_ranking is enabled
        if ($voting['show_ranking']) {
            usort($candidates, function($a, $b) {
                return ($b['votes'] + $b['base_votes']) - ($a['votes'] + $a['base_votes']);
            });
        }

        $this->render('voting/show', [
            'voting' => $voting,
            'candidates' => $candidates,
            'totalVotes' => $totalVotes,
            'canVote' => $canVote,
            'hasVoted' => $hasVoted,
            'message' => $message,
            'meta_title' => $voting['title'] . ' - Votación',
            'meta_description' => $voting['description'],
            'meta_image' => $voting['featured_image']
        ]);
    }

    /**
     * Process vote submission
     */
    public function vote(string $slug): void
    {
        $voting = $this->votingModel->findBySlug($slug);

        if (!$voting) {
            $this->jsonError('Votación no encontrada', 404);
            return;
        }

        // Validate CSRF
        if (!$this->validateCsrf()) {
            $this->jsonError('Token de seguridad inválido', 403);
            return;
        }

        // Check if voting is active
        if ($voting['status'] !== 'active') {
            $this->jsonError('Esta votación no está activa', 400);
            return;
        }

        // Check voting period
        $now = new \DateTime();
        if ($voting['voting_start'] && new \DateTime($voting['voting_start']) > $now) {
            $this->jsonError('La votación aún no ha comenzado', 400);
            return;
        }
        if ($voting['voting_end'] && new \DateTime($voting['voting_end']) < $now) {
            $this->jsonError('La votación ha finalizado', 400);
            return;
        }

        // Check if already voted
        if (!$voting['allow_multiple_votes'] && $this->hasUserVoted($voting['id'])) {
            $this->jsonError('Ya has votado en esta votación', 400);
            return;
        }

        // Get candidate ID
        $candidateId = (int)($_POST['candidate_id'] ?? 0);
        if (!$candidateId) {
            $this->jsonError('Selecciona un candidato', 400);
            return;
        }

        // Verify candidate belongs to this voting
        $candidate = $this->votingModel->getCandidate($candidateId);
        if (!$candidate || $candidate['voting_id'] != $voting['id']) {
            $this->jsonError('Candidato no válido', 400);
            return;
        }

        if (!$candidate['active']) {
            $this->jsonError('Este candidato no está disponible', 400);
            return;
        }

        // Collect anti-fraud data
        $voteData = [
            'voting_id' => $voting['id'],
            'candidate_id' => $candidateId,
            'ip_address' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'fingerprint' => $_POST['fingerprint'] ?? null,
            'session_id' => session_id()
        ];

        // Register vote
        $result = $this->votingModel->registerVote($voteData);

        if (!$result) {
            $this->jsonError('Error al registrar el voto', 500);
            return;
        }

        // Mark as voted in session
        $this->markAsVoted($voting['id']);

        // Get updated stats
        $candidate = $this->votingModel->getCandidate($candidateId);
        $totalVotes = $this->votingModel->getTotalVotes($voting['id']);

        $this->json([
            'success' => true,
            'message' => '¡Gracias por tu voto!',
            'candidate_votes' => $candidate['votes'] + $candidate['base_votes'],
            'total_votes' => $totalVotes
        ]);
    }

    /**
     * Show voting results page
     */
    public function results(string $slug): void
    {
        $voting = $this->votingModel->findBySlug($slug);

        if (!$voting || $voting['status'] === 'draft') {
            $this->notFound();
            return;
        }

        // Get candidates sorted by votes
        $candidates = $this->votingModel->getCandidates($voting['id']);

        usort($candidates, function($a, $b) {
            return ($b['votes'] + $b['base_votes']) - ($a['votes'] + $a['base_votes']);
        });

        $totalVotes = array_sum(array_map(function($c) {
            return $c['votes'] + $c['base_votes'];
        }, $candidates));

        $this->render('voting/results', [
            'voting' => $voting,
            'candidates' => $candidates,
            'totalVotes' => $totalVotes,
            'meta_title' => 'Resultados - ' . $voting['title']
        ]);
    }

    /**
     * Check if user has voted
     */
    private function hasUserVoted(int $votingId): bool
    {
        // Check session
        $votedList = $_SESSION['voted_votings'] ?? [];
        if (in_array($votingId, $votedList)) {
            return true;
        }

        // Check cookie
        $cookieName = 'voted_' . $votingId;
        if (isset($_COOKIE[$cookieName])) {
            return true;
        }

        // Check IP in database (last 24 hours)
        $ip = $this->getClientIp();
        if ($this->votingModel->hasIpVoted($votingId, $ip, 24)) {
            return true;
        }

        return false;
    }

    /**
     * Mark voting as voted
     */
    private function markAsVoted(int $votingId): void
    {
        // Session
        if (!isset($_SESSION['voted_votings'])) {
            $_SESSION['voted_votings'] = [];
        }
        $_SESSION['voted_votings'][] = $votingId;

        // Cookie (30 days)
        $cookieName = 'voted_' . $votingId;
        setcookie($cookieName, '1', time() + (30 * 24 * 60 * 60), '/', '', true, true);
    }

    /**
     * Get client IP address
     */
    private function getClientIp(): string
    {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Handle comma-separated IPs (X-Forwarded-For)
                if (str_contains($ip, ',')) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }
}
