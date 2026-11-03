<section class="page-heading">
    <p class="eyebrow">Administration</p>
    <h1>Admin Panel</h1>
    <p class="muted">Manage users, projects, events, categories, and join applications.</p>
</section>

<section class="dashboard-grid" aria-label="Admin overview">
    <a class="dashboard-card" href="<?= e(url('admin', 'users')) ?>">
        <span class="dashboard-count"><?= (int) $counts['users'] ?></span>
        <span class="dashboard-label">Users</span>
        <span class="dashboard-link">Manage user accounts and roles</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('admin', 'projects')) ?>">
        <span class="dashboard-count"><?= (int) $counts['projects'] ?></span>
        <span class="dashboard-label">Projects</span>
        <span class="dashboard-link">Manage all community projects</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('admin', 'events')) ?>">
        <span class="dashboard-count"><?= (int) $counts['events'] ?></span>
        <span class="dashboard-label">Events</span>
        <span class="dashboard-link">Create and manage events</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('admin', 'applications')) ?>">
        <span class="dashboard-count"><?= (int) $counts['pending_applications'] ?></span>
        <span class="dashboard-label">Pending applications</span>
        <span class="dashboard-link">Review project applications</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('admin', 'categories')) ?>">
        <span class="dashboard-count"><?= (int) $counts['categories'] ?></span>
        <span class="dashboard-label">Categories</span>
        <span class="dashboard-link">Manage project categories</span>
    </a>
</section>