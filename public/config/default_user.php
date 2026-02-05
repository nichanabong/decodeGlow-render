<?php
ob_start();

// Default user gets least privilege
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 3; 
}

$user = $_SESSION['username'] ?? 'Guest';

$query = "SELECT user_id
FROM users
WHERE 
user_name = :user";

$statement = $db->prepare($query);
$statement->bindValue(':user', $user);
$statement->execute();

// Fetch the row selected by primary key id.
$row = $statement->fetch(PDO::FETCH_ASSOC) ?: ['user_id' => 0];

$userid = $row['user_id'];
?>
