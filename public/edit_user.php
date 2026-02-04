<?php

require('../config/config.php');
require('authenticate.php');

// require('authenticate.php');
if (isset($_GET['id'])) { // Retrieve entry to be edited, if id GET parameter is in URL.
    // Sanitize ID
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Build the parameterized SQL query using the filtered id.
    $query = "SELECT * FROM users WHERE user_id = :id";

    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    // Execute the SELECT and fetch the single row returned.
    $statement->execute();

    if ($statement->rowCount() > 0) {
        $row = $statement->fetch();
    } else {
        $error = "Error encountered while fetching the data.";
    }
} else {
    if (isset($_POST) && !empty($_POST['username'] && !empty($_POST['role']))) {

        $hi = "Hello";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../resources/styles/users.css">
    <title>Document</title>
</head>

<body>
    <main>
        <div class="container">
            <form method="post" action="admin.php">
                <h3>Edit User</h3>
                <label>
                    <span>Username</span>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value=<?= $row['user_name'] ?>
                        autocomplete="off" />
                </label>
                <label>
                    <span>Role Type</span>
                    <select name="role" id="role">
                        <option value="3">User</option>
                        <option value="2">Editor</option>
                        <option value="1">Admin</option>
                    </select>
                </label>
                <label>
                    <button class="button" type="submit">
                        Save Changes
                    </button>
                </label>
            </form>
            <?php if (!empty($hi)): ?>
                <p><?= $hi ?></p>
            <?php endif ?>
        </div>
        <script>
            const deleteUser = ($id) => {
                if (confirm("Are you sure you want to delete this user?")) {
                    <?php
                    $query = "SELECT * FROM users WHERE user_id = :id";

                    $statement = $db->prepare($query);
                    $statement->bindValue(':id', $id, PDO::PARAM_INT);  ?>
                    <?php if ($statement->execute()): ?>
                        alert("User deleted successfully!");
                    <?php else: ?>
                        alert("There was an issue deleting this user.");
                        return false;
                    <?php endif ?>
                } else {
                    alert('Deletion cancelled')
                    return false;
                }
            };
        </script>
    </main>
</body>

</html>