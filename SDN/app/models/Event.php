<?php

class Event
{
    private $db;

    // Store the shared database connection for event queries.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Return all future events, optionally filtered by event type.
    public function findUpcoming($eventType = '')
    {
        $sql = $this->listSelectSql() . ' WHERE `event`.`event_date` > NOW()';
        $parameters = array();

        if ($eventType !== '') {
            $sql .= ' AND `event`.`event_type` = ?';
            $parameters[] = $eventType;
        }

        $sql .= ' ORDER BY `event`.`event_date` ASC';
        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    // Return the three nearest future events for the homepage.
    public function findUpcomingForHome()
    {
        $statement = $this->db->query(
            $this->listSelectSql()
            . ' WHERE `event`.`event_date` > NOW()
               ORDER BY `event`.`event_date` ASC
               LIMIT 3'
        );

        return $statement->fetchAll();
    }

    // Return past events, optionally filtered by event type.
    public function findPast($eventType = '')
    {
        $sql = $this->listSelectSql() . ' WHERE `event`.`event_date` < NOW()';
        $parameters = array();

        if ($eventType !== '') {
            $sql .= ' AND `event`.`event_type` = ?';
            $parameters[] = $eventType;
        }

        $sql .= ' ORDER BY `event`.`event_date` DESC';
        $statement = $this->db->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    // Return all events for administrator management.
    public function findAll()
    {
        $statement = $this->db->query(
            $this->listSelectSql()
            . ' ORDER BY `event`.`event_date` DESC'
        );

        return $statement->fetchAll();
    }

    // Count all events on the platform for the admin dashboard.
    public function countAll()
    {
        $statement = $this->db->query('SELECT COUNT(*) FROM `event`');

        return (int) $statement->fetchColumn();
    }

    // Find one event and include its creator and registration count.
    public function findById($eventId)
    {
        $statement = $this->db->prepare(
            'SELECT `event`.`event_id`, `event`.`created_by`,
                    `event`.`title`, `event`.`description`,
                    `event`.`event_type`, `event`.`event_date`,
                    `event`.`venue`, `event`.`capacity`,
                    `users`.`name` AS `creator_name`,
                    (SELECT COUNT(*)
                     FROM `event_registration`
                     WHERE `event_registration`.`event_id` = `event`.`event_id`)
                     AS `registration_count`
             FROM `event`
             INNER JOIN `users`
               ON `users`.`user_id` = `event`.`created_by`
             WHERE `event`.`event_id` = ?
             LIMIT 1'
        );
        $statement->execute(array((int) $eventId));

        return $statement->fetch();
    }

    // Create an event for an administrator.
    public function create($createdBy, $title, $description, $eventType, $eventDate, $venue, $capacity)
    {
        $statement = $this->db->prepare(
            'INSERT INTO `event`
                (`created_by`, `title`, `description`, `event_type`,
                 `event_date`, `venue`, `capacity`)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        return $statement->execute(array(
            (int) $createdBy,
            $title,
            $description,
            $eventType,
            $eventDate,
            $venue,
            $capacity
        ));
    }

    // Update event details.
    public function update($eventId, $title, $description, $eventType, $eventDate, $venue, $capacity)
    {
        $statement = $this->db->prepare(
            'UPDATE `event`
             SET `title` = ?, `description` = ?, `event_type` = ?,
                 `event_date` = ?, `venue` = ?, `capacity` = ?
             WHERE `event_id` = ?'
        );

        return $statement->execute(array(
            $title,
            $description,
            $eventType,
            $eventDate,
            $venue,
            $capacity,
            (int) $eventId
        ));
    }

    // Delete an event by its ID.
    public function delete($eventId)
    {
        $statement = $this->db->prepare(
            'DELETE FROM `event`
             WHERE `event_id` = ?'
        );
        $statement->execute(array((int) $eventId));

        return $statement->rowCount() === 1;
    }

    // Return the shared event-list query with creator and registration details.
    private function listSelectSql()
    {
        return 'SELECT `event`.`event_id`, `event`.`created_by`,
                       `event`.`title`, `event`.`description`,
                       `event`.`event_type`, `event`.`event_date`,
                       `event`.`venue`, `event`.`capacity`,
                       `users`.`name` AS `creator_name`,
                       (SELECT COUNT(*)
                        FROM `event_registration`
                        WHERE `event_registration`.`event_id` = `event`.`event_id`)
                        AS `registration_count`
                FROM `event`
                INNER JOIN `users`
                  ON `users`.`user_id` = `event`.`created_by`';
    }
}