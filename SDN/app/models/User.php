<?php

class User
{
    private $db;

    // Store the shared database connection for user queries.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Find a user by email for registration checks and login.
    public function findByEmail($email)
    {
        $statement = $this->db->prepare(
            'SELECT `user_id`, `name`, `email`, `password_hash`, `college_course`, `role`
             FROM `users`
             WHERE `email` = ?
             LIMIT 1'
        );
        $statement->execute(array($email));

        return $statement->fetch();
    }

    // Check whether an email address is already registered.
    public function emailExists($email)
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM `users`
             WHERE `email` = ?'
        );
        $statement->execute(array($email));

        return (int) $statement->fetchColumn() > 0;
    }

    // Create a member account without accepting a role from the form.
    public function create($name, $email, $passwordHash, $collegeCourse)
    {
        $statement = $this->db->prepare(
            "INSERT INTO `users` (`name`, `email`, `password_hash`, `college_course`, `role`)
             VALUES (?, ?, ?, ?, 'member')"
        );

        try {
            return $statement->execute(array(
                $name,
                $email,
                $passwordHash,
                $collegeCourse
            ));
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return false;
            }

            throw $exception;
        }
    }

    // Find a user record for the signed-in member's private profile.
    public function findById($userId)
    {
        $statement = $this->db->prepare(
            'SELECT `user_id`, `name`, `email`, `college_course`,
                    `skills`, `bio`, `role`, `created_at`
             FROM `users`
             WHERE `user_id` = ?
             LIMIT 1'
        );
        $statement->execute(array((int) $userId));

        return $statement->fetch();
    }

    // Find only public profile fields for a member directory profile.
    public function findPublicProfile($userId)
    {
        $statement = $this->db->prepare(
            "SELECT `user_id`, `name`, `college_course`, `skills`, `bio`, `created_at`
             FROM `users`
             WHERE `user_id` = ? AND `role` = 'member'
             LIMIT 1"
        );
        $statement->execute(array((int) $userId));

        return $statement->fetch();
    }

    // Search member names and skills without exposing private account fields.
    public function searchDirectory($searchTerm)
    {
        $searchTerm = trim((string) $searchTerm);
        $statement = $this->db->prepare(
            "SELECT `user_id`, `name`, `college_course`, `skills`, `bio`
             FROM `users`
             WHERE `role` = 'member'
               AND (? = '' OR `name` LIKE ? OR `skills` LIKE ?)
             ORDER BY `name` ASC
             LIMIT 50"
        );
        $pattern = '%' . $searchTerm . '%';
        $statement->execute(array($searchTerm, $pattern, $pattern));

        return $statement->fetchAll();
    }

    // List a member's currently open projects for their public profile.
    public function getOpenProjectsForMember($userId)
    {
        $statement = $this->db->prepare(
            "SELECT `project`.`project_id`, `project`.`title`,
                    `project`.`description`, `project`.`tech_stack`,
                    `project_category`.`category_name`
             FROM `project`
             LEFT JOIN `project_category`
               ON `project_category`.`category_id` = `project`.`category_id`
             WHERE `project`.`owner_id` = ? AND `project`.`status` = 'open'
             ORDER BY `project`.`created_at` DESC"
        );
        $statement->execute(array((int) $userId));

        return $statement->fetchAll();
    }

    // Return live counts used by the member dashboard overview.
    public function getDashboardCounts($userId)
    {
        $statement = $this->db->prepare(
            'SELECT
                (SELECT COUNT(*) FROM `project` WHERE `owner_id` = ?) AS `my_projects`,
                (SELECT COUNT(*) FROM `project_application` WHERE `applicant_id` = ?) AS `my_applications`,
                (SELECT COUNT(*)
                   FROM `project_application`
                   INNER JOIN `project`
                     ON `project`.`project_id` = `project_application`.`project_id`
                  WHERE `project`.`owner_id` = ?
                    AND `project_application`.`status` = \'pending\') AS `waiting_applications`,
                (SELECT COUNT(*)
                   FROM `event_registration`
                   INNER JOIN `event`
                     ON `event`.`event_id` = `event_registration`.`event_id`
                  WHERE `event_registration`.`user_id` = ?
                    AND `event`.`event_date` >= ?) AS `upcoming_registrations`,
                (SELECT COUNT(*)
                   FROM `notification`
                  WHERE `user_id` = ? AND `is_read` = 0) AS `unread_notifications`'
        );
        $statement->execute(array(
            (int) $userId,
            (int) $userId,
            (int) $userId,
            (int) $userId,
            date('Y-m-d H:i:s'),
            (int) $userId
        ));

        return $statement->fetch();
    }

    // Get the current password hash to verify an optional password change.
    public function getPasswordHash($userId)
    {
        $statement = $this->db->prepare(
            'SELECT `password_hash`
             FROM `users`
             WHERE `user_id` = ?
             LIMIT 1'
        );
        $statement->execute(array((int) $userId));

        return $statement->fetchColumn();
    }

    // Save editable profile fields and an optional new password atomically.
    public function updateProfile($userId, $name, $collegeCourse, $skills, $bio, $newPasswordHash = null)
    {
        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare(
                'UPDATE `users`
                 SET `name` = ?, `college_course` = ?, `skills` = ?, `bio` = ?
                 WHERE `user_id` = ?'
            );
            $statement->execute(array(
                $name,
                $collegeCourse,
                $skills,
                $bio,
                (int) $userId
            ));

            if ($newPasswordHash !== null) {
                $passwordStatement = $this->db->prepare(
                    'UPDATE `users`
                     SET `password_hash` = ?
                     WHERE `user_id` = ?'
                );
                $passwordStatement->execute(array($newPasswordHash, (int) $userId));
            }

            $this->db->commit();

            return true;
        } catch (PDOException $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    // Count all users on the platform for the admin dashboard.
    public function countAll()
    {
        $statement = $this->db->query('SELECT COUNT(*) FROM `users`');

        return (int) $statement->fetchColumn();
    }

    // Return all users ordered by join date for admin management.
    public function findAll()
    {
        $statement = $this->db->query(
            'SELECT `user_id`, `name`, `email`, `college_course`, `role`, `created_at`
             FROM `users`
             ORDER BY `created_at` DESC, `user_id` DESC'
        );

        return $statement->fetchAll();
    }

    // Change a user role between member and admin.
    public function updateRole($userId, $role)
    {
        if (!in_array($role, array('member', 'admin'), true)) {
            return false;
        }

        $statement = $this->db->prepare(
            'UPDATE `users`
             SET `role` = ?
             WHERE `user_id` = ?'
        );

        return $statement->execute(array($role, (int) $userId));
    }

    // Delete a user account by ID.
    public function delete($userId)
    {
        $statement = $this->db->prepare(
            'DELETE FROM `users`
             WHERE `user_id` = ?'
        );
        $statement->execute(array((int) $userId));

        return $statement->rowCount() === 1;
    }
}