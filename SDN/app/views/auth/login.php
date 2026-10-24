<section class="auth-card">
    <img
        class="auth-logo"
        src="<?= e(asset('images/logo.png')) ?>"
        alt="SDN Student Dev Network"
    >
    <h1>Log in to SDN</h1>
    <p class="auth-intro muted">Continue building with your college community.</p>

    <?php if (count($errors) > 0) { ?>
        <div class="form-errors" role="alert">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= e($error) ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <form method="post" action="<?= e(url('auth', 'login')) ?>">
        <?= csrfField() ?>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                maxlength="120"
                autocomplete="username"
                value="<?= e($formData['email']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
            >
        </div>

        <button type="submit">Log in</button>
    </form>

    <p class="auth-switch">
        New to SDN?
        <a href="<?= e(url('auth', 'register')) ?>">Create an account</a>
    </p>
</section>