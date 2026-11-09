<div class="app-wrapper">

    <?= view('templates/sidebar') ?>

    <div class="content">
        <?= view('templates/topbar', ['crumbs' => $crumbs]) ?>

        <div class="mt-4">

            <!-- Flash сообщения -->
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('message') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#entryModal" data-mode="add">
                        <i class="bi bi-plus-lg"></i> Добавить запись
                    </button>
                    <a href="<?= base_url('/workentries/export-csv') ?>?<?= http_build_query([
                                                                            'date_from'   => $dateFrom,
                                                                            'date_to'     => $dateTo,
                                                                            'employee_id' => $employeeId,
                                                                            'rate_id'     => $rateId,
                                                                            'search'      => $search,
                                                                        ]) ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download"></i> Экспорт CSV
                    </a>
                </div>
                <div class="text-muted small">Всего: <?= number_format($total, 0, ',', ' ') ?></div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Фильтры -->
                    <form method="get" action="<?= base_url('/workentries-list') ?>">
                        <div class="row align-items-end filter-row mb-3 g-2">
                            <div class="col-auto">
                                <label class="form-label small mb-1">С даты</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="<?= esc($dateFrom) ?>">
                            </div>
                            <div class="col-auto">
                                <label class="form-label small mb-1">По дату</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="<?= esc($dateTo) ?>">
                            </div>
                            <div class="col-auto">
                                <label class="form-label small mb-1">Сотрудник</label>
                                <select name="employee_id" class="form-select form-select-sm" style="min-width:180px">
                                    <option value="">Все сотрудники</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= $emp['id'] ?>" <?= $employeeId == $emp['id'] ? 'selected' : '' ?>>
                                            <?= esc($emp['last_name'] . ' ' . $emp['first_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label small mb-1">Ставка</label>
                                <select name="rate_id" class="form-select form-select-sm" style="min-width:180px">
                                    <option value="">Все ставки</option>
                                    <?php foreach ($rates as $rate): ?>
                                        <option value="<?= $rate['id'] ?>" <?= $rateId == $rate['id'] ? 'selected' : '' ?>>
                                            <?= esc($rate['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label small mb-1">Поиск</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="По примечанию, виду работы"
                                    value="<?= esc($search) ?>" style="min-width:220px">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary">Применить</button>
                                <a href="<?= base_url('/workentries') ?>" class="btn btn-sm btn-outline-secondary">Сброс</a>
                            </div>
                            <div class="col-auto ms-auto d-flex align-items-center gap-2">
                                <label class="small text-muted mb-0">Показывать</label>
                                <select name="per_page" class="form-select form-select-sm"
                                    style="width:75px" onchange="this.form.submit()">
                                    <?php foreach ([10, 25, 50, 100] as $n): ?>
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
                                    <th>Дата</th>
                                    <th>Сотрудник</th>
                                    <th>Вид работы</th>
                                    <th>Кол-во</th>
                                    <th>Ставка</th>
                                    <th>Сумма</th>
                                    <th>Примечание</th>
                                    <th>Создал</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($entries)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Записей не найдено
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($entries as $entry): ?>
                                        <tr>
                                            <td><?= esc($entry['work_date']) ?></td>
                                            <td><?= esc(trim($entry['employee_name'])) ?></td>
                                            <td>
                                                <?= esc($entry['rate_name'] ?? '—') ?>
                                                <?php if (!empty($entry['rate_unit'])): ?>
                                                    <span class="text-muted small">(<?= esc($entry['rate_unit']) ?>)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($entry['qty']) ?></td>
                                            <td><?= number_format($entry['rate_snapshot'], 2) ?> ₽</td>
                                            <td class="fw-bold"><?= number_format($entry['amount'], 2) ?> ₽</td>
                                            <td class="text-muted small"><?= esc($entry['note'] ?? '—') ?></td>
                                            <td class="small"><?= esc($entry['created_by_name'] ?? '—') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-secondary" title="Просмотр"
                                                        data-bs-toggle="modal" data-bs-target="#viewModal"
                                                        data-date="<?= esc($entry['work_date']) ?>"
                                                        data-employee="<?= esc($entry['employee_name']) ?>"
                                                        data-rate="<?= esc($entry['rate_name']) ?>"
                                                        data-unit="<?= esc($entry['rate_unit']) ?>"
                                                        data-qty="<?= esc($entry['qty']) ?>"
                                                        data-rate-snapshot="<?= esc($entry['rate_snapshot']) ?>"
                                                        data-amount="<?= esc($entry['amount']) ?>"
                                                        data-note="<?= esc($entry['note'] ?? '') ?>"
                                                        data-created-by="<?= esc($entry['created_by_name'] ?? '—') ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-primary" title="Редактировать"
                                                        data-bs-toggle="modal" data-bs-target="#entryModal"
                                                        data-mode="edit"
                                                        data-id="<?= $entry['id'] ?>"
                                                        data-date="<?= esc($entry['work_date']) ?>"
                                                        data-employee-id="<?= esc($entry['employee_id']) ?>"
                                                        data-rate-id="<?= esc($entry['rate_id']) ?>"
                                                        data-qty="<?= esc($entry['qty']) ?>"
                                                        data-note="<?= esc($entry['note'] ?? '') ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Удалить"
                                                        data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                                        data-id="<?= $entry['id'] ?>"
                                                        data-name="<?= esc($entry['rate_name']) ?>">
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
                            <nav><?= $pager->links('work_entries', 'default_full') ?></nav>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>

</div>

<!-- Модалка: Добавить / Редактировать запись -->
<div class="modal fade" id="entryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" id="entryForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="entryId">
                <div class="modal-header">
                    <h5 class="modal-title" id="entryModalLabel">Добавить запись</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Дата <span class="text-danger">*</span></label>
                            <input type="date" name="work_date" class="form-control" id="entryDate"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Сотрудник <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select" id="entryEmployee" required>
                                <option value="">— Выберите сотрудника —</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>">
                                        <?= esc($emp['last_name'] . ' ' . $emp['first_name']) ?>
                                        <?php if (!empty($emp['surname'])): ?>
                                            <?= esc($emp['surname']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($emp['position_name'])): ?>
                                            — <span class="text-muted"><?= esc($emp['position_name']) ?></span>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Ставка <span class="text-danger">*</span></label>
                            <select name="rate_id" class="form-select" id="entryRate" required>
                                <option value="">— Выберите ставку —</option>
                                <?php foreach ($rates as $rate): ?>
                                    <option value="<?= $rate['id'] ?>"
                                        data-rate="<?= $rate['rate'] ?>"
                                        data-unit="<?= esc($rate['unit_name']) ?>">
                                        <?= esc($rate['name']) ?>
                                        <?php if (!empty($rate['unit_short'])): ?>
                                            (<?= esc($rate['unit_short']) ?>)
                                        <?php else: ?>
                                            (<?= esc($rate['unit_name']) ?>)
                                        <?php endif; ?>
                                        — <?= number_format($rate['rate'], 2) ?> ₽
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Количество <span class="text-danger">*</span></label>
                            <input type="number" name="qty" step="0.01" min="0.01"
                                class="form-control" id="entryQty" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ставка (фиксация)</label>
                            <input type="text" class="form-control" id="entryRateSnapshot" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Сумма</label>
                            <input type="text" class="form-control" id="entryAmount" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Примечание</label>
                            <textarea name="note" class="form-control" rows="2" id="entryNote"></textarea>
                        </div>
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

<!-- Модалка: Просмотр записи -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Просмотр записи</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" style="width:140px">Дата:</td>
                        <td id="vDate"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Сотрудник:</td>
                        <td id="vEmployee"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Вид работы:</td>
                        <td id="vRate"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Количество:</td>
                        <td id="vQty"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ставка:</td>
                        <td id="vRateSnapshot"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Сумма:</td>
                        <td class="fw-bold" id="vAmount"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Примечание:</td>
                        <td id="vNote"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Создал:</td>
                        <td id="vCreatedBy"></td>
                    </tr>
                </table>
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
                Вы действительно хотите удалить запись <strong id="deleteName"></strong>?<br>
                Действие нельзя будет отменить.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                <a href="#" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Удалить</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Добавить / Редактировать запись
    document.querySelectorAll('[data-bs-target="#entryModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const mode = this.dataset.mode;
            const form = document.getElementById('entryForm');

            if (mode === 'add') {
                document.getElementById('entryModalLabel').textContent = 'Добавить запись';
                form.action = '<?= base_url('/workentries/store') ?>';
                form.reset();
                document.getElementById('entryId').value = '';
                document.getElementById('entryDate').value = '<?= date('Y-m-d') ?>';
                document.getElementById('entryRateSnapshot').value = '';
                document.getElementById('entryAmount').value = '';
            } else {
                document.getElementById('entryModalLabel').textContent = 'Редактировать запись';
                form.action = '<?= base_url('/workentries/update') ?>';
                document.getElementById('entryId').value = this.dataset.id;
                document.getElementById('entryDate').value = this.dataset.date;
                document.getElementById('entryEmployee').value = this.dataset.employeeId;
                document.getElementById('entryRate').value = this.dataset.rateId;
                document.getElementById('entryQty').value = this.dataset.qty;
                document.getElementById('entryNote').value = this.dataset.note;

                // Триггерим расчёт суммы
                calcAmount();
            }
        });
    });

    // Просмотр записи
    document.querySelectorAll('[data-bs-target="#viewModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            document.getElementById('vDate').textContent = d.date;
            document.getElementById('vEmployee').textContent = d.employee;
            document.getElementById('vRate').textContent = d.rate + (d.unit ? ' (' + d.unit + ')' : '');
            document.getElementById('vQty').textContent = d.qty + (d.unit ? ' ' + d.unit : '');
            document.getElementById('vRateSnapshot').textContent = parseFloat(d.rateSnapshot).toFixed(2) + ' ₽';
            document.getElementById('vAmount').textContent = parseFloat(d.amount).toFixed(2) + ' ₽';
            document.getElementById('vNote').textContent = d.note || '—';
            document.getElementById('vCreatedBy').textContent = d.createdBy;
        });
    });

    // Удаление записи
    document.querySelectorAll('[data-bs-target="#confirmDeleteModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('deleteName').textContent = this.dataset.name;
            document.getElementById('deleteConfirmBtn').href =
                '<?= base_url('/workentries/delete/') ?>' + this.dataset.id;
        });
    });

    // Автоматический расчёт суммы
    const rateSelect = document.getElementById('entryRate');
    const qtyInput = document.getElementById('entryQty');
    const snapshotOut = document.getElementById('entryRateSnapshot');
    const amountOut = document.getElementById('entryAmount');

    function calcAmount() {
        const opt = rateSelect.options[rateSelect.selectedIndex];
        const rate = parseFloat(opt?.dataset.rate || 0);
        const qty = parseFloat(qtyInput.value || 0);

        snapshotOut.value = rate ? rate.toFixed(2) + ' ₽' : '';
        amountOut.value = (rate && qty) ? (rate * qty).toFixed(2) + ' ₽' : '';
    }

    rateSelect.addEventListener('change', calcAmount);
    qtyInput.addEventListener('input', calcAmount);

    // Скрыть/показать сайдбар
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        const sb = document.querySelector('.sidebar');
        sb.style.display = (sb.style.display === 'none') ? 'flex' : 'none';
    });
</script>