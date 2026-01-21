<?php
/**
 * Sponsors Block Template
 * TLOS - The Last of SaaS
 */

use App\Models\Sponsor;
use App\Models\Event;

$sponsorModel = new Sponsor();
$eventModel = new Event();

// Get settings
$displayMode = $settings['display_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 4);
$visibleItems = (int)($settings['visible_items'] ?? 5);
$selectionMode = $settings['selection_mode'] ?? 'all';
$eventId = $settings['event_id'] ?? '';
$levelFilter = $settings['level_filter'] ?? '';
$selectedSponsors = $settings['selected_sponsors'] ?? [];
$limit = (int)($settings['limit'] ?? 12);
$logoHeight = (int)($settings['logo_height'] ?? 80);
$showName = $settings['show_name'] ?? true;
$showLevel = $settings['show_level'] ?? true;
$grayscale = !empty($settings['grayscale']);
$autoplay = !empty($settings['autoplay']);
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Handle selected_sponsors as JSON string
if (is_string($selectedSponsors)) {
    $selectedSponsors = json_decode($selectedSponsors, true) ?? [];
}

// Get sponsors based on selection mode
$sponsors = [];
switch ($selectionMode) {
    case 'event':
        if ($eventId) {
            $sponsors = $eventModel->getSponsors((int)$eventId);
            if ($limit > 0) {
                $sponsors = array_slice($sponsors, 0, $limit);
            }
        }
        break;

    case 'level':
        if ($levelFilter) {
            $allSponsors = $sponsorModel->getActive();
            $sponsors = array_filter($allSponsors, fn($s) => ($s['level'] ?? '') === $levelFilter);
            if ($limit > 0) {
                $sponsors = array_slice($sponsors, 0, $limit);
            }
        }
        break;

    case 'manual':
        if (!empty($selectedSponsors)) {
            foreach ($selectedSponsors as $sponsorId) {
                $sponsor = $sponsorModel->find((int)$sponsorId);
                if ($sponsor && $sponsor['active']) {
                    $sponsors[] = $sponsor;
                }
            }
        }
        break;

    default: // 'all'
        $sponsors = $sponsorModel->getActive();
        if ($limit > 0) {
            $sponsors = array_slice($sponsors, 0, $limit);
        }
        break;
}

// Filter only sponsors with logos
$sponsors = array_filter($sponsors, fn($s) => !empty($s['logo_url']));

if (empty($sponsors)) {
    return;
}

// Build custom styles
$colorStyles = [];
if (!empty($settings['title_color'])) {
    $colorStyles[] = "--block-title-color: {$settings['title_color']}";
}
if (!empty($settings['subtitle_color'])) {
    $colorStyles[] = "--block-subtitle-color: {$settings['subtitle_color']}";
}
$customStyles = !empty($colorStyles) ? implode('; ', $colorStyles) . '; ' : '';
$customStyles .= "--logo-height: {$logoHeight}px; ";
$customStyles .= "--grid-columns: {$columns}; ";

$blockId = 'sponsors-' . uniqid();
?>

<section class="block block-sponsors section <?= $renderer->getBlockClasses($block, $settings) ?? '' ?>"
         id="<?= $blockId ?>"
         style="<?= $customStyles ?>">
    <div class="container">
        <?php if (!empty($content['title']) || !empty($content['subtitle'])): ?>
            <div class="section-header text-center" data-aos="fade-up">
                <?php if (!empty($content['title'])): ?>
                    <h2 class="section-title"><?= htmlspecialchars($content['title']) ?></h2>
                <?php endif; ?>
                <?php if (!empty($content['subtitle'])): ?>
                    <p class="section-subtitle"><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="sponsors-container sponsors-<?= $displayMode ?> <?= $grayscale ? 'sponsors-grayscale' : '' ?>" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($sponsors as $index => $sponsor): ?>
                <div class="sponsor-item" data-aos="fade-up" data-aos-delay="<?= 50 + ($index * 30) ?>">
                    <a href="<?= htmlspecialchars($sponsor['website'] ?? '#') ?>" target="_blank" rel="noopener" class="sponsor-link">
                        <div class="sponsor-logo">
                            <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>"
                                 alt="<?= htmlspecialchars($sponsor['name']) ?>"
                                 loading="lazy">
                        </div>
                        <?php if ($showName || $showLevel): ?>
                            <div class="sponsor-info">
                                <?php if ($showName): ?>
                                    <span class="sponsor-name"><?= htmlspecialchars($sponsor['name']) ?></span>
                                <?php endif; ?>
                                <?php if ($showLevel && !empty($sponsor['level'])): ?>
                                    <span class="sponsor-level"><?= htmlspecialchars(ucfirst($sponsor['level'])) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
#<?= $blockId ?> .sponsors-grid {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns, 4), 1fr);
    gap: 2rem;
}

#<?= $blockId ?> .sponsor-item {
    text-align: center;
}

#<?= $blockId ?> .sponsor-link {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease;
}

#<?= $blockId ?> .sponsor-link:hover {
    transform: translateY(-5px);
}

#<?= $blockId ?> .sponsor-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    height: var(--logo-height, 80px);
    margin-bottom: 0.5rem;
}

#<?= $blockId ?> .sponsor-logo img {
    max-height: 100%;
    max-width: 100%;
    width: auto;
    object-fit: contain;
    transition: filter 0.3s ease;
}

#<?= $blockId ?> .sponsors-grayscale .sponsor-logo img {
    filter: grayscale(100%);
    opacity: 0.7;
}

#<?= $blockId ?> .sponsors-grayscale .sponsor-link:hover .sponsor-logo img {
    filter: grayscale(0);
    opacity: 1;
}

#<?= $blockId ?> .sponsor-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

#<?= $blockId ?> .sponsor-name {
    font-weight: 500;
    font-size: 0.9rem;
}

#<?= $blockId ?> .sponsor-level {
    font-size: 0.75rem;
    color: var(--color-gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

@media (max-width: 992px) {
    #<?= $blockId ?> .sponsors-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> .sponsors-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
