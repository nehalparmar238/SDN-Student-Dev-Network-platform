<section class="page-heading">
    <p class="eyebrow"><?= e(ucfirst($event['event_type'])) ?></p>
    <h1><?= e($event['title']) ?></h1>
    <p class="muted"><?= e(formatDate($event['event_date'])) ?></p>
</section>

<section class="profile-panel event-details">
    <dl class="profile-details">
        <div>
            <dt>Event type</dt>
            <dd><?= e(ucfirst($event['event_type'])) ?></dd>
        </div>
        <div>
            <dt>Date and time</dt>
            <dd><?= e(formatDate($event['event_date'])) ?></dd>
        </div>
        <div>
            <dt>Venue</dt>
            <dd><?= e($event['venue'] ?? '') ?: '<span class="muted">Not specified</span>' ?></dd>
        </div>
        <div>
            <dt>Organizer</dt>
            <dd><?= e($event['creator_name']) ?></dd>
        </div>
        <div>
            <dt>Capacity</dt>
            <dd>
                <?php if ($event['capacity'] === null) { ?>
                    Unlimited
                <?php } else { ?>
                    <?= (int) $event['capacity'] ?>
                    total · <?= (int) $spotsLeft ?> spots left
                <?php } ?>
            </dd>
        </div>
        <div class="profile-bio">
            <dt>Description</dt>
            <dd><?= nl2br(e($event['description'] ?? '')) ?: '<span class="muted">No description provided.</span>' ?></dd>
        </div>
    </dl>

    <?php if (!isLoggedIn() && $isFuture && !$isFull) { ?>
        <p>
            <a href="<?= e(url('auth', 'login')) ?>">Log in</a>
            to register for this event.
        </p>
    <?php } elseif (isLoggedIn() && $isRegistered) { ?>
        <p class="status-badge status-accepted">You are registered</p>
        <?php if ($isFuture) { ?>
            <form
                method="post"
                action="<?= e(url('events', 'unregister')) ?>"
                data-confirm="Remove your registration for this event?"
            >
                <?= csrfField() ?>
                <input type="hidden" name="event_id" value="<?= (int) $event['event_id'] ?>">
                <button class="button-danger" type="submit">Unregister</button>
            </form>
        <?php } ?>
    <?php } elseif ($isFuture && !$isFull) { ?>
        <form method="post" action="<?= e(url('events', 'register')) ?>">
            <?= csrfField() ?>
            <input type="hidden" name="event_id" value="<?= (int) $event['event_id'] ?>">
            <button type="submit">Register</button>
        </form>
    <?php } elseif ($isFull) { ?>
        <p class="empty-state">This event is full.</p>
    <?php } else { ?>
        <p class="muted">Registration is closed for this event.</p>
    <?php } ?>
</section>