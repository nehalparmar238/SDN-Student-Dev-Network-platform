<?php

class ProfileController extends Controller
{
    // Display the private profile belonging to the signed-in member.
    public function index()
    {
        $this->requireLogin();

        $userModel = new User();
        $user = $userModel->findById(currentUserId());

        if (!$user) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $this->render('profile/index', array(
            'user' => $user
        ));
    }

    // Edit allowed profile fields and optionally change the password.
    public function edit()
    {
        $this->requireLogin();

        $userModel = new User();
        $user = $userModel->findById(currentUserId());

        if (!$user) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $errors = array();
        $formData = array(
            'name' => $user['name'],
            'college_course' => $user['college_course'] ?? '',
            'skills' => $user['skills'] ?? '',
            'bio' => $user['bio'] ?? ''
        );

        if ($this->isPost()) {
            $this->requirePost();

            $formData = array(
                'name' => $this->postString('name'),
                'college_course' => $this->postString('college_course'),
                'skills' => $this->postString('skills'),
                'bio' => $this->postString('bio')
            );
            $currentPassword = $this->postString('current_password', false);
            $newPassword = $this->postString('new_password', false);

            if ($formData['name'] === '') {
                $errors[] = 'Name is required.';
            } elseif (mb_strlen($formData['name'], 'UTF-8') > 100) {
                $errors[] = 'Name must be 100 characters or fewer.';
            }

            if (mb_strlen($formData['college_course'], 'UTF-8') > 150) {
                $errors[] = 'College or course must be 150 characters or fewer.';
            }

            if (mb_strlen($formData['skills'], 'UTF-8') > 255) {
                $errors[] = 'Skills must be 255 characters or fewer.';
            }

            if (mb_strlen($formData['bio'], 'UTF-8') > 5000) {
                $errors[] = 'Bio must be 5,000 characters or fewer.';
            }

            $newPasswordHash = null;
            if ($currentPassword !== '' || $newPassword !== '') {
                if ($currentPassword === '' || $newPassword === '') {
                    $errors[] = 'Enter both your current password and a new password to change it.';
                } elseif (mb_strlen($newPassword, 'UTF-8') < 8) {
                    $errors[] = 'New password must be at least 8 characters long.';
                } else {
                    $passwordHash = $userModel->getPasswordHash(currentUserId());

                    if ($passwordHash === false || !password_verify($currentPassword, $passwordHash)) {
                        $errors[] = 'Your current password is incorrect.';
                    } else {
                        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    }
                }
            }

            if (count($errors) === 0) {
                $courseValue = $formData['college_course'] === ''
                    ? null
                    : $formData['college_course'];
                $skillsValue = $formData['skills'] === '' ? null : $formData['skills'];
                $bioValue = $formData['bio'] === '' ? null : $formData['bio'];

                $userModel->updateProfile(
                    currentUserId(),
                    $formData['name'],
                    $courseValue,
                    $skillsValue,
                    $bioValue,
                    $newPasswordHash
                );

                $_SESSION['user_name'] = $formData['name'];
                setFlash('success', 'Your profile has been updated.');
                $this->redirect('profile');
            }
        }

        $this->render('profile/edit', array(
            'errors' => $errors,
            'formData' => $formData,
            'email' => $user['email']
        ));
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