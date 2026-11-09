<div class="app-wrapper">

    <?= view('templates/sidebar') ?>

    <div class="content">
        <?= view('templates/topbar', ['crumbs' => $crumbs]) ?>

        <div class="mt-4">
            <!-- Фильтры -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form id="reportForm" class="row g-2 align-items-end">

                        <div class="col-auto">
                            <label class="form-label small mb-1">Период с/по</label>
                            <div class="d-flex gap-1">
                                <input type="date" class="form-control form-control-sm" id="reportFrom" name="date_from">
                                <input type="date" class="form-control form-control-sm" id="reportTo" name="date_to">
                            </div>
                        </div>

                        <div class="col-auto">
                            <label class="form-label small mb-1">Сотрудник</label>
                            <select class="form-select form-select-sm" id="reportEmployee" name="employee_id" style="min-width:180px">
                                <option value="">Все</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>">
                                        <?= esc($emp['last_name'] . ' ' . $emp['first_name']) ?>
                                        <?php if (!empty($emp['surname'])): ?>
                                            <?= esc($emp['surname']) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-auto">
                            <label class="form-label small mb-1">Отдел</label>
                            <select class="form-select form-select-sm" id="reportDepartment" name="department_id" style="min-width:150px">
                                <option value="">Все</option>
                                <?php foreach ($departments as $dep): ?>
                                    <option value="<?= $dep['id'] ?>"><?= esc($dep['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-auto">
                            <label class="form-label small mb-1">Сортировка</label>
                            <select class="form-select form-select-sm" id="reportSort" name="sort_by">
                                <option value="date">По дате</option>
                                <option value="employee">По сотруднику</option>
                                <option value="amount_desc">По сумме (убыв.)</option>
                            </select>
                        </div>

                        <div class="col-auto ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm" id="generateReport">
                                <i class="bi bi-play-fill"></i> Сформировать
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="exportCsv">
                                <i class="bi bi-file-earmark-arrow-down"></i> CSV
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="bi bi-file-earmark-excel"></i> XLSX
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="bi bi-printer"></i> Печать
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Результаты -->
            <div class="card shadow-sm" id="reportResults" style="display:none;">
                <div class="card-body">
                    
                    <!-- Заголовок с итогом -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Отчёт сформирован</h6>
                        <div class="text-end">
                            <div class="text-muted small">Всего записей: <span id="reportCount">0</span></div>
                            <h5 class="mb-0">Итого: <strong id="reportTotal">0.00</strong> ₽</h5>
                        </div>
                    </div>

                    <!-- Вкладки -->
                    <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" 
                                data-bs-target="#detail" type="button" role="tab">
                                <i class="bi bi-list-ul"></i> Детализация выплат
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="summary-tab" data-bs-toggle="tab" 
                                data-bs-target="#summary" type="button" role="tab">
                                <i class="bi bi-person-lines-fill"></i> Сводка по сотрудникам
                            </button>
                        </li>
                    </ul>

                    <!-- Содержимое вкладок -->
                    <div class="tab-content" id="reportTabsContent">
                        
                        <!-- Вкладка 1: Детализация -->
                        <div class="tab-pane fade show active" id="detail" role="tabpanel">
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
                                            <th>Примечание</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportDetail">
                                        <!-- Данные подгрузятся через JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Вкладка 2: Сводка -->
                        <div class="tab-pane fade" id="summary" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Сотрудник</th>
                                                    <th class="text-center">Кол-во записей</th>
                                                    <th class="text-end">Сумма</th>
                                                    <th class="text-center">Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reportSummary">
                                                <!-- Данные подгрузятся через JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">Действия</h6>
                                        <p class="small mb-2">Вы можете скачать payslip (расчётный лист) для каждого сотрудника отдельно или для всех сразу.</p>
                                        <button class="btn btn-sm btn-primary w-100" id="downloadAllPayslips" disabled>
                                            <i class="bi bi-file-earmark-text"></i> Скачать все payslips
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Заглушка "Нет данных" -->
            <div class="card shadow-sm" id="noDataMessage" style="display:none;">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <h6>Нет данных по выбранным критериям</h6>
                    <p class="small mb-0">Попробуйте изменить фильтры или период</p>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    // Скрыть/показать сайдбар
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        const sb = document.querySelector('.sidebar');
        sb.style.display = (sb.style.display === 'none') ? 'flex' : 'none';
    });

    // Формирование отчёта
    document.getElementById('generateReport').addEventListener('click', function() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        // Показываем индикатор загрузки
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Загрузка...';

        fetch('<?= base_url('/reports/generate') ?>?' + params)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.count === 0) {
                        // Нет данных
                        document.getElementById('reportResults').style.display = 'none';
                        document.getElementById('noDataMessage').style.display = 'block';
                    } else {
                        // Заполняем данные
                        document.getElementById('reportCount').textContent = data.count;
                        document.getElementById('reportTotal').textContent = parseFloat(data.total).toFixed(2);

                        // Детализация
                        const detailBody = document.getElementById('reportDetail');
                        detailBody.innerHTML = '';
                        data.entries.forEach(entry => {
                            const row = `
                                <tr>
                                    <td>${entry.work_date}</td>
                                    <td>${entry.employee_name}</td>
                                    <td>${entry.rate_name || '—'}</td>
                                    <td>${entry.qty}</td>
                                    <td>${parseFloat(entry.rate_snapshot).toFixed(2)} ₽</td>
                                    <td class="fw-medium">${parseFloat(entry.amount).toFixed(2)} ₽</td>
                                    <td class="text-muted small">${entry.note || '—'}</td>
                                </tr>
                            `;
                            detailBody.innerHTML += row;
                        });

                        // Сводка
                        const summaryBody = document.getElementById('reportSummary');
                        summaryBody.innerHTML = '';
                        data.summary.forEach(item => {
                            const row = `
                                <tr>
                                    <td class="fw-medium">${item.employee_name}</td>
                                    <td class="text-center">${item.work_count}</td>
                                    <td class="text-end fw-medium">${parseFloat(item.total_amount).toFixed(2)} ₽</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary" disabled>
                                            <i class="bi bi-file-earmark-text"></i> Payslip
                                        </button>
                                    </td>
                                </tr>
                            `;
                            summaryBody.innerHTML += row;
                        });

                        document.getElementById('noDataMessage').style.display = 'none';
                        document.getElementById('reportResults').style.display = 'block';
                    }
                } else {
                    alert('Ошибка при формировании отчёта');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при формировании отчёта');
            })
            .finally(() => {
                // Возвращаем кнопку в исходное состояние
                const btn = document.getElementById('generateReport');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-play-fill"></i> Сформировать';
            });
    });

    // Экспорт CSV
    document.getElementById('exportCsv').addEventListener('click', function() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        
        window.location.href = '<?= base_url('/reports/export-csv') ?>?' + params;
    });
</script>
