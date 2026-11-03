<?php
$flash = getFlash();
$unreadNotificationCount = 0;

if (isLoggedIn()) {
    $notificationModel = new Notification();
    $unreadNotificationCount = $notificationModel->countUnread(currentUserId());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDN – Student Dev Network</title>
    <link rel="icon" href="<?= e(asset('images/logo.png')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
    <header class="site-header">
        <nav class="navbar container" aria-label="Main navigation">
            <a class="brand" href="<?= e(url('home')) ?>">
                <img class="nav-logo" src="<?= e(asset('images/logo.png')) ?>" alt="SDN Student Dev Network">
            </a>

            <button
                class="menu-toggle"
                type="button"
                aria-expanded="false"
                aria-controls="nav-links"
            >Menu</button>

            <div class="nav-links" id="nav-links">
                <?php if (!isLoggedIn()) { ?>
                    <a href="<?= e(url('home')) ?>">Home</a>
                    <a href="<?= e(url('projects')) ?>">Projects</a>
                    <a href="<?= e(url('events')) ?>">Events</a>
                    <a href="<?= e(url('auth', 'login')) ?>">Login</a>
                    <a href="<?= e(url('auth', 'register')) ?>">Register</a>
                <?php } else { ?>
                    <a href="<?= e(url('dashboard')) ?>">Dashboard</a>
                    <a href="<?= e(url('projects')) ?>">Projects</a>
                    <a href="<?= e(url('members')) ?>">Members</a>
                    <a href="<?= e(url('events')) ?>">Events</a>
                    <a href="<?= e(url('notifications')) ?>">
                        Notifications
                        <span class="notification-count"><?= (int) $unreadNotificationCount ?></span>
                    </a>
                    <a href="<?= e(url('profile')) ?>">My Profile</a>
                    <?php if (isAdmin()) { ?>
                        <a href="<?= e(url('admin')) ?>">Admin</a>
                    <?php } ?>
                    <form class="logout-form" method="post" action="<?= e(url('auth', 'logout')) ?>" data-confirm="Log out of SDN?">
                        <?= csrfField() ?>
                        <button class="nav-logout" type="submit">Logout</button>
                    </form>
                <?php } ?>
            </div>
        </nav>
    </header>

    <main class="site-main">
        <?php if ($flash !== null) { ?>
            <div class="flash-message flash-<?= e($flash['type']) ?>" role="status">
                <?= e($flash['message']) ?>
            </div>
        <?php } ?>