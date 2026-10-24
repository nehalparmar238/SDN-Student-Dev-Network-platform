<?php

// Escape a value before printing it in HTML.
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Build a URL for a page, action, and optional query parameters.
function url($page, $action = 'index', $params = array())
{
    $query = array_merge(
        array('page' => $page, 'action' => $action),
        $params
    );

    return BASE_URL . '/index.php?' . http_build_query($query);
}

// Build a URL for a file in the public assets folder.
function asset($path)
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

// Check whether a user is signed in.
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Return the signed-in user's ID or null for a guest.
function currentUserId()
{
    return isLoggedIn() ? (int) $_SESSION['user_id'] : null;
}

// Return the signed-in user's display name or an empty string.
function currentUserName()
{
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
}

// Check whether the signed-in user has the admin role.
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Store a one-time message for the next page.
function setFlash($type, $message)
{
    $_SESSION['flash'] = array(
        'type' => $type,
        'message' => $message
    );
}

// Return the one-time message and remove it from the session.
function getFlash()
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

// Return the current session's CSRF token, creating one if needed.
function csrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Return a hidden form field containing the CSRF token.
function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

// Stop the request when its submitted CSRF token is invalid.
function verifyCsrf()
{
    $submittedToken = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';

    if (
        !isset($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $submittedToken)
    ) {
        http_response_code(403);
        exit('Invalid security token. Please go back and try again.');
    }
}

// Format a date and time for display.
function formatDate($datetime)
{
    $timestamp = strtotime((string) $datetime);

    if ($timestamp === false) {
        return '';
    }

    return date('d M Y, h:i A', $timestamp);
}