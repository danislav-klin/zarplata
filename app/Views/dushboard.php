<div class="app-wrapper">

    <?= view('templates/sidebar') ?>

    <div class="content">
        <?= view('templates/topbar', ['crumbs' => 'Дашборд']) ?>

        <div class="mt-4">

            <!-- Карточки статистики -->
            <div class="row g-3">
                <div class="col-3">
                    <div class="card card-stat shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Сотрудников</div>
                                    <h3 class="mb-0"><?= esc($employeeCount) ?></h3>
                                </div>
                                <div class="text-primary fs-1"><i class="bi bi-people-fill"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-3">
                    <div class="card card-stat shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Записей за период</div>
                                    <h3 class="mb-0"><?= esc($entriesCount) ?></h3>
                                </div>
                                <div class="text-success fs-1"><i class="bi bi-journal-check"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-3">
                    <div class="card card-stat shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Выплаты за месяц</div>
                                    <h3 class="mb-0"><?= number_format($monthAmount, 0, '.', ' ') ?> ₽</h3>
                                </div>
                                <div class="text-warning fs-1"><i class="bi bi-currency-dollar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-3">
                    <div class="card card-stat shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Быстрые действия</div>
                                    <div class="mt-2 quick-actions">
                                        <button class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal" data-bs-target="#modalAddEntry">
                                            <i class="bi bi-plus-lg"></i> Добавить запись
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#modalAddEmployee">
                                            <i class="bi bi-person-plus"></i> Добавить сотрудника
                                        </button>
                                    </div>
                                </div>
                                <div class="text-info fs-1"><i class="bi bi-speedometer2"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">

                <!-- Последние записи -->
                <div class="col-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Последние записи</h5>
                                <div class="text-muted small">Показаны последние 10</div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Дата</th>
                                            <th>Сотрудник</th>
                                            <th>Вид работы</th>
                                            <th>Кол-во</th>
                                            <th>Ставка</th>
                                            <th>Сумма</th>
                                            <th>Создал</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($lastEntries)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">
                                                    Записей пока нет
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($lastEntries as $entry): ?>
                                                <tr>
                                                    <td><?= date('d.m.Y', strtotime($entry['work_date'])) ?></td>
                                                    <td><?= esc(trim($entry['employee_name'])) ?></td>
                                                    <td><?= esc($entry['rate_name'] ?? '—') ?></td>
                                                    <td><?= esc($entry['qty']) ?></td>
                                                    <td><?= number_format($entry['rate_snapshot'], 2) ?> ₽</td>
                                                    <td class="fw-medium"><?= number_format($entry['amount'], 2) ?> ₽</td>
                                                    <td class="text-muted small"><?= esc($entry['created_by_name'] ?? '—') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Правая колонка -->
                <div class="col-4">
                    <!-- Краткий отчёт -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6>Краткий отчёт</h6>
                            <ul class="list-unstyled small mt-3">
                                <li>Текущий месяц: <strong><?= number_format($monthAmount, 0, '.', ' ') ?> ₽</strong></li>
                                <li class="mt-1">Средняя выплата на сотрудника: <strong><?= number_format($avgAmount, 0, '.', ' ') ?> ₽</strong></li>
                                <li class="mt-1">Активных ставок: <strong><?= esc($activeRates) ?></strong></li>
                            </ul>
                            <div class="mt-3 d-flex gap-2">
                                <a href="<?= base_url('/reports-list') ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-bar-chart"></i> Открыть отчёт
                                </a>
                                <a href="<?= base_url('/export-csv') ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>Экспорт CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Топ-5 сотрудников -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="bi bi-trophy text-warning"></i> Топ-5 за месяц
                            </h6>
                            <?php if (empty($topEmployees)): ?>
                                <div class="text-center text-muted py-3 small">
                                    Нет данных за текущий месяц
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($topEmployees as $index => $emp): ?>
                                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-<?= $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'light text-dark') ?> me-2">
                                                    <?= $index + 1 ?>
                                                </span>
                                                <span class="small"><?= esc($emp['employee_name']) ?></span>
                                                <div class="text-muted" style="font-size:0.75rem; margin-left: 30px;">
                                                    <?= $emp['work_count'] ?> <?= declension($emp['work_count'], ['запись', 'записи', 'записей']) ?>
                                                </div>
                                            </div>
                                            <strong class="text-primary"><?= number_format($emp['total_amount'], 0, '.', ' ') ?> ₽</strong>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Модалка: Добавить запись -->
<div class="modal fade" id="modalAddEntry" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('/workentries/store') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Добавить запись</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Дата <span class="text-danger">*</span></label>
                        <input name="work_date" type="date" class="form-control"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Сотрудник <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">— Выберите —</option>
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
                    <div class="mb-3">
                        <label class="form-label">Ставка <span class="text-danger">*</span></label>
                        <select name="rate_id" class="form-select" required>
                            <option value="">— Выберите —</option>
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
                    <div class="mb-3">
                        <label class="form-label">Количество <span class="text-danger">*</span></label>
                        <input name="qty" type="number" step="0.01" min="0.01"
                            class="form-control" id="entryQty" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Сумма</label>
                        <input type="text" class="form-control" id="entryAmount" readonly
                            placeholder="Рассчитается автоматически">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Примечание</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
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

<!-- Модалка: Добавить сотрудника -->
<div class="modal fade" id="modalAddEmployee" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('/employees/store') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Добавить сотрудника</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Фамилия <span class="text-danger">*</span></label>
                            <input name="last_name" type="text" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Имя <span class="text-danger">*</span></label>
                            <input name="first_name" type="text" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Отчество</label>
                            <input name="surname" type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Телефон</label>
                            <input name="phone" type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Отдел</label>
                            <select name="department_id" class="form-select">
                                <option value="">— Не указан —</option>
                                <?php foreach ($departments as $dep): ?>
                                    <option value="<?= $dep['id'] ?>"><?= esc($dep['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Должность</label>
                            <select name="position_id" class="form-select">
                                <option value="">— Не указана —</option>
                                <?php foreach ($positions as $pos): ?>
                                    <option value="<?= $pos['id'] ?>"><?= esc($pos['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Дата приёма</label>
                            <input name="hired_at" type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Табельный номер</label>
                            <input name="personnel_number" type="text" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Заметка</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
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

<script>
    // Автоматический расчёт суммы в модалке записи
    const rateSelect = document.querySelector('[name="rate_id"]');
    const qtyInput = document.getElementById('entryQty');
    const amountOut = document.getElementById('entryAmount');

    function calcAmount() {
        const opt = rateSelect.options[rateSelect.selectedIndex];
        const rate = parseFloat(opt?.dataset.rate || 0);
        const qty = parseFloat(qtyInput.value || 0);
        amountOut.value = (rate && qty) ? (rate * qty).toFixed(2) + ' ₽' : '';
    }

    rateSelect.addEventListener('change', calcAmount);
    qtyInput.addEventListener('input', calcAmount);

    // Скрыть/показать сайдбар
    document.getElementById('toggleSidebar').addEventListener('click', function() {
        const sb = document.querySelector('.sidebar');
        sb.style.display = (sb.style.display === 'none') ? 'flex' : 'none';
    });
</script>

<?php
// Функция склонения числительных
function declension($number, $titles) {
    $cases = [2, 0, 1, 1, 1, 2];
    return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}
?>
