<?php
/**
 * Agenda Block Template
 * TLOS - The Last of SaaS
 */

use App\Models\Activity;
use App\Models\Event;

$activityModel = new Activity();
$eventModel = new Event();

// Get settings
$eventId = $settings['event_id'] ?? '';
$displayMode = $settings['display_mode'] ?? 'timeline';
$groupBy = $settings['group_by'] ?? 'date';
$showRoom = $settings['show_room'] ?? true;
$showSpeaker = $settings['show_speaker'] ?? true;
$showDescription = $settings['show_description'] ?? true;
$showTime = $settings['show_time'] ?? true;
$filterDate = $settings['filter_by_date'] ?? '';
$filterRoom = $settings['filter_by_room'] ?? '';
$filterType = $settings['filter_by_type'] ?? '';

if (!$eventId) {
    return;
}

// Get event
$event = $eventModel->find((int)$eventId);
if (!$event) {
    return;
}

// Get activities
$activities = $activityModel->getByEvent((int)$eventId);

// Apply filters
if ($filterDate) {
    $activities = array_filter($activities, fn($a) => $a['activity_date'] === $filterDate);
}
if ($filterRoom) {
    $activities = array_filter($activities, fn($a) => ($a['room_id'] ?? '') == $filterRoom);
}
if ($filterType) {
    $activities = array_filter($activities, fn($a) => ($a['activity_type'] ?? '') === $filterType);
}

if (empty($activities)) {
    return;
}

// Group activities
$grouped = [];
foreach ($activities as $activity) {
    switch ($groupBy) {
        case 'room':
            $key = $activity['room_name'] ?? 'Sin sala';
            break;
        case 'type':
            $key = Activity::getActivityTypes()[$activity['activity_type']] ?? $activity['activity_type'];
            break;
        default: // date
            $key = $activity['activity_date'];
            break;
    }
    if (!isset($grouped[$key])) {
        $grouped[$key] = [];
    }
    $grouped[$key][] = $activity;
}

// Build custom styles
$colorStyles = [];
if (!empty($settings['title_color'])) {
    $colorStyles[] = "--block-title-color: {$settings['title_color']}";
}
$customStyles = !empty($colorStyles) ? implode('; ', $colorStyles) . '; ' : '';

$blockId = 'agenda-' . uniqid();
$activityTypes = Activity::getActivityTypes();
?>

<section class="block block-agenda section <?= $renderer->getBlockClasses($block, $settings) ?? '' ?>"
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

        <div class="agenda-container agenda-<?= $displayMode ?>" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($grouped as $groupKey => $groupActivities): ?>
                <div class="agenda-group">
                    <div class="agenda-group-header">
                        <?php if ($groupBy === 'date'): ?>
                            <?php
                            $dateObj = new DateTime($groupKey);
                            $monthYearFormatter = new IntlDateFormatter('es_ES', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'MMMM yyyy');
                            $dayNameFormatter = new IntlDateFormatter('es_ES', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'EEEE');
                            ?>
                            <h3 class="agenda-date">
                                <span class="day"><?= $dateObj->format('d') ?></span>
                                <span class="month-year">
                                    <?= ucfirst($monthYearFormatter->format($dateObj)) ?>
                                    <small><?= ucfirst($dayNameFormatter->format($dateObj)) ?></small>
                                </span>
                            </h3>
                        <?php else: ?>
                            <h3 class="agenda-group-title"><?= htmlspecialchars($groupKey) ?></h3>
                        <?php endif; ?>
                    </div>

                    <div class="agenda-items">
                        <?php foreach ($groupActivities as $index => $activity): ?>
                            <div class="agenda-item" data-type="<?= htmlspecialchars($activity['activity_type']) ?>"
                                 style="--room-color: <?= htmlspecialchars($activity['room_color'] ?? '#6B7280') ?>">
                                <?php if ($showTime): ?>
                                    <div class="agenda-time">
                                        <span class="time-start"><?= date('H:i', strtotime($activity['start_time'])) ?></span>
                                        <span class="time-separator">-</span>
                                        <span class="time-end"><?= date('H:i', strtotime($activity['end_time'])) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="agenda-content">
                                    <div class="agenda-header">
                                        <span class="agenda-type"><?= $activityTypes[$activity['activity_type']] ?? $activity['activity_type'] ?></span>
                                        <?php if ($showRoom && !empty($activity['room_name'])): ?>
                                            <span class="agenda-room"><?= htmlspecialchars($activity['room_name']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <h4 class="agenda-title"><?= htmlspecialchars($activity['title']) ?></h4>

                                    <?php if ($showDescription && !empty($activity['description'])): ?>
                                        <p class="agenda-description"><?= htmlspecialchars($activity['description']) ?></p>
                                    <?php endif; ?>

                                    <?php if ($showSpeaker && !empty($activity['speaker_name'])): ?>
                                        <div class="agenda-speaker">
                                            <?php if (!empty($activity['speaker_photo'])): ?>
                                                <img src="<?= htmlspecialchars($activity['speaker_photo']) ?>" alt="" class="speaker-photo">
                                            <?php endif; ?>
                                            <div class="speaker-info">
                                                <span class="speaker-name"><?= htmlspecialchars($activity['speaker_name']) ?></span>
                                                <?php if (!empty($activity['speaker_position'])): ?>
                                                    <span class="speaker-position"><?= htmlspecialchars($activity['speaker_position']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
#<?= $blockId ?> .agenda-group {
    margin-bottom: 3rem;
}

#<?= $blockId ?> .agenda-date {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

#<?= $blockId ?> .agenda-date .day {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1;
    color: var(--color-primary);
}

