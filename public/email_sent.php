<?php
require("../config/config.php");
require("../config/default_user.php");

// Import classes to the current namespace so we can use them without prefixing with the full namespace.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Load the package's classes, require composer's autoloader.
require "../vendor/autoload.php";

if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['message-body'])) {
    $name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email   = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $message = filter_input(INPUT_POST, 'message-body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = $_POST['message-body'];

    // Configure phpmailer to throw an exception if there is a problem
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 25;

    $mail->Username = "nichanabong@gmail.com";
    $mail->Password = "tckx vdrf grii pkxr";

    $mail->setFrom($email, $name);
    $mail->addAddress("nichalearner@gmail.com");

    $mail->Subject = $subject;
    $mail->Body = strip_tags($message);

    $mail->send();
    echo ("email sent");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../resources/styles/mystyles.css">
    <title>Contact Us!</title>
</head>
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

<body>
    <main>
        <section class="insert-container">
            <div class="insert-form-container" id="sent-email">
                <h1>Contact</h1>
                <p>Thank you for your message!</p>
            </div>
        </section>
    </main>
</body>

</html>