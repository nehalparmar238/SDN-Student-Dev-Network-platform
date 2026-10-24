<section class="auth-card admin-category-form">
    <p class="eyebrow">Administration</p>
    <h1>Edit Category</h1>

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
        action="<?= e(url('admin', 'categoryEdit', array('id' => (int) $categoryId))) ?>"
    >
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

        <div class="hero-actions">
            <button type="submit">Save category</button>
            <a class="button button-secondary" href="<?= e(url('admin', 'categories')) ?>">Cancel</a>
        </div>
    </form>
</section>