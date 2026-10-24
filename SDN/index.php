<?php

require_once __DIR__ . '/config/config.php';
require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Project.php';
require_once APP_PATH . '/models/Application.php';
require_once APP_PATH . '/models/Notification.php';
require_once APP_PATH . '/models/Event.php';
require_once APP_PATH . '/models/EventRegistration.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$router = new Router();
$router->dispatch();