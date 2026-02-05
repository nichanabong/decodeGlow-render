<?php
require('/config/config.php');

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM users
                  WHERE user_id = :id";
    $statement = $db->prepare($query);

    // Bind the :id placeholder to the actual value of $id
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    // Execute DELETE.
    if ($statement->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        echo 'Error was encountered while deleting the user.';
    }
}
