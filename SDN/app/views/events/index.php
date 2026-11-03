<section class="page-heading">
    <p class="eyebrow">Learn and connect</p>
    <h1>Events</h1>
    <p class="muted">Find events, workshops, and hackathons in the SDN community.</p>
</section>

<form method="get" action="<?= e(url('events')) ?>">
    <div class="form-group">
        <label for="event-type">Filter by type</label>
        <select id="event-type" name="type">
            <option value="">All types</option>
            <option value="event" <?= $eventType === 'event' ? 'selected' : '' ?>>Event</option>
            <option value="workshop" <?= $eventType === 'workshop' ? 'selected' : '' ?>>Workshop</option>
            <option value="hackathon" <?= $eventType === 'hackathon' ? 'selected' : '' ?>>Hackathon</option>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit">Filter events</button>
        <?php if ($eventType !== '') { ?>
            <a href="<?= e(url('events')) ?>">Clear filter</a>
        <?php } ?>
    </div>
</form>

<section class="content-section" aria-labelledby="upcoming-events-heading">
    <h2 id="upcoming-events-heading">Upcoming</h2>

    <?php if (count($upcomingEvents) > 0) { ?>
        <div class="card-grid">
            <?php foreach ($upcomingEvents as $event) { ?>
                <article class="card event-card">
                    <p class="eyebrow"><?= e(ucfirst($event['event_type'])) ?></p>
                    <h3>
                        <a href="<?= e(url(
                            'events',
                            'view',
                            array('id' => (int) $event['event_id'])
                        )) ?>"><?= e($event['title']) ?></a>
                    </h3>
                    <p><?= e($event['description'] ?? '') ?></p>
                    <p class="muted"><?= e(formatDate($event['event_date'])) ?></p>
                    <?php if ($event['venue'] !== null && $event['venue'] !== '') { ?>
                        <p class="muted"><?= e($event['venue']) ?></p>
                    <?php } ?>
                    <?php if ($event['capacity'] !== null) { ?>
                        <p class="muted">
                            <?= max(0, (int) $event['capacity'] - (int) $event['registration_count']) ?>
                            spots left
                        </p>
                    <?php } ?>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="empty-state">Nothing found.</p>
    <?php } ?>
</section>

<section class="content-section" aria-labelledby="past-events-heading">
    <h2 id="past-events-heading">Past</h2>

    <?php if (count($pastEvents) > 0) { ?>
        <div class="card-grid">
            <?php foreach ($pastEvents as $event) { ?>
                <article class="card event-card">
                    <p class="eyebrow"><?= e(ucfirst($event['event_type'])) ?></p>
                    <h3>
                        <a href="<?= e(url(
                            'events',
                            'view',
                            array('id' => (int) $event['event_id'])
                        )) ?>"><?= e($event['title']) ?></a>
                    </h3>
                    <p><?= e($event['description'] ?? '') ?></p>
                    <p class="muted"><?= e(formatDate($event['event_date'])) ?></p>
                    <?php if ($event['venue'] !== null && $event['venue'] !== '') { ?>
                        <p class="muted"><?= e($event['venue']) ?></p>
                    <?php } ?>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="empty-state">Nothing found.</p>
    <?php } ?>
</section>