<?php

class ProjectController extends Controller
{
    // Browse projects with optional keyword, category, and technology filters.
    public function index()
    {
        $searchTerm = $this->getQueryString('q', 120);
        $technology = $this->getQueryString('tech', 255);
        $categoryId = isset($_GET['category']) && !is_array($_GET['category'])
            ? (int) $_GET['category']
            : 0;

        $filters = array(
            'q' => $searchTerm,
            'category_id' => $categoryId,
            'tech' => $technology
        );
        $projectModel = new Project();
        $categoryModel = new Category();

        $this->render('projects/index', array(
            'projects' => $projectModel->findAll($filters),
            'categories' => $categoryModel->findAll(),
            'filters' => $filters
        ));
    }

    // Display project details and the accepted members.
    public function view()
    {
        $projectId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $projectModel = new Project();
        $project = $projectId > 0 ? $projectModel->findById($projectId) : false;

        if (!$project) {
            $this->showNotFound();
            return;
        }

        $applicationStatus = null;
        $canApply = false;

        if (isLoggedIn() && (int) $project['owner_id'] !== currentUserId()) {
            $application = $projectModel->findApplicationByMember(
                $projectId,
                currentUserId()
            );
            $applicationStatus = $application ? $application['status'] : null;
            $canApply = $project['status'] === 'open' && $application === false;
        }

        $this->render('projects/view', array(
            'project' => $project,
            'acceptedMembers' => $projectModel->findAcceptedMembers($projectId),
            'applicationStatus' => $applicationStatus,
            'canApply' => $canApply
        ));
    }

    // Show the signed-in member's projects.
    public function mine()
    {
        $this->requireLogin();

        $projectModel = new Project();
        $this->render('projects/mine', array(
            'projects' => $projectModel->findByOwner(currentUserId())
        ));
    }

    // Show the create form or save a new project.
    public function create()
    {
        $this->requireLogin();

        $categoryModel = new Category();
        $projectModel = new Project();
        $errors = array();
        $formData = $this->emptyFormData();

        if ($this->isPost()) {
            $this->requirePost();
            $formData = $this->readFormData();
            $teamSizeNeeded = $this->validateProject($formData, $categoryModel, $errors, false);

            if (count($errors) === 0) {
                $projectModel->create(
                    currentUserId(),
                    (int) $formData['category_id'],
                    $formData['title'],
                    $this->nullableString($formData['description']),
                    $this->nullableString($formData['tech_stack']),
                    $teamSizeNeeded
                );

                setFlash('success', 'Your project has been created.');
                $this->redirect('projects', 'mine');
            }
        }

        $this->render('projects/create', array(
            'categories' => $categoryModel->findAll(),
            'errors' => $errors,
            'formData' => $formData,
            'isEdit' => false,
            'projectId' => 0
        ));
    }

    // Show the edit form or update a project after checking its owner.
    public function edit()
    {
        $this->requireLogin();

        $projectId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $projectModel = new Project();
        $categoryModel = new Category();
        $project = $projectId > 0 ? $projectModel->findById($projectId) : false;

        if (!$project) {
            $this->showNotFound();
            return;
        }

        if ((int) $project['owner_id'] !== currentUserId() && !isAdmin()) {
            http_response_code(403);
            exit('403 Forbidden');
        }

        $errors = array();
        $formData = array(
            'title' => $project['title'],
            'category_id' => (string) $project['category_id'],
            'description' => $project['description'] ?? '',
            'tech_stack' => $project['tech_stack'] ?? '',
            'team_size_needed' => $project['team_size_needed'] === null
                ? ''
                : (string) $project['team_size_needed'],
            'status' => $project['status']
        );

        if ($this->isPost()) {
            $this->requirePost();
            $formData = $this->readFormData(true);
            $teamSizeNeeded = $this->validateProject($formData, $categoryModel, $errors, true);

            if (count($errors) === 0) {
                $projectModel->update(
                    $projectId,
                    (int) $formData['category_id'],
                    $formData['title'],
                    $this->nullableString($formData['description']),
                    $this->nullableString($formData['tech_stack']),
                    $teamSizeNeeded,
                    $formData['status']
                );

                setFlash('success', 'The project has been updated.');
                $this->redirect('projects', 'view', array('id' => $projectId));
            }
        }

        $this->render('projects/edit', array(
            'categories' => $categoryModel->findAll(),
            'errors' => $errors,
            'formData' => $formData,
            'isEdit' => true,
            'projectId' => $projectId
        ));
    }

    // Read and validate the form fields for creating or editing a project.
    private function validateProject($formData, $categoryModel, &$errors, $isEdit)
    {
        if ($formData['title'] === '') {
            $errors[] = 'Project title is required.';
        } elseif (mb_strlen($formData['title'], 'UTF-8') > 120) {
            $errors[] = 'Project title must be 120 characters or fewer.';
        }

        $categoryId = filter_var($formData['category_id'], FILTER_VALIDATE_INT);
        if (
            $categoryId === false
            || $categoryId < 1
            || !$categoryModel->exists($categoryId)
        ) {
            $errors[] = 'Choose a valid project category.';
        }

        if (mb_strlen($formData['tech_stack'], 'UTF-8') > 255) {
            $errors[] = 'Technology stack must be 255 characters or fewer.';
        }

        $teamSizeNeeded = null;
        if ($formData['team_size_needed'] !== '') {
            $teamSize = filter_var($formData['team_size_needed'], FILTER_VALIDATE_INT);

            if ($teamSize === false || $teamSize < 1 || $teamSize > 50) {
                $errors[] = 'Team size must be a whole number from 1 to 50.';
            } else {
                $teamSizeNeeded = (int) $teamSize;
            }
        }

        if ($isEdit) {
            $validStatuses = array('open', 'in_progress', 'closed');
            if (!in_array($formData['status'], $validStatuses, true)) {
                $errors[] = 'Choose a valid project status.';
            }
        }

        return $teamSizeNeeded;
    }

    // Return blank values for the new-project form.
    private function emptyFormData()
    {
        return array(
            'title' => '',
            'category_id' => '',
            'description' => '',
            'tech_stack' => '',
            'team_size_needed' => '',
            'status' => 'open'
        );
    }

    // Read project fields from POST without trusting submitted arrays.
    private function readFormData($includeStatus = false)
    {
        $formData = array(
            'title' => $this->postString('title'),
            'category_id' => $this->postString('category_id'),
            'description' => $this->postString('description'),
            'tech_stack' => $this->postString('tech_stack'),
            'team_size_needed' => $this->postString('team_size_needed'),
            'status' => $includeStatus ? $this->postString('status') : 'open'
        );

        return $formData;
    }

    // Return a bounded string query parameter or an empty string.
    private function getQueryString($field, $maxLength)
    {
        if (!isset($_GET[$field]) || !is_string($_GET[$field])) {
            return '';
        }

        $value = trim($_GET[$field]);
        if (mb_strlen($value, 'UTF-8') > $maxLength) {
            $value = mb_substr($value, 0, $maxLength, 'UTF-8');
        }

        return $value;
    }

    // Read a string form value and optionally preserve its whitespace.
    private function postString($field, $trim = true)
    {
        if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
            return '';
        }

        return $trim ? trim($_POST[$field]) : $_POST[$field];
    }

    // Convert an optional string to null when the field is empty.
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