#<?= $blockId ?> .agenda-date .month-year {
    display: flex;
    flex-direction: column;
    font-size: 1.1rem;
    text-transform: capitalize;
}

#<?= $blockId ?> .agenda-date .month-year small {
    font-size: 0.85rem;
    color: var(--color-gray-500);
}

#<?= $blockId ?> .agenda-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

#<?= $blockId ?> .agenda-item {
    display: flex;
    gap: 1.5rem;
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-left: 4px solid var(--room-color, var(--color-primary));
    transition: transform 0.2s ease;
}

#<?= $blockId ?> .agenda-item:hover {
    transform: translateX(5px);
}

#<?= $blockId ?> .agenda-time {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 60px;
    color: var(--color-gray-600);
}

#<?= $blockId ?> .agenda-time .time-start {
    font-weight: 600;
    font-size: 1.1rem;
}

#<?= $blockId ?> .agenda-time .time-separator {
    font-size: 0.8rem;
}

#<?= $blockId ?> .agenda-time .time-end {
    font-size: 0.9rem;
    color: var(--color-gray-400);
}

#<?= $blockId ?> .agenda-content {
    flex: 1;
}

#<?= $blockId ?> .agenda-header {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

#<?= $blockId ?> .agenda-type {
    font-size: 0.75rem;
    padding: 2px 8px;
    background: var(--color-gray-100);
    border-radius: 4px;
    text-transform: uppercase;
}

#<?= $blockId ?> .agenda-room {
    font-size: 0.75rem;
    padding: 2px 8px;
    background: var(--room-color, var(--color-gray-100));
    color: white;
    border-radius: 4px;
}

#<?= $blockId ?> .agenda-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
}

#<?= $blockId ?> .agenda-description {
    font-size: 0.9rem;
    color: var(--color-gray-600);
    margin: 0 0 0.75rem 0;
    line-height: 1.5;
}

#<?= $blockId ?> .agenda-speaker {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--color-gray-100);
}

#<?= $blockId ?> .speaker-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

#<?= $blockId ?> .speaker-info {
    display: flex;
    flex-direction: column;
}

#<?= $blockId ?> .speaker-name {
    font-weight: 500;
    font-size: 0.9rem;
}

#<?= $blockId ?> .speaker-position {
    font-size: 0.8rem;
    color: var(--color-gray-500);
}

@media (max-width: 768px) {
    #<?= $blockId ?> .agenda-item {
        flex-direction: column;
        gap: 1rem;
    }

    #<?= $blockId ?> .agenda-time {
        flex-direction: row;
        gap: 0.5rem;
        justify-content: flex-start;
    }
}
</style>
