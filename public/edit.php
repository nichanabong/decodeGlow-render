<?php
require('../config/config.php');

session_start();
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 3) {
        header("Location: index.php?unauthorized=true");
        exit;
    }
} else {
    // Default least privilege
    $_SESSION['role'] = 3;
}

// require('authenticate.php');
if (isset($_GET['id'])) { // Retrieve entry to be edited, if id GET parameter is in URL.
    // Sanitize ID
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Build the parameterized SQL query using the filtered id.
    $query = "SELECT post_title,
                     post_content
             FROM posts
             WHERE post_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    // Execute the SELECT and fetch the single row returned.
    $statement->execute();

    if ($statement->rowCount() > 0) {
        $row = $statement->fetch();
        $row_post_title   = $row['post_title'];
        $row_post_content = $row['post_content'];

        $imageQuery = "SELECT relative_path,
                          alt_text,
                          post_id
                FROM album
                WHERE post_id = :id
                LIMIT 1";

        $imageQueryStatement = $db->prepare($imageQuery);

        // Bind the :id parameter in the query to the sanitized $id specifying a binding-type of Integer
        $imageQueryStatement->bindValue('id', $id, PDO::PARAM_INT);
        $imageQueryStatement->execute();

        // Fetch the row selected by primary key id.
        $imageRow = $imageQueryStatement->fetch();
    } else {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}


function isAuthenticated()
{
    return isset($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/resources/styles/mystyles.css">
    <link rel="icon" href="images/favicon.png">
    <title>Edit Post - <?= $row_post_title ?></title>
    <script>
        function confirmDelete() {
            if (confirm("Are you sure you want to delete this post?")) {
                require('authenticate.php');
                if (isAuthenticated()) {
                    document.getElementById("updateForm").submit();
                    alert("Post deleted")
                } else {
                    alert("Please log in to delete this item.")
                    return false
                }
            } else {
                alert("Deletion cancelled")
                return false;
            }
        }
    </script>
</head>

<body>
    <!-- <div class="form-container"> -->
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <?php if ($id): ?>

        <header>
            <h1>Decode Glow</h1>
        </header>
        <nav>
            <ul>
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li class="admin-header"><a href="insert.php">Admin Upload</a></li>
                <li><a href="admin.php">Admin User Management</a></li>
                <li><a href="insert_products.php">Admin Upload Products</a></li>
                <li><a href="login.php">LogIn | SignUp</a></li>
            </ul>
            <a href="logout.php" class="logout-link">Log Out</a>
        </nav>
        <main>
            <section class="insert-container">
                <div class="insert-form-container">
                    <form id="updateForm" method="post" action="update.php">
                        <!-- <div class="form-wrapper"> -->
                        <div class="form-field">
                            <!-- Hidden input for the primary key. -->
                            <input type="hidden" name="post_id" value="<?= $id ?>">
                            <h1 class="form-title">Edit Post</h1>
                            <!-- Blog title and content are echoed into the input value attributes. -->
                            <!-- <label for="post_title">Title</label>
                            <div class="input-container">
                                <textarea name="post_title" id="post_title" rows="2" required><?= $row_post_title ?></textarea>
                            </div> -->
                            <div class="form-field">
                                <label for="post_title">Title</label>
                                <input type="text" name="post_title" id="post_title" value="<?= $row_post_title ?>">
                            </div>
                        </div>
                        <div>
                            <?php if ($imageRow && $imageRow['post_id'] == $id): ?>
                                <?php

                                // Setting variables
                                $imageRow_post_id  = $imageRow['post_id'];
                                $imageRow_path     = $imageRow['relative_path'];
                                $imageRow_alt_text = $imageRow['alt_text'];
                                ?>
                                <img src="../storage/uploads/<?= basename($imageRow_path, '.' .
                                                                    pathinfo($imageRow_path, PATHINFO_EXTENSION)) . '_medium.' .
                                                                    pathinfo($imageRow_path, PATHINFO_EXTENSION) ?>" alt="<?= $imageRow_alt_text ?>">

                                <label for="deletePhoto" class="checkbox">
                                    <input type="checkbox" name="deletePhoto" id="deletePhoto" value="deletePhoto">
                                    Remove Photo
                                </label>
                            <?php endif ?>
                        </div>
                        <div class="form-field">
                            <label for="post_content">Content</label>
                            <div class="input-container">
                                <textarea name="post_content" id="post_content" rows="8" required><?= $row_post_content ?></textarea>
                            </div>
                        </div>
                        <div class="button-container">
                            <input id="submitBtn" class="button" type="submit" name="update" value="Update">
                            <input class="button" type="submit" name="delete" value="Delete" onclick="return confirmDelete()">
                        </div>

                    </form>

                </div>
            </section>
            <script>
                document.getElementById("submitBtn").addEventListener("click", function(event) {
                    var title = document.getElementById("title").value;
                    var content = document.getElementById("content").value;

                    if (title.trim() === "" || content.trim() === "") {
                        // Prevent form submission
                        event.preventDefault();
                        alert("Both the title and content are required.");
                    }
                });
            </script>

        </main>
    <?php else: ?>
        <?php header("Location: index.php") ?>
        <?php exit ?>
    <?php endif ?>
    <!-- </div> -->
    <script src="https://cdn.tiny.cloud/1/5ttqxefibma4vwo25rm488g7sw7jqnqlgfdfhzvtbqm0sws6/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="../resources/scripts/wysiwyg.js"></script>
</body>

</html>