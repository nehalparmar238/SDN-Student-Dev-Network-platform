<?php

class NotificationController extends Controller
{
    // Show the signed-in member's notifications and mark the loaded list as read.
    public function index()
    {
        $this->requireLogin();

        $notificationModel = new Notification();
        $notifications = $notificationModel->findByUser(currentUserId());
        $notificationModel->markAllRead(currentUserId());

        $this->render('notifications/index', array(
            'notifications' => $notifications
        ));
    }
}