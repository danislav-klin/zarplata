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

            <!-- Заголовок + кнопка -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Должности</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="bi bi-plus-lg me-1"></i> Добавить
                </button>
            </div>

            <!-- Таблица -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th>Название</th>
                                    <th style="width:180px">Отдел</th>
                                    <th>Описание</th>
                                    <th style="width:120px">Создана</th>
                                    <th style="width:120px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($positions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Должностей пока нет
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($positions as $pos): ?>
                                        <tr>
                                            <td class="text-muted"><?= $pos['id'] ?></td>
                                            <td class="fw-medium"><?= esc($pos['name']) ?></td>
                                            <td>
                                                <?php if (!empty($pos['department_name'])): ?>
                                                    <span class="badge bg-secondary"><?= esc($pos['department_name']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted small"><?= esc($pos['description'] ?? '—') ?></td>
                                            <td class="text-muted small"><?= date('d.m.Y', strtotime($pos['created_at'])) ?></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEdit"
                                                    data-id="<?= $pos['id'] ?>"
                                                    data-name="<?= esc($pos['name']) ?>"
                                                    data-department="<?= esc($pos['department_id'] ?? '') ?>"
                                                    data-description="<?= esc($pos['description'] ?? '') ?>"
                                                    title="Редактировать">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDelete"
                                                    data-id="<?= $pos['id'] ?>"
                                                    data-name="<?= esc($pos['name']) ?>"
                                                    title="Удалить">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Модалка: Добавить -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('/positions/store') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Добавить должность</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control" placeholder="Например: Кладовщик" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Отдел</label>
                        <select name="department_id" class="form-select">
                            <option value="">— Не указан —</option>
                            <?php foreach ($departments as $dep): ?>
                                <option value="<?= $dep['id'] ?>"><?= esc($dep['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Краткое описание обязанностей"></textarea>
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

<!-- Модалка: Редактировать -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('/positions/update') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать должность</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Отдел</label>
                        <select name="department_id" class="form-select" id="editDepartment">
                            <option value="">— Не указан —</option>
                            <?php foreach ($departments as $dep): ?>
                                <option value="<?= $dep['id'] ?>"><?= esc($dep['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3" id="editDescription"></textarea>
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

<!-- Модалка: Подтверждение удаления -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтвердите удаление</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Вы действительно хотите удалить должность <strong id="deleteName"></strong>?<br>
                <span class="text-muted small">Если должность используется сотрудниками, удаление будет невозможно.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                <a href="#" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Удалить</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Заполняем поля модалки редактирования
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('editId').value = btn.dataset.id;
        document.getElementById('editName').value = btn.dataset.name;
        document.getElementById('editDescription').value = btn.dataset.description;
        document.getElementById('editDepartment').value = btn.dataset.department || '';
    });

    // Заполняем модалку удаления
    document.getElementById('modalDelete').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('deleteName').textContent = btn.dataset.name;
        document.getElementById('deleteConfirmBtn').href =
            '<?= base_url('/positions/delete/') ?>' + btn.dataset.id;
    });

    // Скрыть/показать сайдбар
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        const sb = document.querySelector('.sidebar');
        sb.style.display = (sb.style.display === 'none') ? 'flex' : 'none';
    });
</script>