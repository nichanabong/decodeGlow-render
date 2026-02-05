<?php

/*******w******** 
    
    Name: Nicha Flor Nabong
    Date: 11/3/2024
    Description: New post page for the Decode Glow - Create a new post.

 ****************/

require('../config/config.php');
require('../config/default_user.php');

$querySelect = "SELECT product_category_id, product_category
FROM product_categories";

// A PDO::Statement is prepared from the query.
$statementRetrieve = $db->prepare($querySelect);

// Execution on the DB server is delayed until we execute().
$statementRetrieve->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/resources/styles/mystyles.css">
    <link rel="icon" href="images/favicon.png">
    <title>Admin Page</title>
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
        <a href="logout.php" class="logout-link">Log Out</a>
    </nav>
    <main>
        <section class="insert-container">
            <div class="insert-form-container">

                <!-- New Post Form -->
                <form method="post" action="file_product_upload.php" enctype="multipart/form-data" class="file-upload-form" id="uploadForm">
                    <h1>New Product</h1>
                    <div class="form-field">
                        <label for="file">Browse</label>
                        <input type="file" name="file" id="file">
                    </div>
                    <div class="form-field">
                        <label for="product_name">Product Name</label>
                        <input type="text" name="product_name" id="product_name" required></input>
                    </div>
                    <div class="form-field">
                        <label for="product_brand">Brand</label>
                        <input type="text" name="product_brand" id="product_brand" rows="2" required></input>
                    </div>
                    <label for="featureProduct" class="checkbox">
                        <input type="checkbox" name="featureProduct" id="featureProduct" value="featureProduct">
                        Feature Product
                    </label>
                    <div class="form-field">
                        <label for="product_description">Description</label>
                        <textarea name="product_description" id="post_content"></textarea>
                    </div>
                    <div class="form-field">
                        <label for="category">Choose a category:</label>
                        <select name="product_category_id" id="category" required>
                            <?php while ($row = $statementRetrieve->fetch()): ?>
                                <option value="<?= htmlspecialchars($row['product_category_id']); ?>">
                                    <?= htmlspecialchars($row['product_category']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="button-container">
                        <button type="submit" id="submitBtn" class="button">Submit</button>
                    </div>
                </form>
                <!-- Form content remains the same, include the following message for errors -->
                <?php if (isset($_SESSION['upload_error'])): ?>
                    <p><?= $_SESSION['upload_error']; ?></p>
                    <?php unset($_SESSION['upload_error']); ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <script src="https://cdn.tiny.cloud/1/5ttqxefibma4vwo25rm488g7sw7jqnqlgfdfhzvtbqm0sws6/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="../resources/scripts/wysiwyg.js"></script>
</body>



</html>