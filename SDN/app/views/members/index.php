<section class="page-heading">
    <p class="eyebrow">Meet the community</p>
    <h1>Member Directory</h1>
    <p class="muted">Find students by name or skills and explore what they are working on.</p>
</section>

<form class="directory-search" method="get" action="<?= e(BASE_URL . '/index.php') ?>">
    <input type="hidden" name="page" value="members">
    <label class="visually-hidden" for="member-search">Search by name or skill</label>
    <input
        id="member-search"
        name="q"
        type="search"
        maxlength="100"
        value="<?= e($searchTerm) ?>"
        placeholder="Search names or skills"
    >
    <button type="submit">Search</button>
</form>

<?php if (count($members) > 0) { ?>
    <section class="card-grid member-grid" aria-label="Members">
        <?php foreach ($members as $member) { ?>
            <article class="card member-card">
                <p class="eyebrow"><?= e($member['college_course'] ?? '') ?: 'SDN member' ?></p>
                <h2>
                    <a href="<?= e(url('members', 'view', array('id' => (int) $member['user_id']))) ?>">
                        <?= e($member['name']) ?>
                    </a>
                </h2>
                <p><?= e($member['skills'] ?? '') ?: '<span class="muted">Skills not added</span>' ?></p>
                <?php if (!empty($member['bio'])) { ?>
                    <p><?= e($member['bio']) ?></p>
                <?php } ?>
                <a class="text-link" href="<?= e(url('members', 'view', array('id' => (int) $member['user_id']))) ?>">View profile</a>
            </article>
        <?php } ?>
    </section>
<?php } else { ?>
    <p class="empty-state">
        <?= $searchTerm === '' ? 'No members found.' : 'No members matched that search.' ?>
    </p>
<?php } ?>