<?php

/*******w******** 
    Name: 
    Date: 11/3/2024
    Description: Home page for Decode Glow
 ****************/
require('../config/config.php');
include('../app/truncate.php');
require('../config/default_user.php');

// Query to get all products
$query = "SELECT * FROM products";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute();

// Query to get the images
$imageQuery = "SELECT relative_path,
                      alt_text,
                      product_id
               FROM products_album";

$imageQueryStatement = $db->prepare($imageQuery);
$imageQueryStatement->execute();

// Query to get all reviews (comments)
$reviewQuery = "SELECT review_id, product_id, user_id, comment, comment_date_created
                FROM reviews
                ORDER BY comment_date_created DESC
                LIMIT 9";

$reviewQueryStatement = $db->prepare($reviewQuery);
$reviewQueryStatement->execute();

// Query to get all users
$userQuery = "SELECT user_id, user_name
                FROM users";

$userQueryStatement = $db->prepare($userQuery);
$userQueryStatement->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/resources/styles/mystyles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fragment+Mono:ital@0;1&display=swap" rel="stylesheet">
    <link rel="icon" href="images/favicon.png">
    <title>Decode: Glowy Skin ✨</title>
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
        <!-- Left column - Blog Post Side: -->
        <?php
        // Get all image data grouped by product_id
        $imagesByProductId = [];
        while ($imageRow = $imageQueryStatement->fetch()) {
            $imagesByProductId[$imageRow['product_id']][] = $imageRow;
        }

        // Get all reviews grouped by product_id
        $reviewsByProductId = [];
        while ($reviewRow = $reviewQueryStatement->fetch()) {
            $reviewsByProductId[$reviewRow['product_id']][] = $reviewRow;
        }

        // Get all users
        $userRows = [];
        while ($userRow = $userQueryStatement->fetch()) {
            $userRows[] = $userRow;
        }
        ?>

        <article id="productsShowAll">
            <?php while ($row = $statement->fetch()): ?>
                <!-- <div id="product_container"> -->
                <figure>
                    <h2><?= $row['product_name'] ?></h2>
                    <?php if (isset($imagesByProductId[$row['product_id']])): ?>
                        <?php foreach ($imagesByProductId[$row['product_id']] as $imageRow): ?>
                            <a href="show.php?id=<?= $row['product_id'] ?>&product=true">
                                <img src="../storage/product_uploads/<?= basename($imageRow['relative_path'], '.' .
                                                                            pathinfo($imageRow['relative_path'], PATHINFO_EXTENSION)) . '_medium.' .
                                                                            pathinfo($imageRow['relative_path'], PATHINFO_EXTENSION) ?>"
                                    alt="<?= $imageRow['alt_text'] ?>">
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </figure>
                <p><?= $row['product_brand'] ?></p>
                <p><?= $row['product_description'] ?></p>

                <!-- Display the comments for this product -->
                <div class="comments">
                    <h3>Reviews:</h3>
                    <?php if (isset($reviewsByProductId[$row['product_id']])): ?>
                        <?php foreach ($reviewsByProductId[$row['product_id']] as $review): ?>
                            <?php foreach ($userRows as $userRow): ?>
                                <?php if ($userRow['user_id'] == $review['user_id']): ?>
                                    <?php $username = $userRow['user_name'] ?>
                                    <div class="review">
                                        <p><strong>Comment:</strong> <?= htmlspecialchars($review['comment']) ?></p>
                                        <p><small>from user: <?= $username ?></small></p>
                                        <p><small>Posted on: <?= $review['comment_date_created'] ?></small></p>
                                    </div>
                                <?php endif ?>
                            <?php endforeach ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No reviews yet for this product.</p>
                    <?php endif; ?>
                </div>
                <!-- </div> -->
            <?php endwhile; ?>
        </article>
    </main>
    <footer>
        <p>&copy; 2024 Decode Glow. All rights reserved.</p>
        <p>Website designed by NNabong-WEBD2008.</p>
        <p><a href="privacy-policy.html">Privacy Policy</a> | <a href="terms-of-service.html">Terms of Service</a></p>
    </footer>

</body>

</html>