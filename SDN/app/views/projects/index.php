<section class="page-heading">
    <p class="eyebrow">Collaborate and build</p>
    <h1>Projects</h1>
    <p class="muted">Browse student technology projects and filter by keyword, category, or tech stack.</p>
    <?php if (isLoggedIn()) { ?>
        <div class="hero-actions">
            <a class="button" href="<?= e(url('projects', 'create')) ?>">Create a project</a>
            <a class="button button-secondary" href="<?= e(url('projects', 'mine')) ?>">My projects</a>
        </div>
    <?php } ?>
</section>

<form class="project-filter-form" method="get" action="<?= e(BASE_URL . '/index.php') ?>">
    <input type="hidden" name="page" value="projects">
    <input type="hidden" name="action" value="index">

    <div class="form-group">
        <label for="filter-q">Keyword</label>
        <input
            id="filter-q"
            name="q"
            type="search"
            maxlength="120"
            value="<?= e($filters['q']) ?>"
            placeholder="Title or description"
        >
    </div>

    <div class="form-group">
        <label for="filter-category">Category</label>
        <select id="filter-category" name="category">
            <option value="0">All categories</option>
            <?php foreach ($categories as $category) { ?>
                <option
                    value="<?= (int) $category['category_id'] ?>"
                    <?= (int) $filters['category_id'] === (int) $category['category_id'] ? 'selected' : '' ?>
                ><?= e($category['category_name']) ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="filter-tech">Technology</label>
        <input
            id="filter-tech"
            name="tech"
            type="text"
            maxlength="255"
            value="<?= e($filters['tech']) ?>"
            placeholder="e.g. PHP, Python, React"
        >
    </div>

    <div class="filter-actions">
        <button type="submit">Filter projects</button>
        <?php if ($filters['q'] !== '' || (int) $filters['category_id'] > 0 || $filters['tech'] !== '') { ?>
            <a href="<?= e(url('projects')) ?>">Clear filters</a>
        <?php } ?>
    </div>
</form>

<?php if (count($projects) > 0) { ?>
    <section class="card-grid project-grid" aria-label="Projects list">
        <?php foreach ($projects as $project) { ?>
            <article class="card project-card">
                <div class="project-card-heading">
                    <p class="eyebrow"><?= e($project['category_name']) ?></p>
                    <span class="status-badge status-<?= e($project['status']) ?>">
                        <?= e(ucwords(str_replace('_', ' ', $project['status']))) ?>
                    </span>
                </div>
                <h2>
                    <a href="<?= e(url('projects', 'view', array('id' => (int) $project['project_id']))) ?>">
                        <?= e($project['title']) ?>
                    </a>
                </h2>
                <p><?= e($project['description'] ?? '') ?></p>
                <?php if ($project['tech_stack'] !== null && $project['tech_stack'] !== '') { ?>
                    <p class="muted">Tech: <?= e($project['tech_stack']) ?></p>
                <?php } ?>
                <p class="project-owner">By <?= e($project['owner_name']) ?></p>
            </article>
        <?php } ?>
    </section>
<?php } else { ?>
    <p class="empty-state">Nothing found.</p>
<?php } ?>