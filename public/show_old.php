<?php

/*******w******** 
    
    Name: Nicha Flor Nabong
    Date: 11/3/2024
    Description: Shows an individual post based on a passed id ($_GET).

 ****************/
require('/config/config.php');
require('/config/default_user.php');

// Buid and prepare SQL string with :id placeholder parameter.
if (isset($_GET['id']) && isset($_GET['product'])) {

    $category = "product";

    // Sanitize $_GET['post_id'] to ensure it's a number.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT product_id, 
                     product_name,
                     product_brand,
                     product_description
        FROM products
        WHERE product_id = :product_id";

    $statement = $db->prepare($query);

    // Bind the :id parameter in the query to the sanitized $id specifying a binding-type of Integer
    $statement->bindValue('product_id', $id, PDO::PARAM_INT);
    $statement->execute();

    // Fetch the row selected by primary key id.
    $row = $statement->fetch();

    // Setting variables
    $row_product_name = $row['product_name'];
    $row_product_brand = $row['product_brand'];
    $row_product_description = $row['product_description'];


    $imageQuery = "SELECT relative_path,
                          alt_text,
                          product_id
                FROM products_album
                WHERE product_id = :product_id
                LIMIT 1";

    $imageQueryStatement = $db->prepare($imageQuery);

    // Bind the :id parameter in the query to the sanitized $id specifying a binding-type of Integer
    $imageQueryStatement->bindValue('product_id', $id, PDO::PARAM_INT);
    $imageQueryStatement->execute();

    // Fetch the row selected by primary key id.
    $imageRow = $imageQueryStatement->fetch();
} elseif (isset($_GET['id'])) {

    $category = "blogpost";

    // Sanitize $_GET['post_id'] to ensure it's a number.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT post_id, 
                     post_title,
                     DATE_FORMAT(post_date, '%M %d, %Y, %l:%i %p') AS formatted_date,
                     post_content
        FROM posts
        WHERE post_id = :post_id
        LIMIT 1";

    $statement = $db->prepare($query);

    // Bind the :id parameter in the query to the sanitized $id specifying a binding-type of Integer
    $statement->bindValue('post_id', $id, PDO::PARAM_INT);
    $statement->execute();

    // Fetch the row selected by primary key id.
    $row = $statement->fetch();

    // Setting variables
    $row_post_title = $row['post_title'];
    $row_post_date  = $row['formatted_date'];
    $row_post_id    = $row['post_id'];
    $row_post_content = $row['post_content'];


    $imageQuery = "SELECT relative_path,
                          alt_text,
                          post_id
                FROM album
                WHERE post_id = :post_id
                LIMIT 1";

    $imageQueryStatement = $db->prepare($imageQuery);

    // Bind the :id parameter in the query to the sanitized $id specifying a binding-type of Integer
    $imageQueryStatement->bindValue('post_id', $id, PDO::PARAM_INT);
    $imageQueryStatement->execute();

    // Fetch the row selected by primary key id.
    $imageRow = $imageQueryStatement->fetch();
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
    <title>READ: <?= $row['post_title'] ?></title>
</head>

<body>
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
        <?php if ($user != "Guest"): ?>
            <a href="logout.php" class="logout-link">Log Out</a>
        <?php endif ?>
    </nav>
    <main>
        <section class="insert-container">
            <div class="insert-form-container">
                <?php if ($id): ?>
                    <?php if ($category == "product"): ?>
                        <?php $id_reference = "product_id" ?>
                        <?php $title_reference = $row_product_name ?>
                        <?php $content_reference = $row_product_description ?>
                        <?php $post_date_reference = false ?>
                        <?php $dir_upload_reference = "product_uploads" ?>
                    <?php else: ?>
                        <?php $id_reference = "post_id" ?>
                        <?php $title_reference = $row_post_title ?>
                        <?php $content_reference = $row_post_content ?>
                        <?php $post_date_reference = true ?>
                        <?php $dir_upload_reference = "uploads" ?>

                    <?php endif ?>
                    <?php if ($imageRow && $imageRow[$id_reference] == $id): ?>
                        <?php
                        // Setting variables
                        $imageRow_post_id  = $imageRow[$id_reference];
                        $imageRow_path     = $imageRow['relative_path'];
                        $imageRow_alt_text = $imageRow['alt_text'];
                        ?>
                        <img src="../storage/<?= $dir_upload_reference ?>/<?= basename($imageRow_path, '.' .
                                                                                pathinfo($imageRow_path, PATHINFO_EXTENSION)) . '_medium.' .
                                                                                pathinfo($imageRow_path, PATHINFO_EXTENSION) ?>" alt="<?= $imageRow_alt_text ?>">
                    <?php endif ?>
                    <!-- Remember that alternative syntax is good and html inside php is bad -->
                    <h1><?= $title_reference ?></h1>
                    <?php if ($post_date_reference): ?>
                        <p>
                            <?= $row_post_date ?>
                        </p>
                    <?php endif ?>
                    <?php if ($_SESSION['role'] != 3): ?>
                        <a href=" edit.php?id=<?= $row_post_id ?>">Edit</a>
                    <?php endif ?>
                    <p id="content"><?= $content_reference ?></p>
                <?php else: ?>
                    <?php header("location: index.php"); ?>
                    <?php exit ?>
                <?php endif ?>
            </div>
        </section>
    </main>
</body>

</html>