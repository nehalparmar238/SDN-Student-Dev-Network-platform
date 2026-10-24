<?php

class AdminController extends Controller
{
    // Display the administrator dashboard with platform counts and section links.
    public function index()
    {
        $this->requireAdmin();

        $userModel = new User();
        $projectModel = new Project();
        $eventModel = new Event();
        $applicationModel = new Application();
        $categoryModel = new Category();

        $counts = array(
            'users' => $userModel->countAll(),
            'projects' => $projectModel->countAll(),
            'events' => $eventModel->countAll(),
            'pending_applications' => $applicationModel->countPending(),
            'categories' => $categoryModel->countAll()
        );

        $this->render('admin/index', array(
            'counts' => $counts
        ));
    }

    // Show all registered users with their role and join date.
    public function users()
    {
        $this->requireAdmin();

        $userModel = new User();
        $this->render('admin/users', array(
            'users' => $userModel->findAll()
        ));
    }

    // Switch another user's role between member and admin.
    public function userRole()
    {
        $this->requireAdmin();
        $this->requirePost();

        $userId = $this->postId('user_id');
        $role = $this->postString('role');

        if ($userId === currentUserId()) {
            setFlash('error', 'You cannot change your own role.');
            $this->redirect('admin', 'users');
        }

        if (!in_array($role, array('member', 'admin'), true)) {
            setFlash('error', 'Choose a valid user role.');
            $this->redirect('admin', 'users');
        }

        $userModel = new User();
        $user = $userId > 0 ? $userModel->findById($userId) : false;

        if (!$user) {
            setFlash('error', 'The user could not be found.');
            $this->redirect('admin', 'users');
        }

        $userModel->updateRole($userId, $role);
        setFlash('success', 'The user role has been updated.');
        $this->redirect('admin', 'users');
    }

    // Delete another user account.
    public function userDelete()
    {
        $this->requireAdmin();
        $this->requirePost();

        $userId = $this->postId('user_id');

        if ($userId === currentUserId()) {
            setFlash('error', 'You cannot delete your own account.');
            $this->redirect('admin', 'users');
        }

        $userModel = new User();
        $user = $userId > 0 ? $userModel->findById($userId) : false;

        if (!$user) {
            setFlash('error', 'The user could not be found.');
            $this->redirect('admin', 'users');
        }

        $userModel->delete($userId);
        setFlash('success', 'The user account has been deleted.');
        $this->redirect('admin', 'users');
    }

    // Show all projects for administrator management.
    public function projects()
    {
        $this->requireAdmin();

        $projectModel = new Project();
        $this->render('admin/projects', array(
            'projects' => $projectModel->findAll()
        ));
    }

    // Change the status of a project.
    public function projectStatus()
    {
        $this->requireAdmin();
        $this->requirePost();

        $projectId = $this->postId('project_id');
        $status = $this->postString('status');

        if (!in_array($status, array('open', 'in_progress', 'closed'), true)) {
            setFlash('error', 'Choose a valid project status.');
            $this->redirect('admin', 'projects');
        }

        $projectModel = new Project();
        $project = $projectId > 0 ? $projectModel->findById($projectId) : false;

        if (!$project) {
            setFlash('error', 'The project could not be found.');
            $this->redirect('admin', 'projects');
        }

        $projectModel->updateStatus($projectId, $status);
        setFlash('success', 'The project status has been updated.');
        $this->redirect('admin', 'projects');
    }

    // Delete a project.
    public function projectDelete()
    {
        $this->requireAdmin();
        $this->requirePost();

        $projectId = $this->postId('project_id');
        $projectModel = new Project();
        $project = $projectId > 0 ? $projectModel->findById($projectId) : false;

        if (!$project) {
            setFlash('error', 'The project could not be found.');
            $this->redirect('admin', 'projects');
        }

        $projectModel->delete($projectId);
        setFlash('success', 'The project has been deleted.');
        $this->redirect('admin', 'projects');
    }

