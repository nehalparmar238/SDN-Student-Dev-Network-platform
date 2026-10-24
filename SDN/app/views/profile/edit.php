<section class="auth-card profile-edit-card">
    <p class="eyebrow">Member account</p>
    <h1>Edit Profile</h1>
    <p class="muted">Your email address and account type cannot be changed here.</p>

    <?php if (count($errors) > 0) { ?>
        <div class="form-errors" role="alert">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= e($error) ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <form method="post" action="<?= e(url('profile', 'edit')) ?>">
        <?= csrfField() ?>

        <div class="form-group">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" maxlength="100" required value="<?= e($formData['name']) ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" value="<?= e($email) ?>" disabled>
        </div>

        <div class="form-group">
            <label for="college_course">College / course</label>
            <input id="college_course" name="college_course" type="text" maxlength="150" value="<?= e($formData['college_course']) ?>">
        </div>

        <div class="form-group">
            <label for="skills">Skills</label>
            <input id="skills" name="skills" type="text" maxlength="255" value="<?= e($formData['skills']) ?>" placeholder="For example: PHP, UI design, Python">
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" maxlength="5000"><?= e($formData['bio']) ?></textarea>
        </div>

        <fieldset class="password-change">
            <legend>Optional password change</legend>
            <p class="muted">Leave both fields blank to keep your current password.</p>
            <div class="form-group">
                <label for="current_password">Current password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password">
            </div>
            <div class="form-group">
                <label for="new_password">New password</label>
                <input id="new_password" name="new_password" type="password" minlength="8" autocomplete="new-password">
            </div>
        </fieldset>

        <div class="hero-actions">
            <button type="submit">Save profile</button>
            <a class="button button-secondary" href="<?= e(url('profile')) ?>">Cancel</a>
        </div>
    </form>
</section>