<section class="page-heading">
    <p class="eyebrow">Member account</p>
    <h1>My Profile</h1>
    <p class="muted">Your account details and information shared with the SDN community.</p>
</section>

<section class="profile-panel">
    <dl class="profile-details">
        <div>
            <dt>Name</dt>
            <dd><?= e($user['name']) ?></dd>
        </div>
        <div>
            <dt>Email</dt>
            <dd><?= e($user['email']) ?></dd>
        </div>
        <div>
            <dt>College / course</dt>
            <dd><?= e($user['college_course'] ?? '') ?: '<span class="muted">Not added</span>' ?></dd>
        </div>
        <div>
            <dt>Skills</dt>
            <dd><?= e($user['skills'] ?? '') ?: '<span class="muted">Not added</span>' ?></dd>
        </div>
        <div class="profile-bio">
            <dt>Bio</dt>
            <dd><?= nl2br(e($user['bio'] ?? '')) ?: '<span class="muted">Not added</span>' ?></dd>
        </div>
        <div>
            <dt>Account type</dt>
            <dd><?= e(ucfirst($user['role'])) ?></dd>
        </div>
    </dl>
    <a class="button" href="<?= e(url('profile', 'edit')) ?>">Edit profile</a>
</section>