<section class="auth-card">
    <img
        class="auth-logo"
        src="<?= e(asset('images/logo.png')) ?>"
        alt="SDN Student Dev Network"
    >
    <h1>Create your account</h1>
    <p class="auth-intro muted">Join your college’s student technology community.</p>

    <?php if (count($errors) > 0) { ?>
        <div class="form-errors" role="alert">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= e($error) ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <form method="post" action="<?= e(url('auth', 'register')) ?>">
        <?= csrfField() ?>

        <div class="form-group">
            <label for="name">Name</label>
            <input
                id="name"
                name="name"
                type="text"
                maxlength="100"
                autocomplete="name"
                value="<?= e($formData['name']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                maxlength="120"
                autocomplete="email"
                value="<?= e($formData['email']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="college_course">College / course (optional)</label>
            <input
                id="college_course"
                name="college_course"
                type="text"
                maxlength="150"
                value="<?= e($formData['college_course']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                minlength="8"
                autocomplete="new-password"
                required
            >
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input
                id="confirm_password"
                name="confirm_password"
                type="password"
                minlength="8"
                autocomplete="new-password"
                required
            >
        </div>

        <button type="submit">Create account</button>
    </form>

    <p class="auth-switch">
        Already registered?
        <a href="<?= e(url('auth', 'login')) ?>">Log in</a>
    </p>
</section>