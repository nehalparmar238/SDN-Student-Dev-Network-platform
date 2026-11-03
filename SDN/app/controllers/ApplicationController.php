<?php

class ApplicationController extends Controller
{
    // Create a join application after checking the project and member.
    public function apply()
    {
        $this->requireLogin();
        $this->requirePost();

        $projectId = $this->postId('project_id');
        $message = $this->postString('message');

        if (mb_strlen($message, 'UTF-8') > 255) {
            setFlash('error', 'The application message must be 255 characters or fewer.');
            $this->redirectToProject($projectId);
        }

        $projectModel = new Project();
        $project = $projectId > 0 ? $projectModel->findById($projectId) : false;

        if (!$project) {
            setFlash('error', 'The project could not be found.');
            $this->redirect('projects');
        }

        if ($project['status'] !== 'open') {
            setFlash('error', 'Applications are only accepted for open projects.');
            $this->redirectToProject($projectId);
        }

        if ((int) $project['owner_id'] === currentUserId()) {
            setFlash('error', 'You cannot apply to your own project.');
            $this->redirectToProject($projectId);
        }

        if ($projectModel->findApplicationByMember($projectId, currentUserId()) !== false) {
            setFlash('error', 'You have already submitted an application to this project.');
            $this->redirectToProject($projectId);
        }

        $applicationModel = new Application();
        if (!$applicationModel->create($projectId, currentUserId(), $this->nullableString($message))) {
            setFlash('error', 'You have already submitted an application to this project.');
            $this->redirectToProject($projectId);
        }

        $notificationMessage = currentUserName()
            . ' applied to your project '
            . $project['title'];
        $notificationMessage = mb_substr($notificationMessage, 0, 255, 'UTF-8');

        $notificationModel = new Notification();
        $notificationModel->create((int) $project['owner_id'], $notificationMessage);

        setFlash('success', 'Your application has been sent.');
        $this->redirectToProject($projectId);
    }

    // Show the signed-in member's applications.
    public function mine()
    {
        $this->requireLogin();

        $applicationModel = new Application();
        $this->render('applications/mine', array(
            'applications' => $applicationModel->findByApplicant(currentUserId())
        ));
    }

    // Withdraw a pending application owned by the signed-in member.
    public function withdraw()
    {
        $this->requireLogin();
        $this->requirePost();

        $applicationId = $this->postId('application_id');
        $applicationModel = new Application();
        $application = $applicationId > 0
            ? $applicationModel->findById($applicationId)
            : false;

        if (!$application) {
            setFlash('error', 'The application could not be found.');
            $this->redirect('applications', 'mine');
        }

        if ((int) $application['applicant_id'] !== currentUserId()) {
            http_response_code(403);
            exit('403 Forbidden');
        }

        if ($application['status'] !== 'pending') {
            setFlash('error', 'Only pending applications can be withdrawn.');
            $this->redirect('applications', 'mine');
        }

        if (!$applicationModel->updatePendingStatus($applicationId, 'withdrawn')) {
            setFlash('error', 'The application status changed before it could be withdrawn.');
            $this->redirect('applications', 'mine');
        }

        setFlash('success', 'Your application has been withdrawn.');
        $this->redirect('applications', 'mine');
    }

    // Show applications submitted to projects owned by the signed-in member.
    public function received()
    {
        $this->requireLogin();

        $applicationModel = new Application();
        $applications = $applicationModel->findReceivedByOwner(currentUserId());
        $projects = array();

        foreach ($applications as $application) {
            $projectId = (int) $application['project_id'];

            if (!isset($projects[$projectId])) {
                $projects[$projectId] = array(
                    'project_id' => $projectId,
                    'project_title' => $application['project_title'],
                    'applications' => array()
                );
            }

            $projects[$projectId]['applications'][] = $application;
        }

        $this->render('applications/received', array(
            'projects' => array_values($projects)
        ));
    }

    // Accept a pending application for a project owned by the current user.
    public function accept()
    {
        $this->decide('accepted');
    }

    // Reject a pending application for a project owned by the current user.
    public function reject()
    {
        $this->decide('rejected');
    }

    // Apply a pending decision after checking ownership or administrator access.
    private function decide($status)
    {
        $this->requireLogin();
        $this->requirePost();

        $applicationId = $this->postId('application_id');
        $applicationModel = new Application();
        $application = $applicationId > 0
            ? $applicationModel->findById($applicationId)
            : false;

        if (!$application) {
            setFlash('error', 'The application could not be found.');
            $this->redirect('applications', 'received');
        }

        if ((int) $application['owner_id'] !== currentUserId() && !isAdmin()) {
            http_response_code(403);
            exit('403 Forbidden');
        }

        if ($application['status'] !== 'pending') {
            setFlash('error', 'Only pending applications can be decided.');
            $this->redirect('applications', 'received');
        }

        if (!$applicationModel->updatePendingStatus($applicationId, $status)) {
            setFlash('error', 'The application status changed before your decision was saved.');
            $this->redirect('applications', 'received');
        }

        $decision = $status === 'accepted' ? 'accepted' : 'rejected';
        $notificationMessage = 'Your application to '
            . $application['project_title']
            . ' was '
            . $decision
            . '.';
        $notificationMessage = mb_substr($notificationMessage, 0, 255, 'UTF-8');

        $notificationModel = new Notification();
        $notificationModel->create(
            (int) $application['applicant_id'],
            $notificationMessage
        );

        setFlash('success', 'The application has been ' . $decision . '.');
        $this->redirect('applications', 'received');
    }

    // Read a positive integer from a submitted form field.
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

    // Convert an empty optional string to null.
    private function nullableString($value)
    {
        return $value === '' ? null : $value;
    }

    // Redirect to a project page when its submitted ID is valid.
    private function redirectToProject($projectId)
    {
        if ($projectId > 0) {
            $this->redirect('projects', 'view', array('id' => (int) $projectId));
        }

        $this->redirect('projects');
    }
}