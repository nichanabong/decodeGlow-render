<?php
session_start();

// Default user gets least privilege
!isset($_SESSION['role']) ? $_SESSION['role'] = 3 : $_SESSION['role'];
!isset($_SESSION['username']) ? $user = 'Guest' : $user = $_SESSION['username'];


$query = "SELECT user_id
FROM users
WHERE 
user_name = :user";

$statement = $db->prepare($query);
$statement->bindValue(':user', $user);
$statement->execute();

// Fetch the row selected by primary key id.
$row = $statement->fetch();

if (!isset($row['user_id'])) {
    $row['user_id'] = 0;
}

$userid = $row['user_id'];
    ?>