    // List all events for administrator management.
    public function events()
    {
        $this->requireAdmin();

        $eventModel = new Event();
        $this->render('admin/events', array(
            'events' => $eventModel->findAll()
        ));
    }

    // Show the event creation form or save a new event and notify all members.
    public function eventCreate()
    {
        $this->requireAdmin();

        $errors = array();
        $formData = array(
            'title' => '',
            'description' => '',
            'event_type' => 'event',
            'event_date' => '',
            'venue' => '',
            'capacity' => ''
        );

        if ($this->isPost()) {
            $this->requirePost();
            $formData = $this->readEventFormData();
            $validated = $this->validateEvent($formData, $errors);

            if (count($errors) === 0) {
                $eventModel = new Event();
                $eventModel->create(
                    currentUserId(),
                    $formData['title'],
                    $this->nullableString($formData['description']),
                    $formData['event_type'],
                    $validated['event_date'],
                    $this->nullableString($formData['venue']),
                    $validated['capacity']
                );

                $notificationMessage = 'New '
                    . $formData['event_type']
                    . ': '
                    . $formData['title'];
                $notificationMessage = mb_substr($notificationMessage, 0, 255, 'UTF-8');

                $notificationModel = new Notification();
                $notificationModel->createForMembers($notificationMessage);

                setFlash('success', 'The event has been created.');
                $this->redirect('admin', 'events');
            }
        }

        $this->render('admin/event_form', array(
            'errors' => $errors,
            'formData' => $formData,
            'isEdit' => false,
            'eventId' => 0
        ));
    }

    // Show the event edit form or update an existing event.
    public function eventEdit()
    {
        $this->requireAdmin();

        $eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $eventModel = new Event();
        $event = $eventId > 0 ? $eventModel->findById($eventId) : false;

        if (!$event) {
            $this->showNotFound();
            return;
        }

        $errors = array();
        $timestamp = strtotime((string) $event['event_date']);
        $formattedDate = $timestamp !== false ? date('Y-m-d\TH:i', $timestamp) : '';

        $formData = array(
            'title' => $event['title'],
            'description' => $event['description'] ?? '',
            'event_type' => $event['event_type'],
            'event_date' => $formattedDate,
            'venue' => $event['venue'] ?? '',
            'capacity' => $event['capacity'] === null ? '' : (string) $event['capacity']
        );

        if ($this->isPost()) {
            $this->requirePost();
            $formData = $this->readEventFormData();
            $validated = $this->validateEvent($formData, $errors);

            if (count($errors) === 0) {
                $eventModel->update(
                    $eventId,
                    $formData['title'],
                    $this->nullableString($formData['description']),
                    $formData['event_type'],
                    $validated['event_date'],
                    $this->nullableString($formData['venue']),
                    $validated['capacity']
                );

                setFlash('success', 'The event has been updated.');
                $this->redirect('admin', 'events');
            }
        }

        $this->render('admin/event_form', array(
            'errors' => $errors,
            'formData' => $formData,
            'isEdit' => true,
            'eventId' => $eventId
        ));
    }

    // Delete an event.
    public function eventDelete()
    {
        $this->requireAdmin();
        $this->requirePost();

        $eventId = $this->postId('event_id');
        if ($eventId < 1 && isset($_GET['id'])) {
            $eventId = (int) $_GET['id'];
        }

        $eventModel = new Event();
        $event = $eventId > 0 ? $eventModel->findById($eventId) : false;

        if (!$event) {
            setFlash('error', 'The event could not be found.');
            $this->redirect('admin', 'events');
        }

        $eventModel->delete($eventId);
        setFlash('success', 'The event has been deleted.');
        $this->redirect('admin', 'events');
    }

    // Show category management for administrators.
    public function categories()
    {
        $this->requireAdmin();

        $this->renderCategoryList(array(), array(
            'category_name' => '',
            'description' => ''
        ));
    }

