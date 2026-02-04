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
    var_dump($email, $name);

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

    // The rewriting of the From address is done by Gmail for security reason (fight SPAM, phishing, etc.), not by PHPMailer. Gmail (and many others) doesn't allow sending messages with random From addresses. https://github.com/PHPMailer/PHPMailer/issues/1214
    $mail->setFrom($email, $name);

    $mail->addReplyTo($email, $name);
    $mail->addAddress("nichalearner@gmail.com");

    $mail->Subject = $subject;
    $mail->Body = strip_tags($message);

    $mail->send();

    header("Location: email_sent.php");
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
            <div class="insert-form-container">
                <!-- <div class="container"> -->
                <form action="contact_form.php" method="post" id="contact-form">
                    <h1>Contact Decode Glow</h1>
                    <label>
                        <span>Name</span>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            autocomplete="off" />
                    </label>
                    <label>
                        <span>Email</span>
                        <input
                            type="email"
                            name="email"
                            id="email" />
                    </label>
                    <label>
                        <span>Subject</span>
                        <input
                            type="text"
                            name="subject"
                            id="subject"
                            autocomplete="off" />
                    </label>
                    <label>
                        <span>Message</span>
                        <textarea name="message-body" id="post_content" cols="6"></textarea>
                    </label>
                    <label>
                        <button class="button" type="submit">
                            Send
                        </button>
                    </label>
                </form>
                <!-- </div> -->
            </div>
        </section>
    </main>
    <script src="https://cdn.tiny.cloud/1/5ttqxefibma4vwo25rm488g7sw7jqnqlgfdfhzvtbqm0sws6/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="../resources/scripts/wysiwyg.js"></script>
</body>

</html>