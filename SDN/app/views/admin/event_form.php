<section class="auth-card project-form-card">
    <p class="eyebrow">Administration</p>
    <h1><?= $isEdit ? 'Edit Event' : 'Create Event' ?></h1>

    <?php if (count($errors) > 0) { ?>
        <div class="form-errors" role="alert">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= e($error) ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <form
        method="post"
        action="<?= e($isEdit
            ? url('admin', 'eventEdit', array('id' => (int) $eventId))
            : url('admin', 'eventCreate')) ?>"
    >
        <?= csrfField() ?>

        <div class="form-group">
            <label for="event-title">Title</label>
            <input
                id="event-title"
                name="title"
                type="text"
                maxlength="120"
                required
                value="<?= e($formData['title']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="event-type">Event type</label>
            <select id="event-type" name="event_type" required>
                <option value="event" <?= $formData['event_type'] === 'event' ? 'selected' : '' ?>>Event</option>
                <option value="workshop" <?= $formData['event_type'] === 'workshop' ? 'selected' : '' ?>>Workshop</option>
                <option value="hackathon" <?= $formData['event_type'] === 'hackathon' ? 'selected' : '' ?>>Hackathon</option>
            </select>
        </div>

        <div class="form-group">
            <label for="event-date">Date and time</label>
            <input
                id="event-date"
                name="event_date"
                type="datetime-local"
                required
                value="<?= e($formData['event_date']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="event-venue">Venue (optional)</label>
            <input
                id="event-venue"
                name="venue"
                type="text"
                maxlength="150"
                value="<?= e($formData['venue']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="event-capacity">Capacity (optional)</label>
            <input
                id="event-capacity"
                name="capacity"
                type="number"
                min="1"
                step="1"
                value="<?= e($formData['capacity']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="event-description">Description</label>
            <textarea id="event-description" name="description"><?= e($formData['description']) ?></textarea>
        </div>

        <div class="hero-actions">
            <button type="submit"><?= $isEdit ? 'Save changes' : 'Create event' ?></button>
            <a class="button button-secondary" href="<?= e(url('admin', 'events')) ?>">Cancel</a>
        </div>
    </form>
</section>