    // Create a category after validating the submitted fields.
    public function categoryCreate()
    {
        $this->requireAdmin();
        $this->requirePost();

        $formData = array(
            'category_name' => $this->postString('category_name'),
            'description' => $this->postString('description')
        );
        $errors = $this->validateCategory($formData);

        if (count($errors) === 0) {
            $categoryModel = new Category();
            $description = $formData['description'] === '' ? null : $formData['description'];

            if ($categoryModel->create($formData['category_name'], $description)) {
                setFlash('success', 'The category has been added.');
                $this->redirect('admin', 'categories');
            }

            $errors[] = 'A category with that name already exists.';
        }

        $this->renderCategoryList($errors, $formData);
    }

    // Show a category edit form or save its updated values.
    public function categoryEdit()
    {
        $this->requireAdmin();

        $categoryId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $categoryModel = new Category();
        $category = $categoryId > 0 ? $categoryModel->findById($categoryId) : false;

        if (!$category) {
            $this->showNotFound();
            return;
        }

        $formData = array(
            'category_name' => $category['category_name'],
            'description' => $category['description'] ?? ''
        );
        $errors = array();

        if ($this->isPost()) {
            $this->requirePost();
            $formData = array(
                'category_name' => $this->postString('category_name'),
                'description' => $this->postString('description')
            );
            $errors = $this->validateCategory($formData);

            if (count($errors) === 0) {
                $description = $formData['description'] === ''
                    ? null
                    : $formData['description'];

                if ($categoryModel->update(
                    $categoryId,
                    $formData['category_name'],
                    $description
                )) {
                    setFlash('success', 'The category has been updated.');
                    $this->redirect('admin', 'categories');
                }

                $errors[] = 'A category with that name already exists.';
            }
        }

        $this->render('admin/category_edit', array(
            'categoryId' => $categoryId,
            'errors' => $errors,
            'formData' => $formData
        ));
    }

    // Delete a category only when it is not used by any projects.
    public function categoryDelete()
    {
        $this->requireAdmin();
        $this->requirePost();

        $categoryId = $this->postId('category_id');
        if ($categoryId < 1 && isset($_GET['id'])) {
            $categoryId = (int) $_GET['id'];
        }

        $categoryModel = new Category();

        if ($categoryId < 1 || !$categoryModel->findById($categoryId)) {
            $this->showNotFound();
            return;
        }

        if ($categoryModel->delete($categoryId)) {
            setFlash('success', 'The category has been deleted.');
        } else {
            setFlash('error', 'A category cannot be deleted while projects use it.');
        }

        $this->redirect('admin', 'categories');
    }

    // List all project applications across the platform.
    public function applications()
    {
        $this->requireAdmin();

        $applicationModel = new Application();
        $this->render('admin/applications', array(
            'applications' => $applicationModel->findAll()
        ));
    }

    // Allow an administrator to accept or reject a pending application.
    public function applicationDecide()
    {
        $this->requireAdmin();
        $this->requirePost();

        $applicationId = $this->postId('application_id');
        $status = $this->postString('status');

        if (!in_array($status, array('accepted', 'rejected'), true)) {
            setFlash('error', 'Choose a valid application decision.');
            $this->redirect('admin', 'applications');
        }

        $applicationModel = new Application();
        $application = $applicationId > 0
            ? $applicationModel->findById($applicationId)
            : false;

        if (!$application) {
            setFlash('error', 'The application could not be found.');
            $this->redirect('admin', 'applications');
        }

        if ($application['status'] !== 'pending') {
            setFlash('error', 'Only pending applications can be decided.');
            $this->redirect('admin', 'applications');
        }

        if (!$applicationModel->updatePendingStatus($applicationId, $status)) {
            setFlash('error', 'The application status changed before your decision was saved.');
            $this->redirect('admin', 'applications');
        }

        $notificationMessage = 'Your application to '
            . $application['project_title']
            . ' was '
            . $status
            . '.';
        $notificationMessage = mb_substr($notificationMessage, 0, 255, 'UTF-8');

        $notificationModel = new Notification();
        $notificationModel->create(
            (int) $application['applicant_id'],
            $notificationMessage
        );

        setFlash('success', 'The application has been ' . $status . '.');
        $this->redirect('admin', 'applications');
    }

