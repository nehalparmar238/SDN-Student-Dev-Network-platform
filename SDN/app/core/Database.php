<?php

class Database
{
    private static $connection = null;

    // Return the shared PDO connection, creating it once when needed.
    public static function connect()
    {
        if (self::$connection === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            try {
                self::$connection = new PDO($dsn, DB_USER, DB_PASS, array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ));
            } catch (PDOException $exception) {
                exit('Database connection failed. Start MySQL in XAMPP and import database/schema.sql and seed.sql.');
            }
        }

        return self::$connection;
    }
}