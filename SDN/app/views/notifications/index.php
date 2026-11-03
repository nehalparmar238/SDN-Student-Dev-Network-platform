<section class="page-heading">
    <p class="eyebrow">Updates</p>
    <h1>Notifications</h1>
    <p class="muted">Project application updates and other account alerts.</p>
</section>

<?php if (count($notifications) > 0) { ?>
    <section class="notification-list" aria-label="Your notifications">
        <?php foreach ($notifications as $notification) { ?>
            <?php $isUnread = (int) $notification['is_read'] === 0; ?>
            <article class="notification-item<?= $isUnread ? ' notification-unread' : '' ?>">
                <?php if ($isUnread) { ?>
                    <p class="notification-label">Unread</p>
                <?php } ?>
                <p><?= e($notification['message']) ?></p>
                <time datetime="<?= e($notification['created_at']) ?>">
                    <?= e(formatDate($notification['created_at'])) ?>
                </time>
            </article>
        <?php } ?>
    </section>
<?php } else { ?>
    <p class="empty-state">Nothing found.</p>
<?php } ?>