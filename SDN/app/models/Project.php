<?php

class Project
{
    private $db;

    // Store the shared database connection for project queries.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Return projects matching the optional keyword, category, and technology filters.
    public function findAll($filters = array())
    {
        $sql = 'SELECT `project`.`project_id`, `project`.`owner_id`,
                       `project`.`category_id`, `project`.`title`,
                       `project`.`description`, `project`.`tech_stack`,
                       `project`.`team_size_needed`, `project`.`status`,
                       `project`.`created_at`,
                       `project_category`.`category_name`,
                       `users`.`name` AS `owner_name`
                FROM `project`
                INNER JOIN `project_category`
                  ON `project_category`.`category_id` = `project`.`category_id`
                INNER JOIN `users`
                  ON `users`.`user_id` = `project`.`owner_id`
                WHERE 1 = 1';
        $parameters = array();

        if (isset($filters['q']) && $filters['q'] !== '') {
            $sql .= ' AND (`project`.`title` LIKE ? OR `project`.`description` LIKE ?)';
            $pattern = '%' . $filters['q'] . '%';
            $parameters[] = $pattern;
            $parameters[] = $pattern;
        }

        if (isset($filters['category_id']) && (int) $filters['category_id'] > 0) {
            $sql .= ' AND `project`.`category_id` = ?';
            $parameters[] = (int) $filters['category_id'];
        }

        if (isset($filters['tech']) && $filters['tech'] !== '') {
            $sql .= ' AND `project`.`tech_stack` LIKE ?';
            $parameters[] = '%' . $filters['tech'] . '%';
        }

        $sql .= ' ORDER BY `project`.`created_at` DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    // Return the three newest open projects for the home page.
    public function findLatestOpenForHome()
    {
        $statement = $this->db->query(
            "SELECT `project`.`project_id`, `project`.`title`,
                    `project`.`description`, `project`.`tech_stack`,
                    `project_category`.`category_name`
             FROM `project`
             INNER JOIN `project_category`
               ON `project_category`.`category_id` = `project`.`category_id`
             WHERE `project`.`status` = 'open'
             ORDER BY `project`.`created_at` DESC
             LIMIT 3"
        );

        return $statement->fetchAll();
    }

    // Find one project with its owner and category details.
    public function findById($projectId)
    {
        $statement = $this->db->prepare(
            'SELECT `project`.`project_id`, `project`.`owner_id`,
                    `project`.`category_id`, `project`.`title`,
                    `project`.`description`, `project`.`tech_stack`,
                    `project`.`team_size_needed`, `project`.`status`,
                    `project`.`created_at`,
                    `project_category`.`category_name`,
                    `users`.`name` AS `owner_name`
             FROM `project`
             INNER JOIN `project_category`
               ON `project_category`.`category_id` = `project`.`category_id`
             INNER JOIN `users`
               ON `users`.`user_id` = `project`.`owner_id`
             WHERE `project`.`project_id` = ?
             LIMIT 1'
        );
        $statement->execute(array((int) $projectId));

        return $statement->fetch();
    }

    // Return all projects owned by one member.
    public function findByOwner($ownerId)
    {
        $statement = $this->db->prepare(
            'SELECT `project`.`project_id`, `project`.`title`,
                    `project`.`status`, `project`.`created_at`,
                    `project_category`.`category_name`
             FROM `project`
             INNER JOIN `project_category`
               ON `project_category`.`category_id` = `project`.`category_id`
             WHERE `project`.`owner_id` = ?
             ORDER BY `project`.`created_at` DESC'
        );
        $statement->execute(array((int) $ownerId));

        return $statement->fetchAll();
    }

    // Return members whose applications to the project were accepted.
    public function findAcceptedMembers($projectId)
    {
        $statement = $this->db->prepare(
            "SELECT `users`.`user_id`, `users`.`name`, `users`.`college_course`
             FROM `project_application`
             INNER JOIN `users`
               ON `users`.`user_id` = `project_application`.`applicant_id`
             WHERE `project_application`.`project_id` = ?
               AND `project_application`.`status` = 'accepted'
             ORDER BY `users`.`name` ASC"
        );
        $statement->execute(array((int) $projectId));

        return $statement->fetchAll();
    }

    // Find a member's existing application or return false when none exists.
    public function findApplicationByMember($projectId, $memberId)
    {
        $statement = $this->db->prepare(
            'SELECT `application_id`, `status`
             FROM `project_application`
             WHERE `project_id` = ? AND `applicant_id` = ?
             LIMIT 1'
        );
        $statement->execute(array((int) $projectId, (int) $memberId));

        return $statement->fetch();
    }

    // Create a new project with its required initial open status.
    public function create($ownerId, $categoryId, $title, $description, $techStack, $teamSizeNeeded)
    {
        $statement = $this->db->prepare(
            "INSERT INTO `project`
                (`owner_id`, `category_id`, `title`, `description`,
                 `tech_stack`, `team_size_needed`, `status`)
             VALUES (?, ?, ?, ?, ?, ?, 'open')"
        );

        return $statement->execute(array(
            (int) $ownerId,
            (int) $categoryId,
            $title,
            $description,
            $techStack,
            $teamSizeNeeded
        ));
    }

    // Update editable project details and status.
    public function update($projectId, $categoryId, $title, $description, $techStack, $teamSizeNeeded, $status)
    {
        $statement = $this->db->prepare(
            'UPDATE `project`
             SET `category_id` = ?, `title` = ?, `description` = ?,
                 `tech_stack` = ?, `team_size_needed` = ?, `status` = ?
             WHERE `project_id` = ?'
        );

        return $statement->execute(array(
            (int) $categoryId,
            $title,
            $description,
            $techStack,
            $teamSizeNeeded,
            $status,
            (int) $projectId
        ));
    }

    // Count all projects for the admin dashboard.
    public function countAll()
    {
        $statement = $this->db->query('SELECT COUNT(*) FROM `project`');

        return (int) $statement->fetchColumn();
    }

    // Update only the status of a project from the admin panel.
    public function updateStatus($projectId, $status)
    {
        if (!in_array($status, array('open', 'in_progress', 'closed'), true)) {
            return false;
        }

        $statement = $this->db->prepare(
            'UPDATE `project`
             SET `status` = ?
             WHERE `project_id` = ?'
        );

        return $statement->execute(array($status, (int) $projectId));
    }

    // Delete a project by its ID.
    public function delete($projectId)
    {
        $statement = $this->db->prepare(
            'DELETE FROM `project`
             WHERE `project_id` = ?'
        );
        $statement->execute(array((int) $projectId));

        return $statement->rowCount() === 1;
    }
}