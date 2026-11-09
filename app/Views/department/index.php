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
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#departmentModal" data-mode="add">
                        <i class="bi bi-plus-lg"></i> Добавить отдел
                    </button>
                    <a href="<?= base_url('/employees/export') ?>?<?= http_build_query([
                                                                            'search' => $search,
                                                                        ]) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download"></i> Экспорт CSV
                    </a>
                </div>
                <div class="text-muted small">Всего: <?= $total ?></div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Фильтры -->
                    <form method="get" action="<?= base_url('/departments-list') ?>">
                        <div class="row mb-3 g-2">
                            <div class="col-auto">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="По названию отдела или описанию"
                                    value="<?= esc($search) ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary">Найти</button>
                                <a href="<?= base_url('/departments') ?>" class="btn btn-sm btn-outline-secondary">Сброс</a>
                            </div>
                            <div class="col-auto ms-auto d-flex align-items-center">
                                <label class="small text-muted me-2">Показывать</label>
                                <select name="per_page" class="form-select form-select-sm"
                                    style="width:80px" onchange="this.form.submit()">
                                    <?php foreach ([10, 25, 50, 100] as $n): ?>
                                        <option value="<?= $n ?>" <?= $perPage == $n ? 'selected' : '' ?>><?= $n ?></option>
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
                                    <th>Описание</th>
                                    <th style="width:120px">Сотрудников</th>
                                    <th style="width:100px">Активен</th>
                                    <th style="width:140px">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($departments)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Отделов не найдено
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($departments as $dep): ?>
                                        <tr>
                                            <td class="fw-medium"><?= esc($dep['name']) ?></td>
                                            <td class="text-muted small"><?= esc($dep['description'] ?? '—') ?></td>
                                            <td class="text-center">
                                                <?php if ($dep['employee_count'] > 0): ?>
                                                    <a href="<?= base_url('/departments/view/' . $dep['id']) ?>"
                                                        class="badge bg-primary text-decoration-none">
                                                        <?= $dep['employee_count'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($dep['is_active']): ?>
                                                    <span class="badge bg-success">Да</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Нет</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= base_url('/departments/view/' . $dep['id']) ?>"
                                                        class="btn btn-outline-secondary" title="Просмотр">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button class="btn btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#departmentModal"
                                                        data-mode="edit"
                                                        data-id="<?= $dep['id'] ?>"
                                                        data-name="<?= esc($dep['name']) ?>"
                                                        data-description="<?= esc($dep['description'] ?? '') ?>"
                                                        data-is-active="<?= $dep['is_active'] ?>"
                                                        title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmDeleteModal"
                                                        data-id="<?= $dep['id'] ?>"
                                                        data-name="<?= esc($dep['name']) ?>"
                                                        title="Удалить">
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
                            <nav><?= $pager->links('departments', 'default_full') ?></nav>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal: Add / Edit department -->
<div class="modal fade" id="departmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="departmentForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="deptId">
                <div class="modal-header">
                    <h5 class="modal-title" id="departmentModalLabel">Добавить отдел</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="deptName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3" id="deptDescription"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                            id="deptActive" value="1" checked>
                        <label class="form-check-label" for="deptActive">Активен</label>
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

<!-- Modal: Confirm delete -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтвердите удаление</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Вы действительно хотите удалить отдел <strong id="deleteName"></strong>?<br>
                <span class="text-muted small">Если отдел используется сотрудниками, удаление будет невозможно.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                <a href="#" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Удалить</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Добавить / Редактировать отдел
    document.querySelectorAll('[data-bs-target="#departmentModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const mode = this.dataset.mode;
            const form = document.getElementById('departmentForm');

            if (mode === 'add') {
                document.getElementById('departmentModalLabel').textContent = 'Добавить отдел';
                form.action = '<?= base_url('/departments/store') ?>';
                form.reset();
                document.getElementById('deptId').value = '';
                document.getElementById('deptActive').checked = true;
            } else {
                document.getElementById('departmentModalLabel').textContent = 'Редактировать отдел';
                form.action = '<?= base_url('/departments/update') ?>';
                document.getElementById('deptId').value = this.dataset.id;
                document.getElementById('deptName').value = this.dataset.name;
                document.getElementById('deptDescription').value = this.dataset.description;
                document.getElementById('deptActive').checked = this.dataset.isActive == '1';
            }
        });
    });

    // Удаление отдела
    document.querySelectorAll('[data-bs-target="#confirmDeleteModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('deleteName').textContent = this.dataset.name;
            document.getElementById('deleteConfirmBtn').href =
                '<?= base_url('/departments/delete/') ?>' + this.dataset.id;
        });
    });

    // Скрыть/показать сайдбар
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        const sb = document.querySelector('.sidebar');
        sb.style.display = (sb.style.display === 'none') ? 'flex' : 'none';
    });
</script>