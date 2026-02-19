<?php
/**
 * Activities Block Template
 * Displays activities with filters by type and event
 * TLOS - The Last of SaaS
 */

use App\Models\Activity;
use App\Models\Event;
use App\Helpers\TranslationHelper;

$t = TranslationHelper::getInstance();

// Get settings
$limit = (int)($settings['limit'] ?? 6);
$columns = (int)($settings['columns'] ?? 3);
$cardStyle = $settings['card_style'] ?? 'full'; // full, compact, minimal
$showImage = $settings['show_image'] ?? true;
$showDate = $settings['show_date'] ?? true;
$showTime = $settings['show_time'] ?? true;
$showType = $settings['show_type'] ?? true;
$showSpeaker = $settings['show_speaker'] ?? true;
$showEvent = !empty($settings['show_event']);
$showRoom = !empty($settings['show_room']);

// Get filters from content
$eventId = !empty($content['event_id']) ? (int)$content['event_id'] : null;
$activityTypes = $content['activity_types'] ?? []; // array of types to filter
if (is_string($activityTypes)) {
    $activityTypes = array_filter(array_map('trim', explode(',', $activityTypes)));
}

// Get activities from database
$activityModel = new Activity();

if ($eventId && !empty($activityTypes)) {
    // Filter by event and types
    $activities = $activityModel->getByEventAndTypes($eventId, $activityTypes);
} elseif ($eventId) {
    // Filter by event only
    $activities = $activityModel->getByEvent($eventId);
} elseif (!empty($activityTypes)) {
    // Filter by types only - need to get from all active events
    $eventModel = new Event();
    $events = $eventModel->getActive();
    $activities = [];
    foreach ($events as $event) {
        $eventActivities = $activityModel->getByEventAndTypes($event['id'], $activityTypes);
        $activities = array_merge($activities, $eventActivities);
    }
    // Sort by date
    usort($activities, function($a, $b) {
        $dateCompare = strcmp($a['activity_date'], $b['activity_date']);
        if ($dateCompare !== 0) return $dateCompare;
        return strcmp($a['start_time'], $b['start_time']);
    });
} else {
    // Get recent activities from active events
    $eventModel = new Event();
    $events = $eventModel->getActive();
    $activities = [];
    foreach ($events as $event) {
        $eventActivities = $activityModel->getByEvent($event['id']);
        $activities = array_merge($activities, $eventActivities);
    }
    // Sort by date
    usort($activities, function($a, $b) {
        $dateCompare = strcmp($a['activity_date'], $b['activity_date']);
        if ($dateCompare !== 0) return $dateCompare;
        return strcmp($a['start_time'], $b['start_time']);
    });
}

// Apply limit
$activities = array_slice($activities, 0, $limit);

// Activity type labels
$activityTypeLabels = [
    'charla' => $t->text('activity_type_charla'),
    'mesa_redonda' => $t->text('activity_type_mesa_redonda'),
    'taller' => $t->text('activity_type_taller'),
    'reuniones_1to1' => $t->text('activity_type_reuniones_1to1'),
    'networking' => $t->text('activity_type_networking'),
    'comida' => $t->text('activity_type_comida'),
    'cafe' => $t->text('activity_type_cafe'),
    'bienvenida' => $t->text('activity_type_bienvenida'),
    'cierre' => $t->text('activity_type_cierre'),
    'registro' => $t->text('activity_type_registro'),
    'otro' => $t->text('activity_type_otro'),
];
?>

<section class="block block-activities section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <?php if (!empty($content['title'])): ?>
            <div class="section-header">
                <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($activities)): ?>
            <div class="activities-grid columns-<?= $columns ?> style-<?= $cardStyle ?>">
                <?php foreach ($activities as $activity): ?>
                    <?php
                    $isPast = strtotime($activity['activity_date']) < strtotime('today');
                    $startTime = substr($activity['start_time'] ?? '00:00', 0, 5);
                    $endTime = substr($activity['end_time'] ?? '00:00', 0, 5);
                    $hasValidTime = $startTime !== '00:00';
                    $typeLabel = $activityTypeLabels[$activity['activity_type']] ?? $activity['activity_type'];
                    $activityDate = new DateTime($activity['activity_date']);
                    ?>
                    <a href="/actividades/<?= htmlspecialchars($activity['slug']) ?>" class="activity-card <?= $isPast ? 'past-activity' : '' ?>">
                        <?php if ($showImage && !empty($activity['image_url'])): ?>
                            <div class="activity-image">
                                <img src="<?= htmlspecialchars($activity['image_url']) ?>"
                                     alt="<?= htmlspecialchars($activity['title']) ?>"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="activity-content">
                            <?php if ($showDate && $cardStyle !== 'minimal'): ?>
                                <div class="activity-date-badge">
                                    <span class="day"><?= $activityDate->format('d') ?></span>
                                    <span class="month"><?= strtoupper($activityDate->format('M')) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="activity-info">
                                <?php if ($showType): ?>
                                    <span class="activity-type" style="<?= !empty($activity['category_color']) ? 'background-color:' . $activity['category_color'] : '' ?>">
                                        <?= _e($activity['category_name'] ?? $typeLabel) ?>
                                    </span>
                                <?php endif; ?>

                                <h3><?= htmlspecialchars($activity['title']) ?></h3>

                                <div class="activity-meta">
                                    <?php if ($showDate && $cardStyle === 'minimal'): ?>
                                        <span><i class="fas fa-calendar-alt"></i> <?= $activityDate->format('d M Y') ?></span>
                                    <?php endif; ?>

                                    <?php if ($showTime && $hasValidTime): ?>
                                        <span><i class="fas fa-clock"></i> <?= $startTime ?><?= $cardStyle === 'full' ? ' - ' . $endTime : '' ?></span>
                                    <?php endif; ?>

                                    <?php if ($showRoom && !empty($activity['room_name'])): ?>
                                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($activity['room_name']) ?></span>
                                    <?php endif; ?>

                                    <?php if ($showSpeaker && !empty($activity['speaker_name'])): ?>
                                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($activity['speaker_name']) ?></span>
                                    <?php endif; ?>

                                    <?php if ($showEvent && !empty($activity['event_name'])): ?>
                                        <span><i class="fas fa-calendar"></i> <?= htmlspecialchars($activity['event_name']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($cardStyle === 'full' && !empty($activity['description'])): ?>
                                    <p class="activity-excerpt"><?= htmlspecialchars(substr(strip_tags($activity['description']), 0, 100)) ?>...</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($content['show_more']) && !empty($content['more_url'])): ?>
                <div class="section-footer">
                    <a href="<?= htmlspecialchars($content['more_url']) ?>" class="btn btn-outline">
                        <?= htmlspecialchars($content['more_text'] ?? $t->text('view_all_activities')) ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p><?= $t->text('no_activities_available') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Activities Block Styles */
