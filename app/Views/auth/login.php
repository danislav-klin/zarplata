
<div class="auth-wrapper">
    <div class="card card-auth">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <i class="bi bi-speedometer2 fs-1 text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-0">Вход в систему</h4>
                    <div class="small-muted">Введите свои учётные данные</div>
                </div>
            </div>

            <!-- Flash / Errors -->
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
            <?php endif; ?>

            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err): ?>
                        <div><?= esc($err) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="post" action="<?= base_url('login') ?>" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Email или логин</label>
                    <input name="login" type="text" class="form-control" id="loginLogin" value="<?= esc(old('login')) ?>" required>
                    <div class="invalid-feedback">Укажите email или логин.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <div class="input-group">
                        <input name="password" type="password" class="form-control" id="loginPassword" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePwd"><i class="bi bi-eye"></i></button>
                        <div class="invalid-feedback">Укажите пароль.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input name="remember" class="form-check-input" type="checkbox" id="rememberMe" <?= old('remember') ? 'checked' : '' ?>>
                        <label class="form-check-label small-muted" for="rememberMe">Запомнить меня</label>
                    </div>
                    <div><a href="#" class="small-muted">Забыли пароль?</a></div>
                </div>

                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary" id="submitLogin">Войти</button>
                </div>
                <div class="text-center small-muted">Нет аккаунта? <a href="<?= base_url('/register') ?>">Зарегистрироваться</a></div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePwd').addEventListener('click', function() {
        const pwd = document.getElementById('loginPassword');
        const icon = this.querySelector('i');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>