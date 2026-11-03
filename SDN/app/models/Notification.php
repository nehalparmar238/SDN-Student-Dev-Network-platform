<?php

class Notification
{
    private $db;

    // Store the shared database connection for notification queries.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Create an unread notification for one user.
    public function create($userId, $message)
    {
        $statement = $this->db->prepare(
            'INSERT INTO `notification` (`user_id`, `message`)
             VALUES (?, ?)'
        );

        return $statement->execute(array((int) $userId, $message));
    }

    // Create one notification for every member with a single database statement.
    public function createForMembers($message)
    {
        $statement = $this->db->prepare(
            'INSERT INTO `notification` (`user_id`, `message`)
             SELECT `user_id`, ?
             FROM `users`
             WHERE `role` = \'member\''
        );

        return $statement->execute(array($message));
    }

    // Count unread notifications for one user.
    public function countUnread($userId)
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM `notification`
             WHERE `user_id` = ? AND `is_read` = 0'
        );
        $statement->execute(array((int) $userId));

        return (int) $statement->fetchColumn();
    }

    // Return a user's notifications from newest to oldest.
    public function findByUser($userId)
    {
        $statement = $this->db->prepare(
            'SELECT `notification_id`, `message`, `is_read`, `created_at`
             FROM `notification`
             WHERE `user_id` = ?
             ORDER BY `created_at` DESC, `notification_id` DESC'
        );
        $statement->execute(array((int) $userId));

        return $statement->fetchAll();
    }

    // Mark every notification belonging to a user as read.
    public function markAllRead($userId)
    {
        $statement = $this->db->prepare(
            'UPDATE `notification`
             SET `is_read` = 1
             WHERE `user_id` = ? AND `is_read` = 0'
        );

        return $statement->execute(array((int) $userId));
    }
}