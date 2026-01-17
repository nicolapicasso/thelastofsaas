<?php
/**
 * Team Member Card Partial
 * Used by team.php block template
 *
 * Expected variables:
 * - $member: array with member data
 * - $displayMode: string (minimalista, basica, sinapsis, detallada)
 */
?>
<a href="<?= _url('/equipo/' . htmlspecialchars($member['slug'])) ?>" class="team-member-card">
    <div class="member-photo">
        <?php if (!empty($member['photo'])): ?>
            <img src="<?= htmlspecialchars($member['photo']) ?>"
                 alt="<?= htmlspecialchars($member['name']) ?>"
                 class="photo-main"
                 loading="lazy">
            <?php if (!empty($member['photo_hover'])): ?>
                <img src="<?= htmlspecialchars($member['photo_hover']) ?>"
                     alt="<?= htmlspecialchars($member['name']) ?>"
                     class="photo-hover"
                     loading="lazy">
            <?php endif; ?>
        <?php else: ?>
            <div class="photo-placeholder">
                <i class="fas fa-user"></i>
            </div>
        <?php endif; ?>

        <?php if ($displayMode === 'minimalista'): ?>
            <!-- Overlay for minimal mode -->
            <div class="member-overlay">
                <h3><?= htmlspecialchars($member['name']) ?></h3>
                <?php if (!empty($member['role'])): ?>
                    <p class="member-role"><?= htmlspecialchars($member['role']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($displayMode !== 'minimalista'): ?>
        <div class="member-info">
            <h3><?= htmlspecialchars($member['name']) ?></h3>
            <?php if (!empty($member['role'])): ?>
                <p class="member-role"><?= htmlspecialchars($member['role']) ?></p>
            <?php endif; ?>

            <?php if ($displayMode === 'detallada' && !empty($member['bio'])): ?>
                <p class="member-bio"><?= htmlspecialchars(substr(strip_tags($member['bio']), 0, 120)) ?>...</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</a>
