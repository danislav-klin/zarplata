<div class="auth-wrapper-reg">
    <div class="card card-auth-reg">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3"><i class="bi bi-person-plus fs-1 text-primary"></i></div>
                <div>
                    <h4 class="mb-0">Регистрация</h4>
                    <div class="small-muted-reg">Создайте учётную запись</div>
                </div>
            </div>
            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err): ?>
                        <div><?= esc($err) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
            <?php endif; ?>

            <form id="registerForm" method="post" action="<?= base_url('/register') ?>" novalidate>
                <?= csrf_field() ?>

                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Фамилия *</label>
                        <input name="last_name" type="text" class="form-control" id="regLastName" value="<?= esc(old('last_name')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Имя *</label>
                        <input name="first_name" type="text" class="form-control" id="regFirstName" value="<?= esc(old('first_name')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Отчество</label>
                        <input name="surname" type="text" class="form-control" id="regSurname" value="<?= esc(old('surname')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Логин *</label>
                        <input name="login" type="text" class="form-control" id="regLogin" value="<?= esc(old('login')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input name="email" type="email" class="form-control" id="regEmail" value="<?= esc(old('email')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Пароль *</label>
                        <input name="password" type="password" class="form-control" id="regPassword" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Повторите пароль *</label>
                        <input name="password2" type="password" class="form-control" id="regPassword2" required>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="small-muted">Уже есть аккаунт? <a href="<?= base_url('/login') ?>">Войдите</a></div>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" id="cancelRegister">Отмена</button>
                        <button type="submit" class="btn btn-primary btn-sm">Зарегистрироваться</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.getElementById('cancelRegister').addEventListener('click', function() {
        window.location.href = '<?= base_url('/login') ?>';
    });

    // Небольшая клиентская проверка совпадения паролей для UX (сервер всё равно проверит)
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const pwd = document.getElementById('regPassword').value;
        const pwd2 = document.getElementById('regPassword2').value;
        if (pwd !== pwd2) {
            e.preventDefault();
            alert('Пароли не совпадают');
            return false;
        }
    });
</script>