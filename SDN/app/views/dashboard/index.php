<section class="page-heading">
    <p class="eyebrow">Your SDN activity</p>
    <h1>Dashboard</h1>
    <p class="muted">A quick overview of your projects, applications, events, and messages.</p>
</section>

<section class="dashboard-grid" aria-label="Activity overview">
    <a class="dashboard-card" href="<?= e(url('projects', 'mine')) ?>">
        <span class="dashboard-count"><?= (int) $counts['my_projects'] ?></span>
        <span class="dashboard-label">My projects</span>
        <span class="dashboard-link">View your projects</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('applications', 'mine')) ?>">
        <span class="dashboard-count"><?= (int) $counts['my_applications'] ?></span>
        <span class="dashboard-label">My applications</span>
        <span class="dashboard-link">Track applications you sent</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('applications', 'received')) ?>">
        <span class="dashboard-count"><?= (int) $counts['waiting_applications'] ?></span>
        <span class="dashboard-label">Waiting on your projects</span>
        <span class="dashboard-link">Review pending applications</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('events', 'mine')) ?>">
        <span class="dashboard-count"><?= (int) $counts['upcoming_registrations'] ?></span>
        <span class="dashboard-label">Upcoming event registrations</span>
        <span class="dashboard-link">See events you joined</span>
    </a>
    <a class="dashboard-card" href="<?= e(url('notifications')) ?>">
        <span class="dashboard-count"><?= (int) $counts['unread_notifications'] ?></span>
        <span class="dashboard-label">Unread notifications</span>
        <span class="dashboard-link">Open your notifications</span>
    </a>
</section>