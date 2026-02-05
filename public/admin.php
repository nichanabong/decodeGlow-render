<?php
session_start();
require('../config/config.php');
require('admin_only_auth.php');

isset($_SESSION['username']) ? $user = $_SESSION['username'] : $user = 'Guest';

$query = "SELECT * FROM users";

$statement = $db->prepare($query);
$statement->execute();

if (isset($_POST) && !empty($_POST['username']) && !empty($_POST['role'])) {
    // Sanitize user input to escape HTML entities and filter out dangerous characters.
    $username  = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);

    $update = "UPDATE users SET user_role_id = :role_type WHERE user_name = :username";

    $updateStatement = $db->prepare($update);
    $updateStatement->bindValue(':username', $username);
    $updateStatement->bindValue(':role_type', $role);

    $updateStatement->execute();

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<php lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/resources/styles/users.css">
        <link rel="stylesheet" href="/resources/styles/mystyles.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined"
            rel="stylesheet">
        <title>Admin | Manage Users</title>
    </head>

    <body>
        <header>
            <h1>Manage Users</h1>
        </header>
        <nav>
            <ul>
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <?php if (isset($_SESSION)): ?>
                    <?php if ($_SESSION['role'] != 3): ?>
                        <li class="admin-header"><a href="insert.php">Admin Upload</a></li>
                        <li><a href="admin.php">Admin User Management</a></li>
                        <li><a href="insert_products.php">Admin Upload Products</a></li>
                    <?php endif ?>
                <?php endif ?>
                <?php if ($user == "Guest"): ?>
                    <li><a href="login.php">LogIn | SignUp</a></li>
                <?php endif ?>
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="helloUser">Hello, <?= $user ?>!</li>
                <?php else: ?>
                    <li class="helloUser">Hello, <?= $user ?>!</li>
                <?php endif ?>
            </ul>
            <?php if ($user != "Guest"): ?>
                <a href="logout.php" class="logout-link">Log Out</a>
            <?php endif ?>
        </nav>
        <div class="table-container">
            <table>
                <tr>
                    <th>Username</th>
                    <th class="role">
                        Role Type
                    </th>
                    <th class="edit">
                        Edit
                    </th>
                </tr>
                <?php while ($row = $statement->fetch()): ?>
                    <tr>
                        <td>
                            <?= $row['user_name'] ?>
                        </td>
                        <td class="role">
                            <?= $row['user_role_id'] ?>
                        </td>
                        <td class="edit">
                            <a class="icons"
                                href="edit_user.php?id=<?= $row['user_id'] ?>">
                                <i class="material-symbols-outlined">edit</i>
                            </a>
                            <a class="icons"
                                href="delete_user.php?id=<?= $row['user_id'] ?>"
                                onclick="return confirmDelete()">
                                <i class="material-symbols-outlined">delete</i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile ?>
            </table>
        </div>
        <script>
            function confirmDelete() {
                if (confirm("Are you sure you want to delete this user?")) {
                    return true;
                } else {
                    false;
                }
            }
        </script>
    </body>

</php>