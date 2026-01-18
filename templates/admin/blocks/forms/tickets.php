<?php
/**
 * Tickets Block Admin Form
 * TLOS - The Last of SaaS
 */

use App\Models\Event;
use App\Models\Sponsor;
use App\Models\TicketType;

$eventModel = new Event();
$sponsorModel = new Sponsor();
$ticketTypeModel = new TicketType();

$events = $eventModel->getActive();
$sponsors = $sponsorModel->getActive();
?>

<div class="block-form">
    <div class="form-section">
        <h4>Configuracion del bloque Tickets</h4>
        <div class="form-group">
            <label>Titulo de la seccion</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Consigue tu entrada') ?>">
        </div>
        <div class="form-group">
            <label>Subtitulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtitulo opcional">
        </div>
        <div class="form-group">
            <label>Texto del boton</label>
            <input type="text" data-content="cta_text" value="<?= htmlspecialchars($content['cta_text'] ?? 'Comprar entrada') ?>">
        </div>
    </div>

    <div class="form-section">
        <h4>Evento y Ticket</h4>
        <div class="form-group">
            <label>Seleccionar evento *</label>
            <select data-setting="event_id" id="tickets-event-id" required>
                <option value="">-- Seleccionar evento --</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= ($settings['event_id'] ?? '') == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['name']) ?>
                        <?php if ($event['start_date'] ?? null): ?>
                            (<?= date('d/m/Y', strtotime($event['start_date'])) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="form-help">Obligatorio: selecciona el evento para el ticket</small>
        </div>

        <div class="form-group">
            <label>Sponsor que ofrece el ticket (opcional)</label>
            <select data-setting="sponsor_id">
                <option value="">Sin sponsor asociado</option>
                <?php foreach ($sponsors as $sponsor): ?>
                    <option value="<?= $sponsor['id'] ?>" <?= ($settings['sponsor_id'] ?? '') == $sponsor['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sponsor['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="form-help">Util para landings de sponsors que invitan empresas</small>
        </div>

        <div class="form-group">
            <label>Tipo de ticket especifico (opcional)</label>
            <select data-setting="ticket_type_id" id="tickets-type-select">
                <option value="">Mostrar ticket por defecto del evento</option>
                <!-- Los tipos de ticket se cargaran dinamicamente segun el evento seleccionado -->
            </select>
            <small class="form-help">Gestiona los tipos de ticket desde <a href="/admin/tickets/types" target="_blank">Tipos de Ticket</a></small>
        </div>
    </div>

    <div class="form-section">
        <h4>Modo de visualizacion</h4>
        <div class="form-group">
            <label>Estilo de presentacion</label>
            <select data-setting="display_mode">
                <option value="card" <?= ($settings['display_mode'] ?? 'card') === 'card' ? 'selected' : '' ?>>Tarjeta destacada</option>
                <option value="inline" <?= ($settings['display_mode'] ?? 'card') === 'inline' ? 'selected' : '' ?>>Inline (horizontal)</option>
                <option value="minimal" <?= ($settings['display_mode'] ?? 'card') === 'minimal' ? 'selected' : '' ?>>Minimalista</option>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h4>Elementos a mostrar</h4>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_price" <?= ($settings['show_price'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar precio</span>
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_remaining" <?= ($settings['show_remaining'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar disponibilidad</span>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_description" <?= ($settings['show_description'] ?? true) ? 'checked' : '' ?>>
                <span>Mostrar descripcion del ticket</span>
            </label>
        </div>
    </div>

    <div class="form-section">
        <h4>Personalizacion (opcional)</h4>
        <div class="form-group">
            <label>Precio personalizado</label>
            <input type="text" data-setting="custom_price" value="<?= htmlspecialchars($settings['custom_price'] ?? '') ?>" placeholder="Ej: Gratis, 50€, Desde 25€...">
            <small class="form-help">Sobreescribe el precio real del ticket (solo visual)</small>
        </div>
        <div class="form-group">
            <label>Disponibilidad personalizada</label>
            <input type="text" data-setting="custom_limit" value="<?= htmlspecialchars($settings['custom_limit'] ?? '') ?>" placeholder="Ej: Plazas limitadas, Ultimas 10 entradas...">
            <small class="form-help">Sobreescribe la disponibilidad real (solo visual)</small>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>
    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>

    <div class="info-box">
        <p><strong>Uso:</strong> Este bloque es ideal para landings de sponsors que quieran invitar empresas a su evento.</p>
        <p><strong>Nota:</strong> El enlace de compra redirigira automaticamente al formulario de registro del evento.</p>
    </div>
</div>
