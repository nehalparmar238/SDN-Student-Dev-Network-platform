<section class="page-heading">
    <p class="eyebrow">Administration</p>
    <h1>Project Categories</h1>
    <p class="muted">Add, edit, and remove categories used to organize projects.</p>
    <a class="button button-secondary" href="<?= e(url('admin')) ?>">Back to Admin Panel</a>
</section>

<section class="auth-card admin-category-form">
    <h2>Add a category</h2>

    <?php if (count($errors) > 0) { ?>
        <div class="form-errors" role="alert">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= e($error) ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <form method="post" action="<?= e(url('admin', 'categoryCreate')) ?>">
        <?= csrfField() ?>

        <div class="form-group">
            <label for="category-name">Category name</label>
            <input
                id="category-name"
                name="category_name"
                type="text"
                maxlength="80"
                required
                value="<?= e($formData['category_name']) ?>"
            >
        </div>

        <div class="form-group">
            <label for="category-description">Description (optional)</label>
            <input
                id="category-description"
                name="description"
                type="text"
                maxlength="255"
                value="<?= e($formData['description']) ?>"
            >
        </div>

        <button type="submit">Add category</button>
    </form>
</section>

<section class="content-section" aria-labelledby="categories-heading">
    <h2 id="categories-heading">All categories</h2>

    <?php if (count($categories) > 0) { ?>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Category</th>
                        <th scope="col">Description</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) { ?>
                        <tr>
                            <td><?= e($category['category_name']) ?></td>
                            <td><?= e($category['description'] ?? '') ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= e(url(
                                        'admin',
                                        'categoryEdit',
                                        array('id' => (int) $category['category_id'])
                                    )) ?>">Edit</a>

                                    <form
                                        method="post"
                                        action="<?= e(url(
                                            'admin',
                                            'categoryDelete',
                                            array('id' => (int) $category['category_id'])
                                        )) ?>"
                                        data-confirm="Delete this category?"
                                    >
                                        <?= csrfField() ?>
                                        <input type="hidden" name="category_id" value="<?= (int) $category['category_id'] ?>">
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