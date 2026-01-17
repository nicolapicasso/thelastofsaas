<?php
/**
 * Voting Form Template
 * TLOS - The Last of SaaS
 */
$isEdit = isset($voting) && $voting;
$title = $isEdit ? 'Editar Votación' : 'Nueva Votación';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $title ?></h1>
        <p><?= $isEdit ? 'Modifica los datos de la votación' : 'Crea una nueva votación' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/votings" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Volver</a>
        <?php if ($isEdit): ?>
            <a href="/admin/votings/<?= $voting['id'] ?>/candidates" class="btn btn-outline"><i class="fas fa-users"></i> Candidatos</a>
            <a href="/admin/votings/<?= $voting['id'] ?>/results" class="btn btn-outline"><i class="fas fa-chart-bar"></i> Resultados</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? "/admin/votings/{$voting['id']}/edit" : '/admin/votings/create' ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Main Content -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3>Información Básica</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label required">Título</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($voting['title'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Slug (URL)</label>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="text-muted">/votar/</span>
                            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($voting['slug'] ?? '') ?>" placeholder="mi-votacion" style="flex: 1;">
                        </div>
                        <small class="text-muted">Dejar vacío para generar automáticamente</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($voting['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Evento</label>
                        <select name="event_id" class="form-control">
                            <option value="">Sin evento asociado</option>
                            <?php foreach ($events as $evt): ?>
                                <option value="<?= $evt['id'] ?>" <?= ($voting['event_id'] ?? '') == $evt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($evt['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Periodo de Votación</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Fecha/Hora Inicio</label>
                            <input type="datetime-local" name="voting_start" class="form-control" value="<?= $voting['voting_start'] ? date('Y-m-d\TH:i', strtotime($voting['voting_start'])) : '' ?>">
                            <small class="text-muted">Dejar vacío para sin límite de inicio</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha/Hora Fin</label>
                            <input type="datetime-local" name="voting_end" class="form-control" value="<?= $voting['voting_end'] ? date('Y-m-d\TH:i', strtotime($voting['voting_end'])) : '' ?>">
                            <small class="text-muted">Dejar vacío para sin límite de fin</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Imagen Destacada</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($voting['featured_image'])): ?>
                        <div style="margin-bottom: 1rem;">
                            <img src="<?= htmlspecialchars($voting['featured_image']) ?>" alt="Imagen actual" style="max-width: 300px; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                    <small class="text-muted">Formatos: JPG, PNG, WebP. Tamaño máximo: 2MB</small>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3>Estado</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Estado de la votación</label>
                        <select name="status" class="form-control">
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($voting['status'] ?? 'draft') == $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Opciones de Visualización</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="show_vote_counts" value="1" <?= ($voting['show_vote_counts'] ?? 0) ? 'checked' : '' ?>>
                            <span>Mostrar contador de votos</span>
                        </label>
                        <small class="text-muted">Los visitantes verán cuántos votos tiene cada candidato</small>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="show_ranking" value="1" <?= ($voting['show_ranking'] ?? 0) ? 'checked' : '' ?>>
                            <span>Mostrar ranking</span>
                        </label>
                        <small class="text-muted">Los candidatos se ordenarán por votos</small>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="allow_multiple_votes" value="1" <?= ($voting['allow_multiple_votes'] ?? 0) ? 'checked' : '' ?>>
                            <span>Permitir múltiples votos</span>
                        </label>
                        <small class="text-muted">Un usuario puede votar más de una vez</small>
                    </div>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Estadísticas</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: center;">
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold;"><?= $candidateCount ?? 0 ?></div>
                            <div class="text-muted">Candidatos</div>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold;"><?= $totalVotes ?? 0 ?></div>
                            <div class="text-muted">Votos</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Crear Votación' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}
.form-check input[type="checkbox"] {
    width: 18px;
    height: 18px;
}
</style>
