<section class="hero">
    <img
        class="hero-logo"
        src="<?= e(asset('images/logo.png')) ?>"
        alt="SDN Student Dev Network"
    >
    <div class="hero-copy">
        <p class="eyebrow">A college technology community</p>
        <h1>Build what’s next, together.</h1>
        <p class="hero-description">
            Meet students who share your curiosity, find a team for your next idea,
            and take part in workshops, events, and hackathons.
        </p>
        <div class="hero-actions">
            <a class="button" href="<?= e(url('projects')) ?>">Browse projects</a>
            <a class="button button-secondary" href="<?= e(url('events')) ?>">Explore events</a>
        </div>
    </div>
</section>

<section class="content-section" aria-labelledby="projects-heading">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Make something real</p>
            <h2 id="projects-heading">Open projects</h2>
        </div>
        <a class="text-link" href="<?= e(url('projects')) ?>">Browse all projects</a>
    </div>

    <?php if (count($projects) > 0) { ?>
        <div class="card-grid">
            <?php foreach ($projects as $project) { ?>
                <article class="card project-card">
                    <p class="eyebrow"><?= e($project['category_name'] ?? '') ?></p>
                    <h3>
                        <a href="<?= e(url('projects', 'view', array('id' => (int) $project['project_id']))) ?>">
                            <?= e($project['title'] ?? '') ?>
                        </a>
                    </h3>
                    <p><?= e($project['description'] ?? '') ?></p>
                    <p class="muted"><?= e($project['tech_stack'] ?? '') ?></p>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="empty-state">Nothing found.</p>
    <?php } ?>
</section>

<section class="content-section" aria-labelledby="events-heading">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Learn and connect</p>
            <h2 id="events-heading">Upcoming events</h2>
        </div>
        <a class="text-link" href="<?= e(url('events')) ?>">Browse all events</a>
    </div>

    <?php if (count($events) > 0) { ?>
        <div class="card-grid">
            <?php foreach ($events as $event) { ?>
                <article class="card event-card">
                    <p class="eyebrow"><?= e(ucfirst($event['event_type'] ?? 'event')) ?></p>
                    <h3>
                        <a href="<?= e(url('events', 'view', array('id' => (int) $event['event_id']))) ?>">
                            <?= e($event['title'] ?? '') ?>
                        </a>
                    </h3>
                    <p><?= e($event['description'] ?? '') ?></p>
                    <p class="muted"><?= e(formatDate($event['event_date'] ?? '')) ?></p>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="empty-state">Nothing found.</p>
    <?php } ?>
</section>