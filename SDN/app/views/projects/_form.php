<?php if (count($errors) > 0) { ?>
    <div class="form-errors" role="alert">
        <ul>
            <?php foreach ($errors as $error) { ?>
                <li><?= e($error) ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if (count($categories) === 0) { ?>
    <p class="empty-state">No categories are available yet. Ask an administrator to add a project category.</p>
<?php } ?>

<form
    method="post"
    action="<?= e($isEdit
        ? url('projects', 'edit', array('id' => (int) $projectId))
        : url('projects', 'create')) ?>"
>
    <?= csrfField() ?>

    <div class="form-group">
        <label for="project-title">Title</label>
        <input id="project-title" name="title" type="text" maxlength="120" required value="<?= e($formData['title']) ?>">
    </div>

    <div class="form-group">
        <label for="project-category">Category</label>
        <select id="project-category" name="category_id" required>
            <option value="">Choose a category</option>
            <?php foreach ($categories as $category) { ?>
                <option
                    value="<?= (int) $category['category_id'] ?>"
                    <?= (int) $formData['category_id'] === (int) $category['category_id'] ? 'selected' : '' ?>
                ><?= e($category['category_name']) ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="project-description">Description</label>
        <textarea id="project-description" name="description"><?= e($formData['description']) ?></textarea>
    </div>

    <div class="form-group">
        <label for="project-tech-stack">Technology stack</label>
        <input id="project-tech-stack" name="tech_stack" type="text" maxlength="255" value="<?= e($formData['tech_stack']) ?>">
    </div>

    <div class="form-group">
        <label for="project-team-size">Team size needed (optional)</label>
        <input id="project-team-size" name="team_size_needed" type="number" min="1" max="50" step="1" value="<?= e($formData['team_size_needed']) ?>">
    </div>

    <?php if ($isEdit) { ?>
        <div class="form-group">
            <label for="project-status">Status</label>
            <select id="project-status" name="status" required>
                <option value="open" <?= $formData['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                <option value="in_progress" <?= $formData['status'] === 'in_progress' ? 'selected' : '' ?>>In progress</option>
                <option value="closed" <?= $formData['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>
    <?php } ?>

    <div class="hero-actions">
        <button type="submit" <?= count($categories) === 0 ? 'disabled' : '' ?>>
            <?= $isEdit ? 'Save changes' : 'Create project' ?>
        </button>
        <a class="button button-secondary" href="<?= e($isEdit
            ? url('projects', 'view', array('id' => (int) $projectId))
            : url('projects')) ?>">Cancel</a>
    </div>
</form>