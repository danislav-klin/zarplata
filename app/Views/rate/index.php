<div class="app-wrapper">

    <?= view('templates/sidebar') ?>

    <div class="content">
        <?= view('templates/topbar', ['crumbs' => 'Список ставок']) ?>

        <div class="mt-4">

            <!-- Flash сообщения -->
            <?php if ($msg = session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= esc($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($errs = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php foreach ($errs as $e): ?>
                        <div><?= esc($e) ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Верхняя панель -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#rateModal"
                        data-mode="add">
                        <i class="bi bi-plus-lg"></i> Добавить ставку
                    </button>
                    <a href="<?= base_url('/rates/export-csv') ?>?<?= http_build_query([
                                                                        'search'  => $search,
                                                                        'unit_id' => $unitFilter,
                                                                    ]) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download"></i> Экспорт CSV
                    </a>
                </div>
                <div class="text-muted small">Всего: <strong><?= $total ?></strong></div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Фильтры -->
                    <form method="get" action="<?= base_url('/rates-list') ?>">
                        <div class="row mb-3 g-2">
                            <div class="col-auto">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="По названию, описанию"
                                    value="<?= esc($search) ?>" style="width:220px">
                            </div>
                            <div class="col-auto">
                                <select name="unit_id" class="form-select form-select-sm">
                                    <option value="">Все единицы</option>
                                    <?php foreach ($units as $u): ?>
                                        <option value="<?= $u['id'] ?>"
                                            <?= $unitFilter == $u['id'] ? 'selected' : '' ?>>
                                            <?= esc($u['name']) ?> (<?= esc($u['short_name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary">Найти</button>
                                <a href="<?= base_url('/rates-list') ?>"
                                    class="btn btn-sm btn-outline-secondary ms-1">Сброс</a>
                            </div>
                            <div class="col-auto ms-auto d-flex align-items-center gap-2">
                                <label class="small text-muted">Показывать</label>
                                <select name="per_page" class="form-select form-select-sm"
                                    style="width:75px" onchange="this.form.submit()">
                                    <?php foreach ([10, 25, 50] as $n): ?>
                                        <option value="<?= $n ?>" <?= $perPage == $n ? 'selected' : '' ?>>
                                            <?= $n ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Таблица -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Название</th>
                                    <th>Единица измерения</th>
                                    <th>Ставка</th>
                                    <th>Описание</th>
                                    <th>Активна</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rates)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-cash-coin fs-3 d-block mb-2"></i>
                                            Ставки не найдены
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rates as $rate): ?>
                                        <tr>
                                            <td><?= esc($rate['name']) ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= esc($rate['unit_name']) ?>
                                                    <?php if (!empty($rate['unit_short'])): ?>
                                                        (<?= esc($rate['unit_short']) ?>)
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold"><?= number_format($rate['rate'], 2) ?> ₽</td>
                                            <td class="text-muted small"><?= esc($rate['description'] ?? '—') ?></td>
                                            <td>
                                                <?php if ($rate['is_active']): ?>
                                                    <span class="badge bg-success">Да</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Нет</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <!-- Просмотр -->
                                                    <button class="btn btn-outline-secondary"
                                                        title="Просмотр"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewRateModal"
                                                        data-name="<?= esc($rate['name']) ?>"
                                                        data-rate="<?= esc($rate['rate']) ?>"
                                                        data-unit="<?= esc($rate['unit_name']) ?>"
                                                        data-description="<?= esc($rate['description'] ?? '') ?>"
                                                        data-active="<?= $rate['is_active'] ? 'Да' : 'Нет' ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <!-- Редактировать -->
                                                    <button class="btn btn-outline-primary"
                                                        title="Редактировать"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rateModal"
                                                        data-mode="edit"
                                                        data-id="<?= $rate['id'] ?>"
                                                        data-name="<?= esc($rate['name']) ?>"
                                                        data-rate="<?= esc($rate['rate']) ?>"
                                                        data-unit-id="<?= esc($rate['unit_id']) ?>"
                                                        data-description="<?= esc($rate['description'] ?? '') ?>"
                                                        data-active="<?= $rate['is_active'] ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <!-- Удалить -->
                                                    <button class="btn btn-outline-danger"
                                                        title="Удалить"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmDeleteModal"
                                                        data-id="<?= $rate['id'] ?>"
                                                        data-name="<?= esc($rate['name']) ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация -->
                    <?php if ($total > $perPage): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small text-muted">
                                Всего: <?= $total ?>
                            </div>
                            <nav><?= $pager->links() ?></nav>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модалка: Добавить единицу измерения -->
<div class="modal fade" id="unitModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="post" action="<?= base_url('/rates/store-unit') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Новая единица измерения</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control"
                            placeholder="час, тонна, км" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Краткое обозначение</label>
                        <input name="short_name" type="text" class="form-control"
                            placeholder="ч, т, км">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary btn-sm">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модалка: Добавить / Редактировать ставку -->
<div class="modal fade" id="rateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="rateForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="rateId">
                <div class="modal-header">
                    <h5 class="modal-title" id="rateModalTitle">Добавить ставку</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control" id="rateName" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Ставка за единицу <span class="text-danger">*</span></label>
                            <input name="rate" type="number" step="0.01" min="0.01"
                                class="form-control" id="rateValue" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Единица измерения <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="unit_id" class="form-select" id="rateUnit" required>
                                    <option value="">— Выберите —</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>">
                                            <?= esc($unit['name']) ?>
                                            <?php if (!empty($unit['short_name'])): ?>
                                                (<?= esc($unit['short_name']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#unitModal"
                                    title="Добавить единицу">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3" id="rateDescription"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input name="is_active" class="form-check-input" type="checkbox"
                            id="rateActive" value="1" checked>
                        <label class="form-check-label" for="rateActive">Активна</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary btn-sm">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модалка: Просмотр ставки -->
<div class="modal fade" id="viewRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRateName">Ставка</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Ставка за единицу</dt>
                    <dd class="col-7 fw-bold" id="vRate">—</dd>

                    <dt class="col-5 text-muted">Единица измерения</dt>
                    <dd class="col-7" id="vUnit">—</dd>

                    <dt class="col-5 text-muted">Описание</dt>
                    <dd class="col-7" id="vDescription">—</dd>

                    <dt class="col-5 text-muted">Активна</dt>
                    <dd class="col-7" id="vActive">—</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Модалка: Подтверждение удаления -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтвердите удаление</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Удалить ставку <strong id="deleteName"></strong>? Действие нельзя отменить.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                <a href="#" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Удалить</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Добавить / Редактировать ставку
    document.querySelectorAll('[data-bs-target="#rateModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const mode = this.dataset.mode;
            const form = document.getElementById('rateForm');

            if (mode === 'add') {
                document.getElementById('rateModalTitle').textContent = 'Добавить ставку';
                form.action = '<?= base_url('/rates/store') ?>';
                form.reset();
                document.getElementById('rateId').value = '';
                document.getElementById('rateActive').checked = true;
            } else {
                document.getElementById('rateModalTitle').textContent = 'Редактировать ставку';
                form.action = '<?= base_url('/rates/update') ?>';
                document.getElementById('rateId').value = this.dataset.id;
                document.getElementById('rateName').value = this.dataset.name;
                document.getElementById('rateValue').value = this.dataset.rate;
                document.getElementById('rateUnit').value = this.dataset.unitId;
                document.getElementById('rateDescription').value = this.dataset.description;
                document.getElementById('rateActive').checked = this.dataset.active == '1';
            }
        });
    });

    // Просмотр ставки
    document.querySelectorAll('[data-bs-target="#viewRateModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            document.getElementById('viewRateName').textContent = d.name;
            document.getElementById('vRate').textContent = d.rate + ' ₽';
            document.getElementById('vUnit').textContent = d.unit;
            document.getElementById('vDescription').textContent = d.description || '—';
            document.getElementById('vActive').textContent = d.active;
        });
    });

    // Удаление ставки
    document.querySelectorAll('[data-bs-target="#confirmDeleteModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('deleteName').textContent = this.dataset.name;
            document.getElementById('deleteConfirmBtn').href =
                '<?= base_url('/rates/delete/') ?>' + this.dataset.id;
        });
    });

    // Сайдбар
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        const sb = document.querySelector('.sidebar');
        sb.style.display = (sb.style.display === 'none') ? 'flex' : 'none';
    });
</script>