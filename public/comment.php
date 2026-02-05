<?php
require('/config/config.php');
require('/config/default_user.php');

$session_username = $_SESSION['username'];
$session_role = $_SESSION['role'];

if (isset($_GET['id'])) {
    // Sanitize ID
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Build the parameterized SQL query using the filtered id.
    $query = "SELECT product_name,
                     product_brand
             FROM products
             WHERE product_id = :id";

    $statement = $db->prepare($query);

    // Bind the :id parameter in the query to the sanitized $id specifying a binding-type of Integer
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    // Fetch the row selected by primary key id.
    $row = $statement->fetch();
} else {
    header("Location: products.php");
    exit;
}

if (!empty($_POST['Comment'])) {

    if ($_POST['captcha'] != $_SESSION['digit']) die("Sorry, the CAPTCHA code entered was incorrect!");
    unset($_SESSION['digit']);
    // Skipping sanitization because of WYSISWYG
    $comment = $_POST['comment'];

    // Insert post details into the posts table
    $query = "INSERT INTO reviews (product_id, user_id, comment, comment_date_created) 
                VALUES (:product_id, :user_id, :comment, now())";

    $statement = $db->prepare($query);

    $statement->bindValue(':product_id', $id);
    $statement->bindValue(':user_id', $userid);
    $statement->bindValue(':comment', $comment);

    // If post insertion is successful:
    if ($statement->execute()) {
        // Console output for debugging
        echo "<script>console.log('Comment successfully added!');</script>";
        header("Location: products.php");
    } else {
        echo "<script>console.log('Error uploading post.');</script>";
        print_r($statement->errorInfo());
    }
}

$_SESSION['username'] = $session_username;
$_SESSION['role'] = $session_role;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/resources/styles/mystyles.css">
    <link rel="icon" href="images/favicon.png">
    <title>Comment</title>

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
                <form method="post" action="comment.php?id=<?= $id ?>">
                    <!-- <div class="form-wrapper"> -->
                    <div class="form-field">
                        <h1 class="form-title"><?= $row['product_brand'] ?></h1>
                        <div class="form-field">
                            <label><?= $row['product_name'] ?></label>
                            <label>Leave a comment:</label>
                        </div>
                    </div>
                    <div class="form-field">
                        <!-- <label for="post_content">Leave a comment:</label> -->
                        <div class="input-container">
                            <textarea name="comment" rows="8"></textarea>
                        </div>
                    </div>
                    <div class="form-field">
                        <p>
                        <p><img src="./captcha.php" width="120" height="30" border="1" alt="CAPTCHA"></p>
                        </p>
                    </div>
                    <div class="form-field">
                        <input type="text" name="captcha" placeholder="Enter the CAPTCHA" required>
                    </div>

                    <div class="button-container">
                        <input id="submitBtn" class="button" type="submit" name="Comment" value="Submit Comment">
                    </div>

                </form>

            </div>
        </section>
    </main>
</body>


</html>