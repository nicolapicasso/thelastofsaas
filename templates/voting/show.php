<?php
/**
 * Voting Page Template
 * TLOS - The Last of SaaS
 */
?>

<section class="voting-page">
    <div class="container">
        <!-- Header -->
        <div class="voting-header">
            <?php if ($voting['featured_image']): ?>
                <img src="<?= htmlspecialchars($voting['featured_image']) ?>" alt="" class="voting-banner">
            <?php endif; ?>
            <h1><?= htmlspecialchars($voting['title']) ?></h1>
            <?php if ($voting['description']): ?>
                <p class="voting-description"><?= nl2br(htmlspecialchars($voting['description'])) ?></p>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-<?= $canVote ? 'info' : 'warning' ?>">
                    <i class="fas fa-<?= $canVote ? 'info-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($voting['show_vote_counts']): ?>
                <div class="voting-stats">
                    <span><strong><?= $totalVotes ?></strong> votos totales</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Candidates -->
        <div class="candidates-grid">
            <?php foreach ($candidates as $index => $candidate): ?>
                <?php
                $percentage = $totalVotes > 0 ? (($candidate['votes'] + $candidate['base_votes']) / $totalVotes) * 100 : 0;
                ?>
                <div class="candidate-card <?= !$candidate['active'] ? 'disabled' : '' ?>" data-id="<?= $candidate['id'] ?>">
                    <?php if ($voting['show_ranking'] && $index < 3): ?>
                        <span class="candidate-rank rank-<?= $index + 1 ?>">
                            <?= match($index) { 0 => '🥇', 1 => '🥈', 2 => '🥉' } ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($candidate['logo_url']): ?>
                        <img src="<?= htmlspecialchars($candidate['logo_url']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>" class="candidate-logo">
                    <?php else: ?>
                        <div class="candidate-logo-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>

                    <h3 class="candidate-name"><?= htmlspecialchars($candidate['name']) ?></h3>

                    <?php if ($candidate['description']): ?>
                        <p class="candidate-description"><?= htmlspecialchars($candidate['description']) ?></p>
                    <?php endif; ?>

                    <?php if ($candidate['website_url']): ?>
                        <a href="<?= htmlspecialchars($candidate['website_url']) ?>" target="_blank" class="candidate-link">
                            <i class="fas fa-external-link-alt"></i> Web
                        </a>
                    <?php endif; ?>

                    <?php if ($voting['show_vote_counts']): ?>
                        <div class="candidate-votes">
                            <div class="vote-bar">
                                <div class="vote-bar-fill" style="width: <?= $percentage ?>%;"></div>
                            </div>
                            <span class="vote-count"><?= $candidate['votes'] + $candidate['base_votes'] ?> votos (<?= number_format($percentage, 1) ?>%)</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($canVote && $candidate['active']): ?>
                        <button type="button" class="btn btn-primary vote-btn" onclick="vote(<?= $candidate['id'] ?>)">
                            <i class="fas fa-vote-yea"></i> Votar
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($hasVoted): ?>
            <div class="voted-message">
                <i class="fas fa-check-circle"></i>
                <p>¡Gracias por tu voto!</p>
                <a href="/votar/<?= $voting['slug'] ?>/resultados" class="btn btn-outline">Ver resultados</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.voting-page {
    padding: 3rem 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
}
.voting-page .container {
    max-width: 1000px;
}

.voting-header {
    text-align: center;
    margin-bottom: 3rem;
}
.voting-banner {
    max-width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}
.voting-header h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
}
.voting-description {
    font-size: 1.1rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto 1.5rem;
    line-height: 1.6;
}
.voting-stats {
    font-size: 1.1rem;
    color: var(--text-secondary);
}
.voting-stats strong {
    color: var(--primary-color);
    font-size: 1.25rem;
}

.alert {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}
.alert-info {
    background: #EBF5FF;
    color: #1E40AF;
}
.alert-warning {
    background: #FEF3C7;
    color: #92400E;
}

.candidates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.candidate-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
}
.candidate-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.candidate-card.disabled {
    opacity: 0.6;
    pointer-events: none;
}

.candidate-rank {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.5rem;
}

.candidate-logo {
    width: 100px;
    height: 100px;
    object-fit: contain;
    border-radius: 50%;
    margin-bottom: 1rem;
    background: var(--bg-secondary);
    padding: 0.5rem;
}
.candidate-logo-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2.5rem;
    color: var(--text-muted);
}

.candidate-name {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.candidate-description {
    font-size: 0.9rem;
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 1rem;
}

.candidate-link {
    display: inline-block;
    font-size: 0.85rem;
    color: var(--primary-color);
    text-decoration: none;
    margin-bottom: 1rem;
}
.candidate-link:hover {
    text-decoration: underline;
}

.candidate-votes {
    margin-bottom: 1rem;
}
.vote-bar {
    height: 8px;
    background: var(--bg-secondary);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}
.vote-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
    border-radius: 4px;
    transition: width 0.3s ease;
}
.vote-count {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.vote-btn {
    width: 100%;
}

.voted-message {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.voted-message i {
    font-size: 3rem;
    color: var(--success-color);
    margin-bottom: 1rem;
}
.voted-message p {
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .voting-header h1 {
        font-size: 1.75rem;
    }
    .candidates-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function vote(candidateId) {
    const btn = event.target.closest('.vote-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Votando...';

    fetch('/votar/<?= $voting['slug'] ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            _csrf_token: '<?= $csrf_token ?>',
            candidate_id: candidateId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Show success and reload
            btn.innerHTML = '<i class="fas fa-check"></i> ¡Votado!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');

            // Update vote count if shown
            <?php if ($voting['show_vote_counts']): ?>
            const card = btn.closest('.candidate-card');
            const countEl = card.querySelector('.vote-count');
            if (countEl) {
                countEl.textContent = data.candidate_votes + ' votos';
            }
            <?php endif; ?>

            // Reload after delay to update everything
            setTimeout(() => location.reload(), 1500);
        } else {
            alert(data.error || 'Error al votar');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-vote-yea"></i> Votar';
        }
    })
    .catch(err => {
        alert('Error de conexión');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-vote-yea"></i> Votar';
    });
}
</script>
