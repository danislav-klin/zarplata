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

            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><?= esc($department['name']) ?></h5>
                <a href="<?= base_url('/department -list') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
            </div>

            <!-- Информация об отделе -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted">Название:</dt>
                                <dd class="col-sm-8 fw-medium"><?= esc($department['name']) ?></dd>

                                <dt class="col-sm-4 text-muted">Описание:</dt>
                                <dd class="col-sm-8"><?= esc($department['description'] ?? '—') ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted">Статус:</dt>
                                <dd class="col-sm-8">
                                    <?php if ($department['is_active']): ?>
                                        <span class="badge bg-success">Активен</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Неактивен</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-4 text-muted">Создан:</dt>
                                <dd class="col-sm-8"><?= date('d.m.Y H:i', strtotime($department['created_at'])) ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Сотрудники отдела -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Сотрудники отдела (<?= count($employees) ?>)</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($employees)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-person-x fs-3 d-block mb-2"></i>
                            В отделе пока нет сотрудников
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ФИО</th>
                                        <th>Должность</th>
                                        <th>Телефон</th>
                                        <th>Email</th>
                                        <th>Дата приёма</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employees as $emp): ?>
                                        <tr>
                                            <td class="fw-medium">
                                                <?= esc($emp['last_name'] . ' ' . $emp['first_name']) ?>
                                                <?php if (!empty($emp['surname'])): ?>
                                                    <?= esc($emp['surname']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($emp['position_name'] ?? '—') ?></td>
                                            <td class="text-muted small"><?= esc($emp['phone'] ?? '—') ?></td>
                                            <td class="text-muted small"><?= esc($emp['email'] ?? '—') ?></td>
                                            <td class="text-muted small">
                                                <?= $emp['hired_at'] ? date('d.m.Y', strtotime($emp['hired_at'])) : '—' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
</script>
