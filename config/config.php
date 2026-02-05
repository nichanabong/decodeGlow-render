<?php
    $host = getenv('DB_HOST');
    $db   = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $port = getenv('DB_PORT') ?: 3306;

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

    try {
        $db = new PDO($dsn, $user, $pass);
    } catch (PDOException $e) {
        print "Error: " . $e->getMessage();
        die();
    }
    ?>