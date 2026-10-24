<section class="page-heading">
    <p class="eyebrow">Projects</p>
    <h1>Applications to My Projects</h1>
    <p class="muted">Review join requests submitted to projects you own.</p>
</section>

<?php if (count($projects) > 0) { ?>
    <?php foreach ($projects as $project) { ?>
        <section class="content-section application-project">
            <h2>
                <a href="<?= e(url(
                    'projects',
                    'view',
                    array('id' => (int) $project['project_id'])
                )) ?>"><?= e($project['project_title']) ?></a>
            </h2>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Applicant</th>
                            <th scope="col">Course</th>
                            <th scope="col">Message</th>
                            <th scope="col">Applied</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($project['applications'] as $application) { ?>
                            <tr>
                                <td><?= e($application['applicant_name']) ?></td>
                                <td><?= e($application['applicant_course'] ?? '') ?></td>
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
                                            <form
                                                method="post"
                                                action="<?= e(url('applications', 'accept')) ?>"
                                            >
                                                <?= csrfField() ?>
                                                <input
                                                    type="hidden"
                                                    name="application_id"
                                                    value="<?= (int) $application['application_id'] ?>"
                                                >
                                                <button type="submit">Accept</button>
                                            </form>
                                            <form
                                                method="post"
                                                action="<?= e(url('applications', 'reject')) ?>"
                                                data-confirm="Reject this application?"
                                            >
                                                <?= csrfField() ?>
                                                <input
                                                    type="hidden"
                                                    name="application_id"
                                                    value="<?= (int) $application['application_id'] ?>"
                                                >
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
        </section>
    <?php } ?>
<?php } else { ?>
    <p class="empty-state">Nothing found.</p>
<?php } ?>