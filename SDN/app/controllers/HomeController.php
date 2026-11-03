<?php

class HomeController extends Controller
{
    // Show the public homepage with the latest community projects and events.
    public function index()
    {
        $projectModel = new Project();
        $projects = $projectModel->findLatestOpenForHome();

        $eventModel = new Event();
        $events = $eventModel->findUpcomingForHome();

        $this->render('home/index', array(
            'projects' => $projects,
            'events' => $events
        ));
    }
}