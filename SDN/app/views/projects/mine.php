<section class="page-heading">
    <p class="eyebrow">Your work</p>
    <h1>My Projects</h1>
    <p class="muted">Projects you have created.</p>
    <a class="button" href="<?= e(url('projects', 'create')) ?>">Create a project</a>
</section>

<?php if (count($projects) > 0) { ?>
    <div class="card-grid project-grid">
        <?php foreach ($projects as $project) { ?>
            <article class="card project-card">
                <p class="eyebrow"><?= e($project['category_name']) ?></p>
                <h2>
                    <a href="<?= e(url('projects', 'view', array('id' => (int) $project['project_id']))) ?>">
                        <?= e($project['title']) ?>
                    </a>
                </h2>
                <p>
                    <span class="status-badge status-<?= e($project['status']) ?>">
                        <?= e(ucwords(str_replace('_', ' ', $project['status']))) ?>
                    </span>
                </p>
                <p class="muted"><?= e(formatDate($project['created_at'])) ?></p>
                <div class="project-actions">
                    <a class="text-link" href="<?= e(url('projects', 'edit', array('id' => (int) $project['project_id']))) ?>">Edit project</a>

                    <form method="post" action="<?= e(url('projects', 'delete')) ?>"
                          data-confirm="Are you sure you want to permanently delete this project? Its applications will also be deleted. This cannot be undone.">
                        <?= csrfField() ?>
                        <input type="hidden" name="project_id" value="<?= (int) $project['project_id'] ?>">
                        <button class="button-danger" type="submit">Delete project</button>
                    </form>
                </div>
            </article>
        <?php } ?>
    </div>
<?php } else { ?>
    <p class="empty-state">Nothing found.</p>
<?php } ?>