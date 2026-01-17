<?php
/**
 * Pricing Block Admin Form
 * Editable pricing plans grid
 * Omniwallet CMS
 */

$plans = $content['plans'] ?? [];
?>

<div class="block-form">
    <div class="form-section">
        <h4>Encabezado de la sección</h4>
        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Planes y Precios') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Descripción opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Planes de Precios</h4>
        <p class="form-help">Configura cada plan con su precio, descripción y características.</p>

        <div class="pricing-plans-container" id="pricingPlansContainer">
            <?php foreach ($plans as $index => $plan): ?>
                <div class="pricing-plan-card" data-plan-index="<?= $index ?>">
                    <div class="plan-card-header">
                        <span class="plan-number">Plan <?= $index + 1 ?></span>
                        <div class="plan-card-actions">
                            <button type="button" class="btn btn-sm btn-outline move-plan-up" title="Mover arriba">↑</button>
                            <button type="button" class="btn btn-sm btn-outline move-plan-down" title="Mover abajo">↓</button>
                            <button type="button" class="btn btn-sm btn-danger remove-plan-btn" title="Eliminar">&times;</button>
                        </div>
                    </div>

                    <div class="plan-card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombre del plan</label>
                                <input type="text" data-plan-field="name" value="<?= htmlspecialchars($plan['name'] ?? '') ?>" placeholder="Ej: Starter">
                            </div>
                            <div class="form-group">
                                <label>Descripción corta</label>
                                <input type="text" data-plan-field="description" value="<?= htmlspecialchars($plan['description'] ?? '') ?>" placeholder="Ej: Ideal para pequeños negocios">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Precio</label>
                                <input type="text" data-plan-field="price" value="<?= htmlspecialchars($plan['price'] ?? '') ?>" placeholder="39 o Personalizado">
                            </div>
                            <div class="form-group">
                                <label>Sufijo precio</label>
                                <input type="text" data-plan-field="price_suffix" value="<?= htmlspecialchars($plan['price_suffix'] ?? '/mes') ?>" placeholder="/mes">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Icono</label>
                                <div class="icon-input-wrapper">
                                    <div class="icon-input-preview">
                                        <i class="<?= htmlspecialchars($plan['icon'] ?? 'fas fa-star') ?>"></i>
                                    </div>
                                    <input type="text" data-plan-field="icon" value="<?= htmlspecialchars($plan['icon'] ?? '') ?>" placeholder="fas fa-star">
                                    <button type="button" class="icon-input-btn">Elegir</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Texto del botón</label>
                                <input type="text" data-plan-field="cta_text" value="<?= htmlspecialchars($plan['cta_text'] ?? 'Empieza gratis') ?>" placeholder="Empieza gratis">
                            </div>
                            <div class="form-group">
                                <label>URL del botón</label>
                                <input type="text" data-plan-field="cta_url" value="<?= htmlspecialchars($plan['cta_url'] ?? '') ?>" placeholder="/registro?plan=starter">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" data-plan-field="highlighted" <?= !empty($plan['highlighted']) ? 'checked' : '' ?>>
                                    <span>Destacar plan (Más popular)</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" data-plan-field="is_enterprise" <?= !empty($plan['is_enterprise']) ? 'checked' : '' ?>>
                                    <span>Plan Enterprise (fondo oscuro)</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Características</label>
                            <div class="features-list" data-plan-features>
                                <?php
                                $features = $plan['features'] ?? [];
                                foreach ($features as $featureIndex => $feature):
                                ?>
                                    <div class="feature-item">
                                        <input type="text" value="<?= htmlspecialchars($feature) ?>" placeholder="Característica...">
                                        <button type="button" class="btn btn-sm btn-danger remove-feature-btn">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline add-feature-btn">+ Añadir característica</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-outline add-plan-btn" id="addPlanBtn">
            <i class="fas fa-plus"></i> Añadir plan
        </button>

        <!-- Hidden input to store plans JSON -->
        <input type="hidden" data-content="plans" id="plans-data-input" value="<?= htmlspecialchars(json_encode($plans)) ?>">
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/animation-settings.php'; ?>
</div>

