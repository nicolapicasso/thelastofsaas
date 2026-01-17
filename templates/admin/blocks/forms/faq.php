<?php
// Load FAQ groups from database
use App\Models\FAQ;
$faqModel = new FAQ();
$faqGroups = $faqModel->getUniqueGroups();
?>
<div class="block-form">
    <div class="form-section">
        <h4>Configuración del bloque FAQ</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '¿Tienes preguntas?') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Filtros</h4>
        <div class="form-group">
            <label>Grupo de FAQs a mostrar</label>
            <select data-setting="faq_group">
                <option value="" <?= empty($settings['faq_group']) ? 'selected' : '' ?>>Todos los grupos</option>
                <?php foreach ($faqGroups as $group): ?>
                    <option value="<?= htmlspecialchars($group) ?>" <?= ($settings['faq_group'] ?? '') === $group ? 'selected' : '' ?>><?= htmlspecialchars($group) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Número máximo de FAQs</label>
            <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 10 ?>" min="1" max="50">
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Color de fondo</label>
                <input type="color" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#f8f9fa') ?>">
            </div>
            <div class="form-group">
                <label>Disposición</label>
                <select data-setting="layout">
                    <option value="accordion" <?= ($settings['layout'] ?? 'accordion') === 'accordion' ? 'selected' : '' ?>>Acordeón</option>
                    <option value="list" <?= ($settings['layout'] ?? '') === 'list' ? 'selected' : '' ?>>Lista expandida</option>
                </select>
            </div>
        </div>
    </div>

    <div class="info-box">
        <p><strong>Nota:</strong> Las FAQs se cargan automáticamente desde la base de datos. Gestiona las preguntas frecuentes desde <a href="/admin/faqs" target="_blank">Administrar FAQs</a>.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
