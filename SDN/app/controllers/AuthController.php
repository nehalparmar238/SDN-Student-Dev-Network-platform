<?php

class AuthController extends Controller
{
    // Show the registration form and create a member account on POST.
    public function register()
    {
        if (isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $userModel = new User();
        $errors = array();
        $formData = array(
            'name' => '',
            'email' => '',
            'college_course' => ''
        );

        if ($this->isPost()) {
            $this->requirePost();

            $name = $this->postString('name');
            $email = strtolower($this->postString('email'));
            $password = $this->postString('password', false);
            $confirmPassword = $this->postString('confirm_password', false);
            $collegeCourse = $this->postString('college_course');

            $formData = array(
                'name' => $name,
                'email' => $email,
                'college_course' => $collegeCourse
            );

            if ($name === '') {
                $errors[] = 'Name is required.';
            } elseif (mb_strlen($name, 'UTF-8') > 100) {
                $errors[] = 'Name must be 100 characters or fewer.';
            }

            if ($email === '') {
                $errors[] = 'Email is required.';
            } elseif (
                mb_strlen($email, 'UTF-8') > 120
                || filter_var($email, FILTER_VALIDATE_EMAIL) === false
            ) {
                $errors[] = 'Enter a valid email address of 120 characters or fewer.';
            } elseif ($userModel->emailExists($email)) {
                $errors[] = 'This email address is already registered.';
            }

            if (mb_strlen($password, 'UTF-8') < 8) {
                $errors[] = 'Password must be at least 8 characters long.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Password and confirmation do not match.';
            }

            if (mb_strlen($collegeCourse, 'UTF-8') > 150) {
                $errors[] = 'College or course must be 150 characters or fewer.';
            }

            if (count($errors) === 0) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $courseValue = $collegeCourse === '' ? null : $collegeCourse;
                $created = $userModel->create($name, $email, $passwordHash, $courseValue);

                if ($created) {
                    setFlash('success', 'Your account has been created. You can now log in.');
                    $this->redirect('auth', 'login');
                }

                $errors[] = 'This email address is already registered.';
            }
        }

        $this->render('auth/register', array(
            'errors' => $errors,
            'formData' => $formData
        ));
    }

    // Show the login form and start a session for valid credentials.
    public function login()
    {
        if (isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $userModel = new User();
        $errors = array();
        $formData = array('email' => '');

        if ($this->isPost()) {
            $this->requirePost();

            $email = strtolower($this->postString('email'));
            $password = $this->postString('password', false);
            $formData['email'] = $email;
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                $this->redirect('dashboard');
            }

            $errors[] = 'Invalid email or password.';
        }

        $this->render('auth/login', array(
            'errors' => $errors,
            'formData' => $formData
        ));
    }

    // Log out the signed-in user using a CSRF-protected POST request.
    public function logout()
    {
        $this->requireLogin();
        $this->requirePost();

        session_unset();
        session_destroy();

        $this->redirect('home');
    }

    // Read a string form value and optionally preserve its whitespace.
    private function postString($field, $trim = true)
    {
        if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
            return '';
        }

        return $trim ? trim($_POST[$field]) : $_POST[$field];
    }
}