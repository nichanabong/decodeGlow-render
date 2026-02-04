<?php
require('../config/config.php');
require('../config/default_user.php');

$querySelect = "SELECT category_id, category_name
FROM categories";

// A PDO::Statement is prepared from the query.
$statementRetrieve = $db->prepare($querySelect);

// Execution on the DB server is delayed until we execute().
$statementRetrieve->execute();

if ($_POST && !empty($_POST['post_title']) && !empty($_POST['post_content']) && !empty($_POST['category_id'])) {

    // Sanitize user input
    $post_title   = filter_input(INPUT_POST, 'post_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $post_content = filter_input(INPUT_POST, 'post_content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $post_content = $_POST['post_content'];
    $category_id  = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

    // Insert post details into the posts table
    $query = "INSERT INTO posts (post_title, post_content, category_id) 
                    VALUES (:title, :content, :category_id)";
    $statement = $db->prepare($query);

    $statement->bindValue(':title', $post_title);
    $statement->bindValue(':content', $post_content);
    $statement->bindValue(':category_id', $category_id);

    // If post insertion is successful:
    if ($statement->execute()) {
        header("location: index.php");
        exit;
    } else {
        echo 'Error uploading post.';
        print_r($statement->errorInfo());
    }
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
    <main>
        <section class="insert-container">
            <div class="insert-form-container">

                <!-- File Upload Form, initially hidden -->
                <form action="file_upload.php" method="post" enctype="multipart/form-data" class="file-upload-form" id="uploadForm">
                    <div class="form-field">
                        <label for="file">Browse</label>
                        <input type="file" name="file" id="file">
                    </div>
                    <h1>New Post</h1>
                    <div class="form-field">
                        <label for="post_title">Title</label>
                        <input type="text" name="post_title" id="post_title" required>
                    </div>
                    <div class="form-field">
                        <label for="post_content">Content</label>
                        <textarea name="post_content" id="post_content" rows="6"></textarea>
                    </div>
                    <div class="form-field">
                        <label for="category">Choose a category:</label>
                        <select name="category_id" id="category" onchange="addToInsertForm()" required>
                            <?php while ($row = $statementRetrieve->fetch()): ?>
                                <option value="<?= htmlspecialchars($row['category_id']); ?>">
                                    <?= htmlspecialchars($row['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="button-container">
                        <button type="submit" id="submitBtn" class="button">Submit Post</button>
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