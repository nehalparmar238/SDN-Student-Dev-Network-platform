<?php

class MemberController extends Controller
{
    // Search the member directory by a name or skill keyword.
    public function index()
    {
        $this->requireLogin();

        $searchTerm = isset($_GET['q']) && is_string($_GET['q'])
            ? trim($_GET['q'])
            : '';
        if (mb_strlen($searchTerm, 'UTF-8') > 100) {
            $searchTerm = mb_substr($searchTerm, 0, 100, 'UTF-8');
        }

        $userModel = new User();
        $members = $userModel->searchDirectory($searchTerm);

        $this->render('members/index', array(
            'members' => $members,
            'searchTerm' => $searchTerm
        ));
    }

    // Display a public member profile and their open projects.
    public function view()
    {
        $this->requireLogin();

        $userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($userId < 1) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $userModel = new User();
        $member = $userModel->findPublicProfile($userId);

        if (!$member) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $projects = $userModel->getOpenProjectsForMember($userId);

        $this->render('members/view', array(
            'member' => $member,
            'projects' => $projects
        ));
    }
}