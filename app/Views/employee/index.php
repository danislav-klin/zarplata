<div class="app-wrapper">

  <?= view('templates/sidebar') ?>

  <div class="content">
    <?= view('templates/topbar', ['crumbs' => 'Список сотрудников']) ?>

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
            data-bs-toggle="modal" data-bs-target="#employeeModal"
            data-mode="add">
            <i class="bi bi-person-plus"></i> Добавить сотрудника
          </button>
          <a href="<?= base_url('/employees/export') ?>"
            class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-download"></i> Экспорт CSV
          </a>
        </div>
        <div class="text-muted small">Всего: <strong><?= $total ?></strong></div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">

          <!-- Фильтры -->
          <form method="get" action="<?= base_url('/employees-list') ?>">
            <div class="row align-items-center g-2 mb-3">
              <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm"
                  placeholder="ФИО, таб. номер, телефон, email"
                  value="<?= esc($search) ?>" style="width:260px">
              </div>
              <div class="col-auto">
                <select name="department_id" class="form-select form-select-sm">
                  <option value="">Все отделы</option>
                  <?php foreach ($departments as $dep): ?>
                    <option value="<?= $dep['id'] ?>"
                      <?= $deptFilter == $dep['id'] ? 'selected' : '' ?>>
                      <?= esc($dep['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-auto">
                <select name="position_id" class="form-select form-select-sm">
                  <option value="">Все должности</option>
                  <?php foreach ($positions as $pos): ?>
                    <option value="<?= $pos['id'] ?>"
                      <?= $posFilter == $pos['id'] ? 'selected' : '' ?>>
                      <?= esc($pos['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Найти</button>
                <a href="<?= base_url('/employees-list') ?>"
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
                  <th>ФИО</th>
                  <th>Таб. №</th>
                  <th>Должность</th>
                  <th>Отдел</th>
                  <th>Телефон</th>
                  <th>Email</th>
                  <th>Дата приёма</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($employees)): ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      <i class="bi bi-people fs-3 d-block mb-2"></i>
                      Сотрудники не найдены
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($employees as $emp): ?>
                    <tr>
                      <td>
                        <?= esc($emp['last_name'] . ' ' . $emp['first_name']
                          . ($emp['surname'] ? ' ' . $emp['surname'] : '')) ?>
                      </td>
                      <td class="text-muted"><?= esc($emp['personnel_number'] ?? '—') ?></td>
                      <td><?= esc($emp['position_name'] ?? '—') ?></td>
                      <td>
                        <?php if (!empty($emp['department_name'])): ?>
                          <span class="badge bg-secondary">
                            <?= esc($emp['department_name']) ?>
                          </span>
                        <?php else: ?>
                          <span class="text-muted">—</span>
                        <?php endif; ?>
                      </td>
                      <td><?= esc($emp['phone'] ?? '—') ?></td>
                      <td><?= esc($emp['email'] ?? '—') ?></td>
                      <td class="text-muted"><?= esc($emp['hired_at'] ?? '—') ?></td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <!-- Просмотр -->
                          <button class="btn btn-outline-secondary"
                            title="Просмотр"
                            data-bs-toggle="modal"
                            data-bs-target="#viewEmployeeModal"
                            data-id="<?= $emp['id'] ?>"
                            data-last="<?= esc($emp['last_name']) ?>"
                            data-first="<?= esc($emp['first_name']) ?>"
                            data-surname="<?= esc($emp['surname'] ?? '') ?>"
                            data-personnel="<?= esc($emp['personnel_number'] ?? '') ?>"
                            data-phone="<?= esc($emp['phone'] ?? '') ?>"
                            data-email="<?= esc($emp['email'] ?? '') ?>"
                            data-hired="<?= esc($emp['hired_at'] ?? '') ?>"
                            data-department="<?= esc($emp['department_name'] ?? '—') ?>"
                            data-position="<?= esc($emp['position_name'] ?? '—') ?>"
                            data-note="<?= esc($emp['note'] ?? '') ?>">
                            <i class="bi bi-eye"></i>
                          </button>
                          <!-- Редактировать -->
                          <button class="btn btn-outline-primary"
                            title="Редактировать"
                            data-bs-toggle="modal"
                            data-bs-target="#employeeModal"
                            data-mode="edit"
                            data-id="<?= $emp['id'] ?>"
                            data-last="<?= esc($emp['last_name']) ?>"
                            data-first="<?= esc($emp['first_name']) ?>"
                            data-surname="<?= esc($emp['surname'] ?? '') ?>"
                            data-personnel="<?= esc($emp['personnel_number'] ?? '') ?>"
                            data-phone="<?= esc($emp['phone'] ?? '') ?>"
                            data-email="<?= esc($emp['email'] ?? '') ?>"
                            data-hired="<?= esc($emp['hired_at'] ?? '') ?>"
                            data-dept="<?= esc($emp['department_id'] ?? '') ?>"
                            data-pos="<?= esc($emp['position_id'] ?? '') ?>"
                            data-note="<?= esc($emp['note'] ?? '') ?>">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <!-- Удалить -->
                          <button class="btn btn-outline-danger"
                            title="Удалить"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-id="<?= $emp['id'] ?>"
                            data-name="<?= esc($emp['last_name'] . ' ' . $emp['first_name']) ?>">
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
              <nav><?= $pager->links('employees', 'default_full') ?></nav>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Модалка: Добавить / Редактировать сотрудника -->
<div class="modal fade" id="employeeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" id="employeeForm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="empId">
        <div class="modal-header">
          <h5 class="modal-title" id="employeeModalTitle">Добавить сотрудника</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label">Фамилия <span class="text-danger">*</span></label>
              <input name="last_name" type="text" class="form-control" id="empLastName" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Имя <span class="text-danger">*</span></label>
              <input name="first_name" type="text" class="form-control" id="empFirstName" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Отчество</label>
              <input name="surname" type="text" class="form-control" id="empSurname">
            </div>
            <div class="col-md-6">
              <label class="form-label">Табельный номер</label>
              <input name="personnel_number" type="text" class="form-control" id="empPersonnel">
            </div>
            <div class="col-md-6">
              <label class="form-label">Дата приёма</label>
              <input name="hired_at" type="date" class="form-control" id="empHired">
            </div>
            <div class="col-md-6">
              <label class="form-label">Отдел</label>
              <select name="department_id" class="form-select" id="empDept">
                <option value="">— Не указан —</option>
                <?php foreach ($departments as $dep): ?>
                  <option value="<?= $dep['id'] ?>"><?= esc($dep['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Должность</label>
              <select name="position_id" class="form-select" id="empPos">
                <option value="">— Не указана —</option>
                <?php foreach ($positions as $pos): ?>
                  <option value="<?= $pos['id'] ?>"><?= esc($pos['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Телефон</label>
              <input name="phone" type="text" class="form-control" id="empPhone">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" id="empEmail">
            </div>
            <div class="col-12">
              <label class="form-label">Заметка</label>
              <textarea name="note" class="form-control" rows="3" id="empNote"></textarea>
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

<!-- Модалка: Просмотр сотрудника -->
<div class="modal fade" id="viewEmployeeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewEmpName">Сотрудник</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-5 text-muted">Табельный номер</dt>
          <dd class="col-7" id="vPersonnel">—</dd>

          <dt class="col-5 text-muted">Должность</dt>
          <dd class="col-7" id="vPosition">—</dd>

          <dt class="col-5 text-muted">Отдел</dt>
          <dd class="col-7" id="vDepartment">—</dd>

          <dt class="col-5 text-muted">Телефон</dt>
          <dd class="col-7" id="vPhone">—</dd>

          <dt class="col-5 text-muted">Email</dt>
          <dd class="col-7" id="vEmail">—</dd>

          <dt class="col-5 text-muted">Дата приёма</dt>
          <dd class="col-7" id="vHired">—</dd>

          <dt class="col-5 text-muted">Заметка</dt>
          <dd class="col-7" id="vNote">—</dd>
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
        Удалить сотрудника <strong id="deleteName"></strong>? Действие нельзя отменить.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
        <a href="#" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Удалить</a>
      </div>
    </div>
  </div>
</div>

<script>
  // Добавить / Редактировать сотрудника
  document.querySelectorAll('[data-bs-target="#employeeModal"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const mode = this.dataset.mode;
      const form = document.getElementById('employeeForm');

      if (mode === 'add') {
        document.getElementById('employeeModalTitle').textContent = 'Добавить сотрудника';
        form.action = '<?= base_url('/employees/store') ?>';
        form.reset();
        document.getElementById('empId').value = '';
      } else {
        document.getElementById('employeeModalTitle').textContent = 'Редактировать сотрудника';
        form.action = '<?= base_url('/employees/update') ?>';
        document.getElementById('empId').value = this.dataset.id;
        document.getElementById('empLastName').value = this.dataset.last;
        document.getElementById('empFirstName').value = this.dataset.first;
        document.getElementById('empSurname').value = this.dataset.surname;
        document.getElementById('empPersonnel').value = this.dataset.personnel;
        document.getElementById('empPhone').value = this.dataset.phone;
        document.getElementById('empEmail').value = this.dataset.email;
        document.getElementById('empHired').value = this.dataset.hired;
        document.getElementById('empDept').value = this.dataset.dept || '';
        document.getElementById('empPos').value = this.dataset.pos || '';
        document.getElementById('empNote').value = this.dataset.note;
      }
    });
  });

  // Просмотр сотрудника
  document.querySelectorAll('[data-bs-target="#viewEmployeeModal"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const d = this.dataset;
      document.getElementById('viewEmpName').textContent =
        d.last + ' ' + d.first + (d.surname ? ' ' + d.surname : '');
      document.getElementById('vPersonnel').textContent = d.personnel || '—';
      document.getElementById('vPosition').textContent = d.position || '—';
      document.getElementById('vDepartment').textContent = d.department || '—';
      document.getElementById('vPhone').textContent = d.phone || '—';
      document.getElementById('vEmail').textContent = d.email || '—';
      document.getElementById('vHired').textContent = d.hired || '—';
      document.getElementById('vNote').textContent = d.note || '—';
    });
  });

  // Удаление сотрудника
  document.querySelectorAll('[data-bs-target="#confirmDeleteModal"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.getElementById('deleteName').textContent = this.dataset.name;
      document.getElementById('deleteConfirmBtn').href =
        '<?= base_url('/employees/delete/') ?>' + this.dataset.id;
    });
  });

  // Сайдбар
  document.getElementById('toggleSidebar').addEventListener('click', function() {
    const sb = document.querySelector('.sidebar');
    sb.style.display = (sb.style.display === 'none') ? 'flex' : 'none';
  });
</script>