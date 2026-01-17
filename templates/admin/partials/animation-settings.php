<?php
/**
 * Animation Settings Partial
 * Reusable animation settings for block forms
 * Omniwallet CMS
 */
$animationTypes = [
    '' => 'Sin animación',
    'fade-in' => 'Aparecer',
    'fade-up' => 'Aparecer desde abajo',
    'fade-down' => 'Aparecer desde arriba',
    'fade-left' => 'Aparecer desde derecha',
    'fade-right' => 'Aparecer desde izquierda',
    'zoom-in' => 'Zoom entrada',
    'zoom-out' => 'Zoom salida',
    'flip-left' => 'Voltear izquierda',
    'flip-right' => 'Voltear derecha',
    'flip-up' => 'Voltear arriba',
    'flip-down' => 'Voltear abajo',
    'slide-up' => 'Deslizar arriba',
    'slide-down' => 'Deslizar abajo',
    'slide-left' => 'Deslizar izquierda',
    'slide-right' => 'Deslizar derecha',
    'bounce-in' => 'Rebote',
    'rotate-in' => 'Rotar',
    'elastic' => 'Elástico',
];

$animationDurations = [
    'fast' => 'Rápida (0.3s)',
    'normal' => 'Normal (0.6s)',
    'slow' => 'Lenta (1s)',
    'slower' => 'Muy lenta (1.5s)',
];

$currentAnimation = $settings['animation'] ?? '';
$currentDuration = $settings['animation_duration'] ?? 'normal';
$currentDelay = $settings['animation_delay'] ?? '0';
$staggerChildren = $settings['animation_stagger'] ?? false;
?>

<div class="form-section animation-settings">
    <h4>Animación de entrada</h4>
    <p class="form-help">Configura la animación cuando el bloque aparece al hacer scroll.</p>

    <div class="form-row">
        <div class="form-group">
            <label>Tipo de animación</label>
            <select data-setting="animation">
                <?php foreach ($animationTypes as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $currentAnimation === $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Duración</label>
            <select data-setting="animation_duration">
                <?php foreach ($animationDurations as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $currentDuration === $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Retraso (ms)</label>
            <select data-setting="animation_delay">
                <option value="0" <?= $currentDelay === '0' ? 'selected' : '' ?>>Sin retraso</option>
                <option value="100" <?= $currentDelay === '100' ? 'selected' : '' ?>>100ms</option>
                <option value="200" <?= $currentDelay === '200' ? 'selected' : '' ?>>200ms</option>
                <option value="300" <?= $currentDelay === '300' ? 'selected' : '' ?>>300ms</option>
                <option value="500" <?= $currentDelay === '500' ? 'selected' : '' ?>>500ms</option>
                <option value="700" <?= $currentDelay === '700' ? 'selected' : '' ?>>700ms</option>
                <option value="1000" <?= $currentDelay === '1000' ? 'selected' : '' ?>>1000ms</option>
            </select>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="animation_stagger" <?= $staggerChildren ? 'checked' : '' ?>>
                <span>Escalonar elementos hijos</span>
            </label>
            <p class="form-help" style="font-size: 11px; margin-top: 4px;">Cada elemento hijo aparece con un pequeño retraso</p>
        </div>
    </div>
</div>