    // Read event fields from POST.
    private function readEventFormData()
    {
        return array(
            'title' => $this->postString('title'),
            'description' => $this->postString('description'),
            'event_type' => $this->postString('event_type'),
            'event_date' => $this->postString('event_date'),
            'venue' => $this->postString('venue'),
            'capacity' => $this->postString('capacity')
        );
    }

    // Validate event inputs and normalize the date and capacity values.
    private function validateEvent($formData, &$errors)
    {
        if ($formData['title'] === '') {
            $errors[] = 'Event title is required.';
        } elseif (mb_strlen($formData['title'], 'UTF-8') > 120) {
            $errors[] = 'Event title must be 120 characters or fewer.';
        }

        $validTypes = array('event', 'workshop', 'hackathon');
        if (!in_array($formData['event_type'], $validTypes, true)) {
            $errors[] = 'Choose a valid event type.';
        }

        $normalizedDate = str_replace('T', ' ', $formData['event_date']);
        if (strlen($normalizedDate) === 16) {
            $normalizedDate .= ':00';
        }

        $timestamp = strtotime($normalizedDate);
        if ($formData['event_date'] === '' || $timestamp === false) {
            $errors[] = 'Enter a valid event date and time.';
        } else {
            $normalizedDate = date('Y-m-d H:i:s', $timestamp);
        }

        if (mb_strlen($formData['venue'], 'UTF-8') > 150) {
            $errors[] = 'Venue must be 150 characters or fewer.';
        }

        $capacity = null;
        if ($formData['capacity'] !== '') {
            $parsedCapacity = filter_var($formData['capacity'], FILTER_VALIDATE_INT);

            if ($parsedCapacity === false || $parsedCapacity < 1) {
                $errors[] = 'Capacity must be a whole number of 1 or more, or left blank.';
            } else {
                $capacity = (int) $parsedCapacity;
            }
        }

        return array(
            'event_date' => $normalizedDate,
            'capacity' => $capacity
        );
    }

    // Validate the category name and optional description.
    private function validateCategory($formData)
    {
        $errors = array();

        if ($formData['category_name'] === '') {
            $errors[] = 'Category name is required.';
        } elseif (mb_strlen($formData['category_name'], 'UTF-8') > 80) {
            $errors[] = 'Category name must be 80 characters or fewer.';
        }

        if (mb_strlen($formData['description'], 'UTF-8') > 255) {
            $errors[] = 'Category description must be 255 characters or fewer.';
        }

        return $errors;
    }

    // Render the category list and retain the submitted form values.
    private function renderCategoryList($errors, $formData)
    {
        $categoryModel = new Category();
        $this->render('admin/categories', array(
            'categories' => $categoryModel->findAll(),
            'errors' => $errors,
            'formData' => $formData
        ));
    }

    // Read a positive integer ID from POST.
    private function postId($field)
    {
        if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
            return 0;
        }

        $value = filter_var(trim($_POST[$field]), FILTER_VALIDATE_INT);

        if ($value === false || $value < 1) {
            return 0;
        }

        return (int) $value;
    }

    // Read a string form value and trim surrounding whitespace.
    private function postString($field)
    {
        if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
            return '';
        }

        return trim($_POST[$field]);
    }

    // Convert an empty string to null.
    private function nullableString($value)
    {
        return $value === '' ? null : $value;
    }

    // Render the shared not-found page with its correct HTTP status.
    private function showNotFound()
    {
        http_response_code(404);
        $this->render('errors/404');
    }
}