<?php
/**
 * Text Color Settings Partial
 * Reusable text color configuration for blocks
 * Omniwallet CMS
 */
?>
<div class="form-section">
    <h4>Colores de texto</h4>
    <div class="form-row">
        <div class="form-group">
            <label>Color del título</label>
            <div class="color-picker-wrapper">
                <input type="color" data-setting="title_color" value="<?= htmlspecialchars($settings['title_color'] ?? '#1f2937') ?>">
                <input type="text" data-setting="title_color" value="<?= htmlspecialchars($settings['title_color'] ?? '#1f2937') ?>" placeholder="#1f2937" class="color-text-input">
            </div>
        </div>
        <div class="form-group">
            <label>Color del subtítulo</label>
            <div class="color-picker-wrapper">
                <input type="color" data-setting="subtitle_color" value="<?= htmlspecialchars($settings['subtitle_color'] ?? '#4b5563') ?>">
                <input type="text" data-setting="subtitle_color" value="<?= htmlspecialchars($settings['subtitle_color'] ?? '#4b5563') ?>" placeholder="#4b5563" class="color-text-input">
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Color del texto</label>
            <div class="color-picker-wrapper">
                <input type="color" data-setting="text_color" value="<?= htmlspecialchars($settings['text_color'] ?? '#6b7280') ?>">
                <input type="text" data-setting="text_color" value="<?= htmlspecialchars($settings['text_color'] ?? '#6b7280') ?>" placeholder="#6b7280" class="color-text-input">
            </div>
        </div>
        <div class="form-group">
            <label>Color de fondo</label>
            <div class="color-picker-wrapper">
                <input type="color" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#ffffff') ?>">
                <input type="text" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#ffffff') ?>" placeholder="#ffffff" class="color-text-input">
            </div>
        </div>
    </div>
</div>

<style>
.color-picker-wrapper,
.color-picker-inline {
    display: flex;
    align-items: center;
    gap: 8px;
}

.color-picker-wrapper input[type="color"],
.color-picker-inline input[type="color"] {
    width: 40px;
    height: 40px;
    padding: 2px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    cursor: pointer;
    background: white;
    flex-shrink: 0;
}

.color-picker-wrapper input[type="color"]::-webkit-color-swatch-wrapper,
.color-picker-inline input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 2px;
}

.color-picker-wrapper input[type="color"]::-webkit-color-swatch,
.color-picker-inline input[type="color"]::-webkit-color-swatch {
    border-radius: 4px;
    border: none;
}

.color-picker-wrapper .color-text-input,
.color-picker-inline input[type="text"] {
    flex: 1;
    font-family: monospace;
    font-size: 13px;
}
</style>

<script>
(function() {
    // Sync color picker with text input
    document.querySelectorAll('.color-picker-wrapper, .color-picker-inline').forEach(wrapper => {
        const colorInput = wrapper.querySelector('input[type="color"]');
        const textInput = wrapper.querySelector('input[type="text"]');

        if (colorInput && textInput) {
            // When color changes, update text
            colorInput.addEventListener('input', function() {
                textInput.value = this.value;
            });

            // When text changes, update color (if valid)
            textInput.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    colorInput.value = this.value;
                }
            });
        }
    });
})();
</script>
