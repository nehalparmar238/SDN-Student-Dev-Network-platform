<?php

class EventRegistration
{
    private $db;

    // Store the shared database connection for event registration queries.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Check whether one member is registered for one event.
    public function exists($eventId, $userId)
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM `event_registration`
             WHERE `event_id` = ? AND `user_id` = ?'
        );
        $statement->execute(array((int) $eventId, (int) $userId));

        return (int) $statement->fetchColumn() > 0;
    }

    // Count registrations for one event.
    public function countForEvent($eventId)
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM `event_registration`
             WHERE `event_id` = ?'
        );
        $statement->execute(array((int) $eventId));

        return (int) $statement->fetchColumn();
    }

    // Register one member and return false if the unique registration already exists.
    public function create($eventId, $userId)
    {
        $statement = $this->db->prepare(
            'INSERT INTO `event_registration` (`event_id`, `user_id`)
             VALUES (?, ?)'
        );

        try {
            return $statement->execute(array((int) $eventId, (int) $userId));
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return false;
            }

            throw $exception;
        }
    }

    // Remove one member's registration from an event.
    public function delete($eventId, $userId)
    {
        $statement = $this->db->prepare(
            'DELETE FROM `event_registration`
             WHERE `event_id` = ? AND `user_id` = ?'
        );
        $statement->execute(array((int) $eventId, (int) $userId));

        return $statement->rowCount() === 1;
    }

    // Return all events registered by one member.
    public function findByUser($userId)
    {
        $statement = $this->db->prepare(
            'SELECT `event`.`event_id`, `event`.`title`,
                    `event`.`description`, `event`.`event_type`,
                    `event`.`event_date`, `event`.`venue`,
                    `event`.`capacity`,
                    `event_registration`.`registered_on`,
                    (SELECT COUNT(*)
                     FROM `event_registration` AS `registration_count_query`
                     WHERE `registration_count_query`.`event_id` = `event`.`event_id`)
                     AS `registration_count`
             FROM `event_registration`
             INNER JOIN `event`
               ON `event`.`event_id` = `event_registration`.`event_id`
             WHERE `event_registration`.`user_id` = ?
             ORDER BY `event`.`event_date` DESC'
        );
        $statement->execute(array((int) $userId));

        return $statement->fetchAll();
    }
}