<?php

class Application
{
    private $db;

    // Store the shared database connection for application queries.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Create a pending application and return false when the member already applied.
    public function create($projectId, $applicantId, $message)
    {
        $statement = $this->db->prepare(
            "INSERT INTO `project_application`
                (`project_id`, `applicant_id`, `message`, `status`)
             VALUES (?, ?, ?, 'pending')"
        );

        try {
            return $statement->execute(array(
                (int) $projectId,
                (int) $applicantId,
                $message
            ));
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return false;
            }

            throw $exception;
        }
    }

    // Find one application together with its project owner and title.
    public function findById($applicationId)
    {
        $statement = $this->db->prepare(
            'SELECT `project_application`.`application_id`,
                    `project_application`.`project_id`,
                    `project_application`.`applicant_id`,
                    `project_application`.`message`,
                    `project_application`.`status`,
                    `project_application`.`applied_on`,
                    `project`.`owner_id`,
                    `project`.`title` AS `project_title`
             FROM `project_application`
             INNER JOIN `project`
               ON `project`.`project_id` = `project_application`.`project_id`
             WHERE `project_application`.`application_id` = ?
             LIMIT 1'
        );
        $statement->execute(array((int) $applicationId));

        return $statement->fetch();
    }

    // Return a member's applications with project and owner details.
    public function findByApplicant($applicantId)
    {
        $statement = $this->db->prepare(
            'SELECT `project_application`.`application_id`,
                    `project_application`.`project_id`,
                    `project_application`.`message`,
                    `project_application`.`status`,
                    `project_application`.`applied_on`,
                    `project`.`title` AS `project_title`,
                    `project`.`status` AS `project_status`,
                    `users`.`name` AS `owner_name`
             FROM `project_application`
             INNER JOIN `project`
               ON `project`.`project_id` = `project_application`.`project_id`
             INNER JOIN `users`
               ON `users`.`user_id` = `project`.`owner_id`
             WHERE `project_application`.`applicant_id` = ?
             ORDER BY `project_application`.`applied_on` DESC'
        );
        $statement->execute(array((int) $applicantId));

        return $statement->fetchAll();
    }

    // Return applications for projects owned by a member, grouped by project in title order.
    public function findReceivedByOwner($ownerId)
    {
        $statement = $this->db->prepare(
            'SELECT `project_application`.`application_id`,
                    `project_application`.`project_id`,
                    `project_application`.`applicant_id`,
                    `project_application`.`message`,
                    `project_application`.`status`,
                    `project_application`.`applied_on`,
                    `project`.`title` AS `project_title`,
                    `users`.`name` AS `applicant_name`,
                    `users`.`college_course` AS `applicant_course`
             FROM `project_application`
             INNER JOIN `project`
               ON `project`.`project_id` = `project_application`.`project_id`
             INNER JOIN `users`
               ON `users`.`user_id` = `project_application`.`applicant_id`
             WHERE `project`.`owner_id` = ?
             ORDER BY `project`.`title` ASC,
                      `project_application`.`applied_on` DESC'
        );
        $statement->execute(array((int) $ownerId));

        return $statement->fetchAll();
    }

    // Change an application only while its current status is pending.
    public function updatePendingStatus($applicationId, $status)
    {
        if (!in_array($status, array('accepted', 'rejected', 'withdrawn'), true)) {
            return false;
        }

        $statement = $this->db->prepare(
            'UPDATE `project_application`
             SET `status` = ?
             WHERE `application_id` = ? AND `status` = \'pending\''
        );
        $statement->execute(array($status, (int) $applicationId));

        return $statement->rowCount() === 1;
    }

    // Count all pending applications across the platform for the admin dashboard.
    public function countPending()
    {
        $statement = $this->db->query(
            "SELECT COUNT(*)
             FROM `project_application`
             WHERE `status` = 'pending'"
        );

        return (int) $statement->fetchColumn();
    }

    // Return every application on the platform for administrator review.
    public function findAll()
    {
        $statement = $this->db->query(
            'SELECT `project_application`.`application_id`,
                    `project_application`.`project_id`,
                    `project_application`.`applicant_id`,
                    `project_application`.`message`,
                    `project_application`.`status`,
                    `project_application`.`applied_on`,
                    `project`.`title` AS `project_title`,
                    `applicant`.`name` AS `applicant_name`,
                    `owner`.`name` AS `owner_name`
             FROM `project_application`
             INNER JOIN `project`
               ON `project`.`project_id` = `project_application`.`project_id`
             INNER JOIN `users` AS `applicant`
               ON `applicant`.`user_id` = `project_application`.`applicant_id`
             INNER JOIN `users` AS `owner`
               ON `owner`.`user_id` = `project`.`owner_id`
             ORDER BY `project_application`.`applied_on` DESC,
                      `project_application`.`application_id` DESC'
        );

        return $statement->fetchAll();
    }
}