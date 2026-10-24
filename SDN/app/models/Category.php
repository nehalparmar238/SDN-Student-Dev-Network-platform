<?php

class Category
{
    private $db;

    // Store the shared database connection for category queries.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    // Return all project categories, sorted by name.
    public function findAll()
    {
        $sql = 'SELECT `category_id`, `category_name`, `description`
                FROM `project_category`
                ORDER BY `category_name` ASC';

        return $this->db->query($sql)->fetchAll();
    }

    // Alias kept so either name works.
    public function getAll()
    {
        return $this->findAll();
    }

    // Count all project categories for the admin dashboard.
    public function countAll()
    {
        $statement = $this->db->query('SELECT COUNT(*) FROM `project_category`');

        return (int) $statement->fetchColumn();
    }

    // Find one category by its ID.
    public function findById($categoryId)
    {
        $statement = $this->db->prepare(
            'SELECT `category_id`, `category_name`, `description`
             FROM `project_category`
             WHERE `category_id` = ?
             LIMIT 1'
        );
        $statement->execute(array((int) $categoryId));

        return $statement->fetch();
    }

    // Check whether a category ID exists.
    public function exists($categoryId)
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM `project_category`
             WHERE `category_id` = ?'
        );
        $statement->execute(array((int) $categoryId));

        return (int) $statement->fetchColumn() > 0;
    }

    // Create a new category and return false if the name already exists.
    public function create($categoryName, $description)
    {
        $statement = $this->db->prepare(
            'INSERT INTO `project_category` (`category_name`, `description`)
             VALUES (?, ?)'
        );

        try {
            return $statement->execute(array($categoryName, $description));
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return false;
            }

            throw $exception;
        }
    }

    // Update a category and return false if the new name duplicates another row.
    public function update($categoryId, $categoryName, $description)
    {
        $statement = $this->db->prepare(
            'UPDATE `project_category`
             SET `category_name` = ?, `description` = ?
             WHERE `category_id` = ?'
        );

        try {
            return $statement->execute(array(
                $categoryName,
                $description,
                (int) $categoryId
            ));
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return false;
            }

            throw $exception;
        }
    }

    // Delete a category only when no projects reference it.
    public function delete($categoryId)
    {
        $checkStatement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM `project`
             WHERE `category_id` = ?'
        );
        $checkStatement->execute(array((int) $categoryId));

        if ((int) $checkStatement->fetchColumn() > 0) {
            return false;
        }

        $deleteStatement = $this->db->prepare(
            'DELETE FROM `project_category`
             WHERE `category_id` = ?'
        );

        try {
            $deleteStatement->execute(array((int) $categoryId));

            return $deleteStatement->rowCount() === 1;
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return false;
            }

            throw $exception;
        }
    }
}