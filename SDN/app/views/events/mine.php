<section class="page-heading">
    <p class="eyebrow">Your activity</p>
    <h1>My Events</h1>
    <p class="muted">Events you have registered for.</p>
</section>

<?php if (count($events) > 0) { ?>
    <section class="card-grid" aria-label="Your registered events">
        <?php foreach ($events as $event) { ?>
            <article class="card event-card">
                <p class="eyebrow"><?= e(ucfirst($event['event_type'])) ?></p>
                <h2>
                    <a href="<?= e(url(
                        'events',
                        'view',
                        array('id' => (int) $event['event_id'])
                    )) ?>"><?= e($event['title']) ?></a>
                </h2>
                <p class="muted"><?= e(formatDate($event['event_date'])) ?></p>
                <p>Registered <?= e(formatDate($event['registered_on'])) ?></p>
                <?php if ($event['is_future']) { ?>
                    <form
                        method="post"
                        action="<?= e(url('events', 'unregister')) ?>"
                        data-confirm="Remove your registration for this event?"
                    >
                        <?= csrfField() ?>
                        <input type="hidden" name="event_id" value="<?= (int) $event['event_id'] ?>">
                        <button class="button-danger" type="submit">Unregister</button>
                    </form>
                <?php } else { ?>
                    <p class="muted">This event has passed.</p>
                <?php } ?>
            </article>
        <?php } ?>
    </section>
<?php } else { ?>
    <p class="empty-state">Nothing found.</p>
<?php } ?>