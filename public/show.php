<?php

/*******w******** 
    Name: Nicha Flor Nabong
    Date: 11/3/2024
    Description: Displays an individual post or product based on a passed ID ($_GET).
 ****************/

require('../config/config.php');
require('../config/default_user.php');

// Check if 'id' exists in the query and determine the category (blog post or product).
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); // Ensure 'id' is a number.

    if (isset($_GET['product'])) {
        $category = "product";
        $query = "SELECT product_id, product_name, product_brand, product_description 
                  FROM products 
                  WHERE product_id = :id";

        $imageQuery = "SELECT relative_path, alt_text 
                       FROM products_album 
                       WHERE product_id = :id 
                       LIMIT 1";
    } else {
        $category = "blogpost";
        $query = "SELECT post_id, post_title, 
                         DATE_FORMAT(post_date, '%M %d, %Y, %l:%i %p') AS formatted_date, 
                         post_content 
                  FROM posts 
                  WHERE post_id = :id 
                  LIMIT 1";

        $imageQuery = "SELECT relative_path, alt_text 
                       FROM album 
                       WHERE post_id = :id 
                       LIMIT 1";
    }

    // Fetch main data.
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $row = $statement->fetch();

    if ($row) {
        // Fetch image data.
        $imageStatement = $db->prepare($imageQuery);
        $imageStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $imageStatement->execute();
        $imageRow = $imageStatement->fetch();
    } else {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../resources/styles/mystyles.css">
    <link rel="icon" href="images/favicon.png">
    <title><?= $category === "product" ? $row['product_name'] : $row['post_title'] ?></title>
</head>

<body>
    <header>
        <h1>Decode Glow</h1>
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
            <li><a href="contact_form.php">Contact Us</a></li>
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
    <main>
        <section class="insert-container">
            <div class="insert-form-container">
                <h1><?= $category === "product" ? $row['product_name'] : $row['post_title'] ?></h1>
                <?php if ($category === "blogpost"): ?>
                    <p><?= $row['formatted_date'] ?></p>
                    <?php if ($_SESSION['role'] != 3): ?>
                        <a href=" edit.php?id=<?= $row['post_id'] ?>">Edit</a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($imageRow): ?>
                    <?php
                    // Separate the file name and extension
                    $fileInfo = pathinfo($imageRow['relative_path']);
                    $mediumFileName = $fileInfo['filename'] . '_medium.' . $fileInfo['extension'];
                    ?>
                    <img src="../storage/<?= $category === 'product' ? 'product_uploads' : 'uploads' ?>/<?= $mediumFileName ?>"
                        alt="<?= $imageRow['alt_text'] ?>">
                <?php endif; ?>

                <div>
                    <!-- Non-logged users cannot add comment -->
                    <?php if ($category === "product" && $user != "Guest"): ?>
                        <a href="comment.php?id=<?= $row['product_id'] ?>">Add Comment</a>
                    <?php endif ?>
                </div>

                <p><?= $category === "product" ? $row['product_description'] : $row['post_content'] ?></p>
            </div>
        </section>
    </main>
</body>

</html>