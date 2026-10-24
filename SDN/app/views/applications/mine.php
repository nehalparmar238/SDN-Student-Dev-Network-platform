<section class="page-heading">
    <p class="eyebrow">Projects</p>
    <h1>My Applications</h1>
    <p class="muted">Track your project join requests and withdraw pending applications.</p>
</section>

<section class="content-section" aria-labelledby="my-applications-heading">
    <h2 id="my-applications-heading">Applications you sent</h2>

    <?php if (count($applications) > 0) { ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Project</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Message</th>
                        <th scope="col">Applied</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
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
                            <td><?= e($application['message'] ?? '') ?></td>
                            <td><?= e(formatDate($application['applied_on'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= e($application['status']) ?>">
                                    <?= e(ucfirst($application['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($application['status'] === 'pending') { ?>
                                    <form
                                        method="post"
                                        action="<?= e(url('applications', 'withdraw')) ?>"
                                        data-confirm="Withdraw this application?"
                                    >
                                        <?= csrfField() ?>
                                        <input
                                            type="hidden"
                                            name="application_id"
                                            value="<?= (int) $application['application_id'] ?>"
                                        >
                                        <button class="button-danger" type="submit">Withdraw</button>
                                    </form>
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