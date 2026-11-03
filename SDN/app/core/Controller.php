<?php

class Controller
{
    // Render a view between the shared header and footer.
    protected function render($view, $data = array())
    {
        $viewPath = APP_PATH . '/views/' . $view . '.php';

        if (!is_file($viewPath)) {
            http_response_code(404);
            exit('404 Not Found');
        }

        extract($data, EXTR_SKIP);

        require APP_PATH . '/views/layouts/header.php';
        require $viewPath;
        require APP_PATH . '/views/layouts/footer.php';
    }

    // Redirect to a page and stop processing the current request.
    protected function redirect($page, $action = 'index', $params = array())
    {
        header('Location: ' . url($page, $action, $params));
        exit;
    }

    // Redirect guests to the login page.
    protected function requireLogin()
    {
        if (!isLoggedIn()) {
            setFlash('error', 'Please log in to continue.');
            $this->redirect('auth', 'login');
        }
    }

    // Allow only administrators to continue.
    protected function requireAdmin()
    {
        $this->requireLogin();

        if (!isAdmin()) {
            http_response_code(403);
            exit('403 Forbidden');
        }
    }

    // Require a POST request and validate its CSRF token.
    protected function requirePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('405 Method Not Allowed');
        }

        verifyCsrf();
    }

    // Check whether the current request uses the POST method.
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}