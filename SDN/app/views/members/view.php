<section class="page-heading">
    <p class="eyebrow">SDN member</p>
    <h1><?= e($member['name']) ?></h1>
    <p class="muted"><?= e($member['college_course'] ?? '') ?></p>
</section>

<section class="profile-panel">
    <dl class="profile-details">
        <div>
            <dt>Skills</dt>
            <dd><?= e($member['skills'] ?? '') ?: '<span class="muted">Not added</span>' ?></dd>
        </div>
        <div class="profile-bio">
            <dt>About</dt>
            <dd><?= nl2br(e($member['bio'] ?? '')) ?: '<span class="muted">Not added</span>' ?></dd>
        </div>
    </dl>
</section>

<section class="content-section" aria-labelledby="member-projects-heading">
    <div class="section-heading">
        <div>
            <p class="eyebrow">What they are building</p>
            <h2 id="member-projects-heading">Open projects</h2>
        </div>
    </div>

    <?php if (count($projects) > 0) { ?>
        <div class="card-grid">
            <?php foreach ($projects as $project) { ?>
                <article class="card">
                    <p class="eyebrow"><?= e($project['category_name'] ?? '') ?></p>
                    <h3>
                        <a href="<?= e(url('projects', 'view', array('id' => (int) $project['project_id']))) ?>">
                            <?= e($project['title']) ?>
                        </a>
                    </h3>
                    <p><?= e($project['description'] ?? '') ?></p>
                    <p class="muted"><?= e($project['tech_stack'] ?? '') ?></p>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="empty-state">This member has no open projects right now.</p>
    <?php } ?>
</section>