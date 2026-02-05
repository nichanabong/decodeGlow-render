<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/default_user.php';

include('/app/truncate.php');

$query = "SELECT post_id, 
                post_title, 
                DATE_FORMAT(post_date, '%M %d, %Y, %l:%i %p') AS date_posted, 
                DATE_FORMAT(post_modified, '%M %d, %Y, %l:%i %p') AS date_modified, 
                post_content,
                category_id
        FROM posts
        ORDER BY COALESCE(post_modified, post_date) DESC
        LIMIT 9";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute();

// Group posts by category
$postsByCategory = [];

while ($row = $statement->fetch()) {
    $categoryId = $row['category_id'];
    if (!isset($postsByCategory[$categoryId])) {
        $postsByCategory[$categoryId] = [];
    }
    $postsByCategory[$categoryId][] = $row;
}


// Query : Image Source
$imageQuery = "SELECT relative_path,
alt_text,
post_id
FROM album
LIMIT 3";

$imageQueryStatement = $db->prepare($imageQuery);
$imageQueryStatement->execute();

// Fetch the row selected by primary key id.
// $imageRow = $imageQueryStatement->fetch();
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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">
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
        <article id="blogBox">
            <!-- Category 3: Products Post -->
            <?php if (isset($postsByCategory[3])): ?>
                <?php while ($imageRow = $imageQueryStatement->fetch()): ?>
                    <?php
                    // Setting variables
                    $imageRow_post_id  = $imageRow['post_id'];
                    $imageRow_path     = $imageRow['relative_path'];
                    $imageRow_alt_text = $imageRow['alt_text'];
                    ?>
                    <figure>
                        <h2><?= $imageRow_alt_text ?></h2>
                        <a href="show.php?id=<?= $imageRow_post_id ?>">
                            <img src="/storage/uploads/<?= basename($imageRow_path, '.' .
                                                                pathinfo($imageRow_path, PATHINFO_EXTENSION)) . '_medium.' .
                                                                pathinfo($imageRow_path, PATHINFO_EXTENSION) ?>" alt="<?= $imageRow_alt_text ?>">
                        </a>
                    </figure>
                <?php endwhile ?>
            <?php endif; ?>
        </article>
        <!-- Left column - Blog Post Side: -->
        <article id="featuredBox">
            <!-- Category 1:  Blog Post/Skincare Tips -->
            <?php if (isset($postsByCategory[1])): ?>
                <?php foreach ($postsByCategory[1] as $post): ?>
                    <h2><a href="show.php?id=<?= $post['post_id'] ?>"><?= $post['post_title'] ?></a></h2>
                    <p>Posted: <?= $post['date_posted'] ?></p>
                    <?php if ($post['date_modified'] != ""): ?>
                        <p>Updated: <?= $post['date_modified'] ?></p>
                    <?php endif ?>
                    <?php $truncatedPost = truncate(text: $post['post_content'], id: $post['post_id']) ?>
                    <p class="truncated"><?= $truncatedPost ?></p>
                <?php endforeach ?>
            <?php endif; ?>
        </article>
        <!-- Right Column - Search Top, Featured Products Bottom: -->
        <div id="rightColumn">
            <article id="searchBox">
                <form id="search">
                    <input type="search" id="search" name="search" placeholder="Search...">
                    <button>Search</button>
                </form>
                <img src="../resources/images/dg_front_photo_medium.png" alt="Girl with moisturizer">
            </article>

        </div>
    </main>
    <footer>
        <p>&copy; 2024 Decode Glow. All rights reserved.</p>
        <p>Website designed by NNabong-WEBD2008.</p>
        <p><a href="privacy-policy.html">Privacy Policy</a> | <a href="terms-of-service.html">Terms of Service</a></p>
    </footer>

</body>

</html>