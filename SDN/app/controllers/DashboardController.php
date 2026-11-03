<?php

class DashboardController extends Controller
{
    // Show the member dashboard with live activity counts.
    public function index()
    {
        $this->requireLogin();

        $userModel = new User();
        $counts = $userModel->getDashboardCounts(currentUserId());

        $this->render('dashboard/index', array(
            'counts' => $counts
        ));
    }
}