<style>
.pricing-plans-container {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.pricing-plan-card {
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.plan-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--color-gray-100);
    border-bottom: 1px solid var(--color-gray-200);
}

.plan-number {
    font-weight: 600;
    color: var(--color-gray-700);
}

.plan-card-actions {
    display: flex;
    gap: var(--spacing-xs);
}

.plan-card-body {
    padding: var(--spacing-md);
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-sm);
}

.feature-item {
    display: flex;
    gap: var(--spacing-xs);
}

.feature-item input {
    flex: 1;
}

.add-plan-btn {
    width: 100%;
}

/* Icon input wrapper */
.icon-input-wrapper {
    display: flex;
    gap: var(--spacing-xs);
    align-items: center;
}

.icon-input-preview {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    font-size: 18px;
    color: var(--color-primary);
}

.icon-input-wrapper input {
    flex: 1;
}
</style>

<script>
(function() {
    const container = document.getElementById('pricingPlansContainer');
    const addPlanBtn = document.getElementById('addPlanBtn');
    const plansInput = document.getElementById('plans-data-input');

    // Collect all plans data and update hidden input
    function updatePlansData() {
        const plans = [];
        const planCards = container.querySelectorAll('.pricing-plan-card');

        planCards.forEach((card, index) => {
            const plan = {
                name: card.querySelector('[data-plan-field="name"]').value,
                description: card.querySelector('[data-plan-field="description"]').value,
                price: card.querySelector('[data-plan-field="price"]').value,
                price_suffix: card.querySelector('[data-plan-field="price_suffix"]').value,
                icon: card.querySelector('[data-plan-field="icon"]').value,
                cta_text: card.querySelector('[data-plan-field="cta_text"]').value,
                cta_url: card.querySelector('[data-plan-field="cta_url"]').value,
                highlighted: card.querySelector('[data-plan-field="highlighted"]').checked,
                is_enterprise: card.querySelector('[data-plan-field="is_enterprise"]').checked,
                features: []
            };

            // Collect features
            const featureInputs = card.querySelectorAll('[data-plan-features] input');
            featureInputs.forEach(input => {
                if (input.value.trim()) {
                    plan.features.push(input.value.trim());
                }
            });

            plans.push(plan);
        });

        plansInput.value = JSON.stringify(plans);
    }

    // Create a new plan card HTML
    function createPlanCard(index, plan = {}) {
        const div = document.createElement('div');
        div.className = 'pricing-plan-card';
        div.setAttribute('data-plan-index', index);

        div.innerHTML = `
            <div class="plan-card-header">
                <span class="plan-number">Plan ${index + 1}</span>
                <div class="plan-card-actions">
                    <button type="button" class="btn btn-sm btn-outline move-plan-up" title="Mover arriba">↑</button>
                    <button type="button" class="btn btn-sm btn-outline move-plan-down" title="Mover abajo">↓</button>
                    <button type="button" class="btn btn-sm btn-danger remove-plan-btn" title="Eliminar">&times;</button>
                </div>
            </div>
            <div class="plan-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre del plan</label>
                        <input type="text" data-plan-field="name" value="${plan.name || ''}" placeholder="Ej: Starter">
                    </div>
                    <div class="form-group">
                        <label>Descripción corta</label>
                        <input type="text" data-plan-field="description" value="${plan.description || ''}" placeholder="Ej: Ideal para pequeños negocios">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="text" data-plan-field="price" value="${plan.price || ''}" placeholder="39 o Personalizado">
                    </div>
                    <div class="form-group">
                        <label>Sufijo precio</label>
                        <input type="text" data-plan-field="price_suffix" value="${plan.price_suffix || '/mes'}" placeholder="/mes">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Icono</label>
                        <div class="icon-input-wrapper">
                            <div class="icon-input-preview">
                                <i class="${plan.icon || 'fas fa-star'}"></i>
                            </div>
                            <input type="text" data-plan-field="icon" value="${plan.icon || ''}" placeholder="fas fa-star">
                            <button type="button" class="icon-input-btn">Elegir</button>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Texto del botón</label>
                        <input type="text" data-plan-field="cta_text" value="${plan.cta_text || 'Empieza gratis'}" placeholder="Empieza gratis">
                    </div>
                    <div class="form-group">
                        <label>URL del botón</label>
                        <input type="text" data-plan-field="cta_url" value="${plan.cta_url || ''}" placeholder="/registro?plan=starter">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" data-plan-field="highlighted" ${plan.highlighted ? 'checked' : ''}>
                            <span>Destacar plan (Más popular)</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" data-plan-field="is_enterprise" ${plan.is_enterprise ? 'checked' : ''}>
                            <span>Plan Enterprise (fondo oscuro)</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Características</label>
                    <div class="features-list" data-plan-features></div>
                    <button type="button" class="btn btn-sm btn-outline add-feature-btn">+ Añadir característica</button>
                </div>
            
    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
        `;

        return div;
    }

    // Create feature item HTML
    function createFeatureItem(value = '') {
        const div = document.createElement('div');
        div.className = 'feature-item';
        div.innerHTML = `
            <input type="text" value="${value}" placeholder="Característica...">
            <button type="button" class="btn btn-sm btn-danger remove-feature-btn">&times;</button>
        `;
        return div;
    }

    // Add new plan
    addPlanBtn.addEventListener('click', function() {
        const index = container.querySelectorAll('.pricing-plan-card').length;
        const newCard = createPlanCard(index);
        container.appendChild(newCard);
        updatePlansData();
    });

    // Event delegation for container
    container.addEventListener('click', function(e) {
        const target = e.target;

        // Remove plan
        if (target.classList.contains('remove-plan-btn')) {
            const card = target.closest('.pricing-plan-card');
            if (confirm('¿Eliminar este plan?')) {
                card.remove();
                updatePlanNumbers();
                updatePlansData();
            }
        }

        // Move plan up
        if (target.classList.contains('move-plan-up')) {
            const card = target.closest('.pricing-plan-card');
            const prev = card.previousElementSibling;
            if (prev) {
                container.insertBefore(card, prev);
                updatePlanNumbers();
                updatePlansData();
            }
        }

        // Move plan down
        if (target.classList.contains('move-plan-down')) {
            const card = target.closest('.pricing-plan-card');
            const next = card.nextElementSibling;
            if (next) {
                container.insertBefore(next, card);
                updatePlanNumbers();
                updatePlansData();
            }
        }

        // Add feature
        if (target.classList.contains('add-feature-btn')) {
            const featuresList = target.previousElementSibling;
            featuresList.appendChild(createFeatureItem());
            updatePlansData();
        }

        // Remove feature
        if (target.classList.contains('remove-feature-btn')) {
            target.closest('.feature-item').remove();
            updatePlansData();
        }
    });

    // Update on input change
    container.addEventListener('input', function(e) {
        if (e.target.tagName === 'INPUT') {
            // Update icon preview
            if (e.target.getAttribute('data-plan-field') === 'icon') {
                const preview = e.target.closest('.icon-input-wrapper').querySelector('.icon-input-preview i');
                preview.className = e.target.value || 'fas fa-star';
            }
            updatePlansData();
        }
    });

    // Update on checkbox change
    container.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            updatePlansData();
        }
    });

    // Update plan numbers
    function updatePlanNumbers() {
        const cards = container.querySelectorAll('.pricing-plan-card');
        cards.forEach((card, index) => {
            card.setAttribute('data-plan-index', index);
            card.querySelector('.plan-number').textContent = `Plan ${index + 1}`;
        });
    }
})();
</script>
