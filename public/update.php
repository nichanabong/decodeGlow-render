<?php

require('/config/config.php');

if ($_POST && isset($_POST['update'])) {
    // UPDATE blog if title, content and id are present in POST.
    if ($_POST && isset($_POST['post_title']) && isset($_POST['post_content']) && isset($_POST['post_id'])) {

        // Sanitize user input to escape HTML entities and filter out dangerous characters.
        $title  = filter_input(INPUT_POST, 'post_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // $content = filter_input(INPUT_POST, 'post_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = $_POST['post_content'];
        $id      = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);

        $dateTime = new DateTime("now", new DateTimeZone('America/Winnipeg')); //static Winnipeg for now
        $currentDate = $dateTime->format('Y-m-d H:i:s');

        // Build the parameterized SQL query and bind to the above sanitized values.
        $query     = "UPDATE posts SET post_title = :title, post_content = :content,  post_modified = :date_time WHERE post_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);
        $statement->bindValue(':date_time', $currentDate);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        // Execute the UPDATE.
        $statement->execute();

        if (isset($_POST['deletePhoto']) && $_POST['deletePhoto'] == 'deletePhoto') {
            $queryAlbumDeleteLocal = "SELECT * FROM album WHERE post_id = :id";
            $statementAlbumDeleteLocal = $db->prepare($queryAlbumDeleteLocal);
            $statementAlbumDeleteLocal->bindValue(':id', $id, PDO::PARAM_INT);

            // Set $files for local deletion
            if ($statementAlbumDeleteLocal->execute() && $statementAlbumDeleteLocal->rowCount() > 0) {
                $row = $statementAlbumDeleteLocal->fetch();

                $dir = "/storage/uploads";
                $filename = basename($row['relative_path'], '.' . pathinfo($row['relative_path'], PATHINFO_EXTENSION));
                $files = glob($dir . "/$filename*");
            }

            // Delete  from PHPMyAdmin
            $queryAlbumDelete = "DELETE FROM album
                          WHERE post_id = :id";
            $statementAlbum = $db->prepare($queryAlbumDelete);

            // Bind the :id placeholder to the actual value of $id
            $statementAlbum->bindValue(':id', $id, PDO::PARAM_INT);
            // Execute DELETE.
            if ($statementAlbum->execute()) {

                // Delete images from the local directory
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                // Redirect header
                header("Location: index.php");
                exit;
            } else {
                echo 'Error deleting record.';
            }
        }

        // Redirect after update.
        header("Location: index.php");
        exit;
    }
} elseif ($_POST && isset($_POST['delete'])) {
    // Sanitize ID
    $id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);

    // Build parameterized SQL query based on filtered id
    $queryPost = "DELETE FROM posts
                  WHERE post_id = :id";
    $statementPost = $db->prepare($queryPost);

    // Bind the :id placeholder to the actual value of $id
    $statementPost->bindValue(':id', $id, PDO::PARAM_INT);

    $queryAlbumDeleteLocal = "SELECT * FROM album WHERE post_id = :id";
    $statementAlbumDeleteLocal = $db->prepare($queryAlbumDeleteLocal);
    $statementAlbumDeleteLocal->bindValue(':id', $id, PDO::PARAM_INT);

    // Set $files for local deletion
    if ($statementAlbumDeleteLocal->execute() && $statementAlbumDeleteLocal->rowCount() > 0) {
        $row = $statementAlbumDeleteLocal->fetch();

        $dir = "/storage/uploads";
        $filename = basename($row['relative_path'], '.' . pathinfo($row['relative_path'], PATHINFO_EXTENSION));
        $files = glob($dir . "/$filename*");
    }

    // Delete  from PHPMyAdmin
    $queryAlbumDelete = "DELETE FROM album
                  WHERE post_id = :id";
    $statementAlbum = $db->prepare($queryAlbumDelete);

    // Bind the :id placeholder to the actual value of $id
    $statementAlbum->bindValue(':id', $id, PDO::PARAM_INT);

    // Execute DELETE.
    if ($statementPost->execute() && $statementAlbum->execute()) {

        // Delete images from the local directory
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // Redirect header
        header("Location: index.php?filename=<?= $filename ?>");
        exit;
    } else {
        echo 'Error deleting record.';
    }
}
