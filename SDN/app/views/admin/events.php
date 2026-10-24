<section class="page-heading">
    <p class="eyebrow">Administration</p>
    <h1>Manage Events</h1>
    <p class="muted">Create and manage community events, workshops, and hackathons.</p>
    <div class="hero-actions">
        <a class="button" href="<?= e(url('admin', 'eventCreate')) ?>">Create Event</a>
        <a class="button button-secondary" href="<?= e(url('admin')) ?>">Back to Admin Panel</a>
    </div>
</section>

<section class="content-section" aria-labelledby="admin-events-heading">
    <h2 id="admin-events-heading">All events</h2>

    <?php if (count($events) > 0) { ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Date</th>
                        <th scope="col">Venue</th>
                        <th scope="col">Registrations</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event) { ?>
                        <tr>
                            <td>
                                <a href="<?= e(url(
                                    'events',
                                    'view',
                                    array('id' => (int) $event['event_id'])
                                )) ?>"><?= e($event['title']) ?></a>
                            </td>
                            <td><?= e(ucfirst($event['event_type'])) ?></td>
                            <td><?= e(formatDate($event['event_date'])) ?></td>
                            <td><?= e($event['venue'] ?? '') ?></td>
                            <td>
                                <?= (int) $event['registration_count'] ?>
                                <?php if ($event['capacity'] !== null) { ?>
                                    / <?= (int) $event['capacity'] ?>
                                <?php } else { ?>
                                    (Unlimited)
                                <?php } ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= e(url(
                                        'admin',
                                        'eventEdit',
                                        array('id' => (int) $event['event_id'])
                                    )) ?>">Edit</a>

                                    <form
                                        method="post"
                                        action="<?= e(url('admin', 'eventDelete')) ?>"
                                        data-confirm="Delete this event?"
                                    >
                                        <?= csrfField() ?>
                                        <input type="hidden" name="event_id" value="<?= (int) $event['event_id'] ?>">
                                        <button class="button-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="empty-state">Nothing found.</p>
    <?php } ?>
</section>