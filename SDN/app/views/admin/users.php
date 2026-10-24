<section class="page-heading">
    <p class="eyebrow">Administration</p>
    <h1>Manage Users</h1>
    <p class="muted">View all registered users, switch roles, or remove accounts.</p>
    <a class="button button-secondary" href="<?= e(url('admin')) ?>">Back to Admin Panel</a>
</section>

<section class="content-section" aria-labelledby="users-heading">
    <h2 id="users-heading">All users</h2>

    <?php if (count($users) > 0) { ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Course</th>
                        <th scope="col">Role</th>
                        <th scope="col">Joined</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?= e($user['name']) ?></td>
                            <td><?= e($user['email']) ?></td>
                            <td><?= e($user['college_course'] ?? '') ?></td>
                            <td><?= e(ucfirst($user['role'])) ?></td>
                            <td><?= e(formatDate($user['created_at'])) ?></td>
                            <td>
                                <?php if ((int) $user['user_id'] === currentUserId()) { ?>
                                    <span class="muted">Current admin</span>
                                <?php } else { ?>
                                    <div class="table-actions">
                                        <form method="post" action="<?= e(url('admin', 'userRole')) ?>">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                            <input
                                                type="hidden"
                                                name="role"
                                                value="<?= $user['role'] === 'admin' ? 'member' : 'admin' ?>"
                                            >
                                            <button class="button-secondary" type="submit">
                                                Make <?= $user['role'] === 'admin' ? 'Member' : 'Admin' ?>
                                            </button>
                                        </form>

                                        <form
                                            method="post"
                                            action="<?= e(url('admin', 'userDelete')) ?>"
                                            data-confirm="Delete this user account?"
                                        >
                                            <?= csrfField() ?>
                                            <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                            <button class="button-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
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