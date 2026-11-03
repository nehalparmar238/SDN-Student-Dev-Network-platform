<section class="page-heading">
    <p class="eyebrow">Administration</p>
    <h1>Manage Applications</h1>
    <p class="muted">View all project join applications across the platform.</p>
    <a class="button button-secondary" href="<?= e(url('admin')) ?>">Back to Admin Panel</a>
</section>

<section class="content-section" aria-labelledby="admin-applications-heading">
    <h2 id="admin-applications-heading">All applications</h2>

    <?php if (count($applications) > 0) { ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Project</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Applicant</th>
                        <th scope="col">Message</th>
                        <th scope="col">Applied</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application) { ?>
                        <tr>
                            <td>
                                <a href="<?= e(url(
                                    'projects',
                                    'view',
                                    array('id' => (int) $application['project_id'])
                                )) ?>"><?= e($application['project_title']) ?></a>
                            </td>
                            <td><?= e($application['owner_name']) ?></td>
                            <td><?= e($application['applicant_name']) ?></td>
                            <td><?= e($application['message'] ?? '') ?></td>
                            <td><?= e(formatDate($application['applied_on'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= e($application['status']) ?>">
                                    <?= e(ucfirst($application['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($application['status'] === 'pending') { ?>
                                    <div class="table-actions">
                                        <form method="post" action="<?= e(url('admin', 'applicationDecide')) ?>">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="application_id" value="<?= (int) $application['application_id'] ?>">
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit">Accept</button>
                                        </form>

                                        <form
                                            method="post"
                                            action="<?= e(url('admin', 'applicationDecide')) ?>"
                                            data-confirm="Reject this application?"
                                        >
                                            <?= csrfField() ?>
                                            <input type="hidden" name="application_id" value="<?= (int) $application['application_id'] ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="button-danger" type="submit">Reject</button>
                                        </form>
                                    </div>
                                <?php } else { ?>
                                    <span class="muted">No action</span>
                                <?php } ?>
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