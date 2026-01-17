<?php
/**
 * Image Picker Partial
 * Usage: <?php include TEMPLATES_PATH . '/admin/partials/image-picker.php'; renderImagePicker($name, $value, $label, $hint, $dataAttr); ?>
 *
 * @param string $name - Field name
 * @param string $value - Current image URL
 * @param string $label - Label text
 * @param string $hint - Optional hint text
 * @param string $dataAttr - Optional data attribute name (for block forms)
 */

function renderImagePicker(string $name, ?string $value = '', string $label = 'Imagen', string $hint = '', string $dataAttr = ''): void
{
    $hasImage = !empty($value);
    $attribute = $dataAttr ? "data-{$dataAttr}=\"{$name}\"" : "name=\"{$name}\"";
    $inputType = $dataAttr ? 'text' : 'hidden';
    ?>
    <div class="form-group">
        <label><?= htmlspecialchars($label) ?></label>
        <div class="image-picker-field">
            <input type="<?= $inputType ?>" <?= $attribute ?> value="<?= htmlspecialchars($value ?? '') ?>">
            <div class="image-picker-preview <?= $hasImage ? 'has-image' : '' ?>">
                <?php if ($hasImage): ?>
                    <img src="<?= htmlspecialchars($value) ?>" alt="Preview">
                <?php else: ?>
                    <div class="preview-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Sin imagen</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="image-picker-actions">
                <button type="button" class="btn btn-sm btn-outline image-picker-select">
                    <i class="fas fa-upload"></i> <?= $hasImage ? 'Cambiar' : 'Seleccionar' ?>
                </button>
                <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasImage ? 'flex' : 'none' ?>;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <?php if ($hint): ?>
                <small class="form-hint"><?= htmlspecialchars($hint) ?></small>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Render image picker for block forms with data-content attribute
 */
function renderBlockImagePicker(string $contentKey, ?string $value = '', string $label = 'Imagen', string $hint = ''): void
{
    renderImagePicker($contentKey, $value, $label, $hint, 'content');
}

/**
 * Render image picker for block forms with data-setting attribute
 */
function renderBlockSettingImagePicker(string $settingKey, ?string $value = '', string $label = 'Imagen', string $hint = ''): void
{
    renderImagePicker($settingKey, $value, $label, $hint, 'setting');
}

/**
 * Render image picker for slide fields
 */
function renderSlideImagePicker(string $fieldKey, ?string $value = '', string $label = 'Imagen', string $hint = ''): void
{
    renderImagePicker($fieldKey, $value, $label, $hint, 'slide-field');
}