.block-activities .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-activities .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
}

.block-activities .section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

.block-activities .activities-grid {
    display: grid;
    gap: var(--spacing-xl);
}

.block-activities .activities-grid.columns-2 {
    grid-template-columns: repeat(2, 1fr);
}

.block-activities .activities-grid.columns-3 {
    grid-template-columns: repeat(3, 1fr);
}

.block-activities .activities-grid.columns-4 {
    grid-template-columns: repeat(4, 1fr);
}

/* Activity Card */
.block-activities .activity-card {
    display: block;
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    text-decoration: none;
    color: inherit;
}

.block-activities .activity-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-4px);
}

.block-activities .activity-card.past-activity {
    opacity: 0.8;
}

/* Image */
.block-activities .activity-image {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.block-activities .activity-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}

.block-activities .activity-card:hover .activity-image img {
    transform: scale(1.05);
}

/* Content */
.block-activities .activity-content {
    padding: var(--spacing-lg);
    display: flex;
    gap: var(--spacing-md);
}

.block-activities .activity-date-badge {
    flex-shrink: 0;
    background: var(--color-dark);
    color: white;
    padding: var(--spacing-sm) var(--spacing-md);
    text-align: center;
    border-radius: var(--radius-md);
    min-width: 55px;
}

.block-activities .activity-date-badge .day {
    display: block;
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
}

.block-activities .activity-date-badge .month {
    display: block;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 0.25rem;
}

.block-activities .activity-info {
    flex: 1;
    min-width: 0;
}

.block-activities .activity-type {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.2rem 0.6rem;
    background: var(--color-dark);
    color: white;
    border-radius: var(--radius-sm);
    margin-bottom: var(--spacing-xs);
}

.block-activities .activity-info h3 {
    font-size: var(--font-size-lg);
    font-weight: 700;
    color: var(--color-dark);
    line-height: 1.3;
    margin-bottom: var(--spacing-sm);
}

.block-activities .activity-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.block-activities .activity-meta i {
    margin-right: 0.25rem;
}

.block-activities .activity-excerpt {
    margin-top: var(--spacing-sm);
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    line-height: 1.6;
}

/* Compact style */
.block-activities .activities-grid.style-compact .activity-content {
    padding: var(--spacing-md);
}

.block-activities .activities-grid.style-compact .activity-date-badge {
    padding: var(--spacing-xs) var(--spacing-sm);
    min-width: 45px;
}

.block-activities .activities-grid.style-compact .activity-date-badge .day {
    font-size: 20px;
}

.block-activities .activities-grid.style-compact .activity-info h3 {
    font-size: var(--font-size-md);
}

/* Minimal style */
.block-activities .activities-grid.style-minimal .activity-card {
    border: 1px solid var(--color-gray-200);
    box-shadow: none;
}

.block-activities .activities-grid.style-minimal .activity-content {
    flex-direction: column;
    padding: var(--spacing-md);
}

.block-activities .activities-grid.style-minimal .activity-date-badge {
    display: none;
}

.block-activities .activities-grid.style-minimal .activity-info h3 {
    font-size: var(--font-size-md);
    margin-bottom: var(--spacing-xs);
}

.block-activities .activities-grid.style-minimal .activity-meta {
    font-size: 12px;
}

/* Footer */
.block-activities .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-activities .section-footer .btn i {
    margin-left: var(--spacing-sm);
}

.block-activities .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* Dark background variant */
.block-activities.bg-dark .section-header h2,
.block-activities.bg-dark .section-header p {
    color: white;
}

.block-activities.bg-dark .activity-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.block-activities.bg-dark .activity-card:hover {
    background: rgba(255, 255, 255, 0.1);
}

.block-activities.bg-dark .activity-info h3 {
    color: white;
}

.block-activities.bg-dark .activity-meta {
    color: rgba(255, 255, 255, 0.6);
}

.block-activities.bg-dark .activity-excerpt {
    color: rgba(255, 255, 255, 0.7);
}

/* Responsive */
@media (max-width: 1024px) {
    .block-activities .activities-grid.columns-4,
    .block-activities .activities-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .block-activities .activities-grid.columns-4,
    .block-activities .activities-grid.columns-3,
    .block-activities .activities-grid.columns-2 {
        grid-template-columns: 1fr;
    }

    .block-activities .activity-content {
        flex-direction: column;
    }

    .block-activities .activity-date-badge {
        align-self: flex-start;
    }
}
</style>
