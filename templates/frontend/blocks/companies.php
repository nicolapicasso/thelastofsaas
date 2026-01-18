<?php
/**
 * Companies Block Template
 * TLOS - The Last of SaaS
 */

use App\Models\Company;
use App\Models\Event;

$companyModel = new Company();
$eventModel = new Event();

// Get settings
$displayMode = $settings['display_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 4);
$visibleItems = (int)($settings['visible_items'] ?? 5);
$selectionMode = $settings['selection_mode'] ?? 'all';
$eventId = $settings['event_id'] ?? '';
$sectorFilter = $settings['sector_filter'] ?? '';
$selectedCompanies = $settings['selected_companies'] ?? [];
$limit = (int)($settings['limit'] ?? 12);
$logoHeight = (int)($settings['logo_height'] ?? 80);
$showName = $settings['show_name'] ?? true;
$showSector = $settings['show_sector'] ?? true;
$grayscale = !empty($settings['grayscale']);
$autoplay = !empty($settings['autoplay']);
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Handle selected_companies as JSON string
if (is_string($selectedCompanies)) {
    $selectedCompanies = json_decode($selectedCompanies, true) ?? [];
}

// Get companies based on selection mode
$companies = [];
switch ($selectionMode) {
    case 'event':
        if ($eventId) {
            $companies = $eventModel->getCompanies((int)$eventId);
            if ($limit > 0) {
                $companies = array_slice($companies, 0, $limit);
            }
        }
        break;

    case 'sector':
        if ($sectorFilter) {
            $allCompanies = $companyModel->getActive();
            $companies = array_filter($allCompanies, fn($c) => ($c['sector'] ?? '') === $sectorFilter);
            if ($limit > 0) {
                $companies = array_slice($companies, 0, $limit);
            }
        }
        break;

    case 'manual':
        if (!empty($selectedCompanies)) {
            foreach ($selectedCompanies as $companyId) {
                $company = $companyModel->find((int)$companyId);
                if ($company && $company['active']) {
                    $companies[] = $company;
                }
            }
        }
        break;

    default: // 'all'
        $companies = $companyModel->getActive();
        if ($limit > 0) {
            $companies = array_slice($companies, 0, $limit);
        }
        break;
}

// Filter only companies with logos
$companies = array_filter($companies, fn($c) => !empty($c['logo_url']));

if (empty($companies)) {
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

$blockId = 'companies-' . uniqid();
?>

<section class="block block-companies section <?= $renderer->getBlockClasses($block, $settings) ?? '' ?>"
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

        <div class="companies-container companies-<?= $displayMode ?> <?= $grayscale ? 'companies-grayscale' : '' ?>" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($companies as $index => $company): ?>
                <div class="company-item" data-aos="fade-up" data-aos-delay="<?= 50 + ($index * 30) ?>">
                    <div class="company-card">
                        <div class="company-logo">
                            <img src="<?= htmlspecialchars($company['logo_url']) ?>"
                                 alt="<?= htmlspecialchars($company['name']) ?>"
                                 loading="lazy">
                        </div>
                        <?php if ($showName || $showSector): ?>
                            <div class="company-info">
                                <?php if ($showName): ?>
                                    <span class="company-name"><?= htmlspecialchars($company['name']) ?></span>
                                <?php endif; ?>
                                <?php if ($showSector && !empty($company['sector'])): ?>
                                    <span class="company-sector"><?= htmlspecialchars($company['sector']) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
#<?= $blockId ?> .companies-grid {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns, 4), 1fr);
    gap: 2rem;
}

#<?= $blockId ?> .company-item {
    text-align: center;
}

#<?= $blockId ?> .company-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

#<?= $blockId ?> .company-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

#<?= $blockId ?> .company-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    height: var(--logo-height, 80px);
    margin-bottom: 0.75rem;
}

#<?= $blockId ?> .company-logo img {
    max-height: 100%;
    max-width: 100%;
    width: auto;
    object-fit: contain;
    transition: filter 0.3s ease;
}

#<?= $blockId ?> .companies-grayscale .company-logo img {
    filter: grayscale(100%);
    opacity: 0.7;
}

#<?= $blockId ?> .companies-grayscale .company-card:hover .company-logo img {
    filter: grayscale(0);
    opacity: 1;
}

#<?= $blockId ?> .company-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

#<?= $blockId ?> .company-name {
    font-weight: 600;
    font-size: 0.95rem;
}

#<?= $blockId ?> .company-sector {
    font-size: 0.8rem;
    color: var(--color-gray-500);
}

@media (max-width: 992px) {
    #<?= $blockId ?> .companies-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> .companies-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
