<section class="page-heading">
    <p class="eyebrow"><?= e($project['category_name']) ?></p>
    <h1><?= e($project['title']) ?></h1>
    <p>
        <span class="status-badge status-<?= e($project['status']) ?>">
            <?= e(ucwords(str_replace('_', ' ', $project['status']))) ?>
        </span>
    </p>
</section>

<section class="profile-panel project-details">
    <dl class="profile-details">
        <div>
            <dt>Project owner</dt>
            <dd><?= e($project['owner_name']) ?></dd>
        </div>
        <div>
            <dt>Category</dt>
            <dd><?= e($project['category_name']) ?></dd>
        </div>
        <div>
            <dt>Technology stack</dt>
            <dd><?= e($project['tech_stack'] ?? '') ?: '<span class="muted">Not specified</span>' ?></dd>
        </div>
        <div>
            <dt>Team size needed</dt>
            <dd><?= $project['team_size_needed'] === null ? '<span class="muted">Not specified</span>' : (int) $project['team_size_needed'] ?></dd>
        </div>
        <div>
            <dt>Posted</dt>
            <dd><?= e(formatDate($project['created_at'])) ?></dd>
        </div>
        <div class="profile-bio">
            <dt>Description</dt>
            <dd><?= nl2br(e($project['description'] ?? '')) ?: '<span class="muted">No description provided.</span>' ?></dd>
        </div>
    </dl>

    <?php if (isLoggedIn() && ((int) $project['owner_id'] === currentUserId() || isAdmin())) { ?>
        <div class="project-actions">
            <a class="button" href="<?= e(url('projects', 'edit', array('id' => (int) $project['project_id']))) ?>">Edit project</a>

            <form method="post" action="<?= e(url('projects', 'delete')) ?>"
                  data-confirm="Are you sure you want to permanently delete this project? Its applications will also be deleted. This cannot be undone.">
                <?= csrfField() ?>
                <input type="hidden" name="project_id" value="<?= (int) $project['project_id'] ?>">
                <button class="button-danger" type="submit">Delete project</button>
            </form>
        </div>
    <?php } ?>
</section>

<section class="content-section" aria-labelledby="accepted-members-heading">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Project team</p>
            <h2 id="accepted-members-heading">Accepted members</h2>
        </div>
    </div>

    <?php if (count($acceptedMembers) > 0) { ?>
        <div class="card-grid">
            <?php foreach ($acceptedMembers as $member) { ?>
                <article class="card">
                    <h3><?= e($member['name']) ?></h3>
                    <p><?= e($member['college_course'] ?? '') ?></p>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="empty-state">Nothing found.</p>
    <?php } ?>
</section>

<?php if (!isLoggedIn() && $project['status'] === 'open') { ?>
    <p class="empty-state">
        <a href="<?= e(url('auth', 'login')) ?>">Log in</a> to apply to join an open project.
    </p>
<?php } elseif ($canApply) { ?>
    <section class="auth-card">
        <h2>Apply to join</h2>
        <form method="post" action="<?= e(url('applications', 'apply')) ?>">
            <?= csrfField() ?>
            <input type="hidden" name="project_id" value="<?= (int) $project['project_id'] ?>">
            <div class="form-group">
                <label for="application-message">Message to the project owner</label>
                <textarea id="application-message" name="message" maxlength="255"></textarea>
            </div>
            <button type="submit">Send application</button>
        </form>
    </section>
<?php } elseif ($applicationStatus !== null) { ?>
    <p class="empty-state">
        Your application status:
        <span class="status-badge status-<?= e($applicationStatus) ?>"><?= e(ucfirst($applicationStatus)) ?></span>
    </p>
<?php } ?>