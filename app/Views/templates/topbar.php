<?php $u = current_user(); ?>

<div class="topbar">
    <div class="d-flex align-items-center">
        <div>
            <h4 class="mb-0">Дашборд</h4>
            <div class="breadcrumbs">Главная / <?= esc($crumbs); ?></div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="me-2 text-end">
            <div style="font-size:14px">
                <?= esc(trim(($u['last_name'] ?? '') . ' ' . ($u['first_name'] ?? ''))) ?>
            </div>
            <div class="small text-muted"><?= esc($u['email'] ?? '') ?></div>
        </div>
        <button class="btn btn-sm btn-outline-secondary"
            data-bs-toggle="modal" data-bs-target="#modalEditProfile"
            title="Редактировать профиль">
            <i class="bi bi-person-gear"></i>
        </button>
        <a href="<?= base_url('/logout') ?>" class="btn btn-outline-danger btn-sm">Выйти</a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mt-2 mb-0" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-2 mb-0" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalEditProfile" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('/profile/update') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать профиль</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Фамилия <span class="text-danger">*</span></label>
                            <input name="last_name" type="text" class="form-control"
                                value="<?= esc(old('last_name', $u['last_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Имя <span class="text-danger">*</span></label>
                            <input name="first_name" type="text" class="form-control"
                                value="<?= esc(old('first_name', $u['first_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Отчество</label>
                            <input name="surname" type="text" class="form-control"
                                value="<?= esc(old('surname', $u['surname'] ?? '')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input name="email" type="email" class="form-control"
                                value="<?= esc(old('email', $u['email'] ?? '')) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Логин <span class="text-danger">*</span></label>
                            <input name="login" type="text" class="form-control"
                                value="<?= esc(old('login', $u['login'] ?? '')) ?>" required>
                        </div>
                    </div>
                    <hr>
                    <div class="text-muted small mb-2">Оставьте пустым, если не хотите менять пароль</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Новый пароль</label>
                            <input name="password" type="password" class="form-control"
                                autocomplete="new-password" placeholder="Минимум 6 символов">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Повторите пароль</label>
                            <input name="password_confirm" type="password" class="form-control"
                                autocomplete="new-password" placeholder="Повторите пароль">
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