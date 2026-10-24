<section class="page-heading">
    <p class="eyebrow">Administration</p>
    <h1>Manage Projects</h1>
    <p class="muted">Review all projects, update statuses, or remove projects.</p>
    <a class="button button-secondary" href="<?= e(url('admin')) ?>">Back to Admin Panel</a>
</section>

<section class="content-section" aria-labelledby="admin-projects-heading">
    <h2 id="admin-projects-heading">All projects</h2>

    <?php if (count($projects) > 0) { ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Category</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project) { ?>
                        <tr>
                            <td>
                                <a href="<?= e(url(
                                    'projects',
                                    'view',
                                    array('id' => (int) $project['project_id'])
                                )) ?>"><?= e($project['title']) ?></a>
                            </td>
                            <td><?= e($project['owner_name']) ?></td>
                            <td><?= e($project['category_name']) ?></td>
                            <td>
                                <span class="status-badge status-<?= e($project['status']) ?>">
                                    <?= e(ucwords(str_replace('_', ' ', $project['status']))) ?>
                                </span>
                            </td>
                            <td><?= e(formatDate($project['created_at'])) ?></td>
                            <td>
                                <div class="table-actions">
                                    <form method="post" action="<?= e(url('admin', 'projectStatus')) ?>">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="project_id" value="<?= (int) $project['project_id'] ?>">
                                        <label class="visually-hidden" for="status-<?= (int) $project['project_id'] ?>">Status</label>
                                        <select id="status-<?= (int) $project['project_id'] ?>" name="status">
                                            <option value="open" <?= $project['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                                            <option value="in_progress" <?= $project['status'] === 'in_progress' ? 'selected' : '' ?>>In progress</option>
                                            <option value="closed" <?= $project['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                        </select>
                                        <button class="button-secondary" type="submit">Update</button>
                                    </form>

                                    <a href="<?= e(url(
                                        'projects',
                                        'edit',
                                        array('id' => (int) $project['project_id'])
                                    )) ?>">Edit</a>

                                    <form
                                        method="post"
                                        action="<?= e(url('admin', 'projectDelete')) ?>"
                                        data-confirm="Delete this project?"
                                    >
                                        <?= csrfField() ?>
                                        <input type="hidden" name="project_id" value="<?= (int) $project['project_id'] ?>">
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