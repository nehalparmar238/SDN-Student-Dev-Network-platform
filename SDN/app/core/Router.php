<?php

class Router
{
    // Dispatch a valid page and action to its controller.
    public function dispatch()
    {
        $page = isset($_GET['page']) ? (string) $_GET['page'] : 'home';
        $rawAction = isset($_GET['action']) ? (string) $_GET['action'] : 'index';
        $action = strtolower($rawAction);

        if (!preg_match('/^[a-z_]+$/', $page) || !preg_match('/^[a-z_]+$/', $action)) {
            $this->showNotFound();
            return;
        }

        $controllers = array(
            'home' => 'HomeController',
            'auth' => 'AuthController',
            'dashboard' => 'DashboardController',
            'profile' => 'ProfileController',
            'members' => 'MemberController',
            'projects' => 'ProjectController',
            'applications' => 'ApplicationController',
            'events' => 'EventController',
            'notifications' => 'NotificationController',
            'admin' => 'AdminController'
        );

        if (!isset($controllers[$page])) {
            $this->showNotFound();
            return;
        }

        $controllerName = $controllers[$page];
        $controllerPath = APP_PATH . '/controllers/' . $controllerName . '.php';

        if (!is_file($controllerPath)) {
            $this->showNotFound();
            return;
        }

        require_once $controllerPath;
        $controller = new $controllerName();

        if (!method_exists($controller, $rawAction)) {
            $this->showNotFound();
            return;
        }

        $controller->$rawAction();
    }

    // Return a not-found response when a route is unavailable.
    private function showNotFound()
    {
        http_response_code(404);
        $notFoundView = APP_PATH . '/views/errors/404.php';

        if (is_file($notFoundView)) {
            require APP_PATH . '/views/layouts/header.php';
            require $notFoundView;
            require APP_PATH . '/views/layouts/footer.php';
            return;
        }

        exit('404 Not Found');
    }
}