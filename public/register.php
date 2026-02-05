<?php
session_start();
require('../config/config.php');

if ($_POST && !empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['confirm-pass'])) {

    // Santize user input
    $email        = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $username     = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password     = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirm_pass = filter_input(INPUT_POST, 'confirm-pass', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Verify if passwords matched
    if ($password === $confirm_pass) {

        $hash_pass = password_hash($password, PASSWORD_DEFAULT);

        // Verify if email is unique
        $verifyEmail = "SELECT * FROM users WHERE user_email = :email";

        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($verifyEmail);
        $statement->bindValue(':email', $email);

        // Execution on the DB server is delayed until we execute().
        $statement->execute();

        if ($statement->rowCount() == 0) {
            $query = "INSERT INTO users (user_name, user_email, user_password_hash)
                    VALUES (:username, :email, :hash_pass)";

            $statement = $db->prepare($query);

            $statement->bindValue(':username', $username);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':hash_pass', $hash_pass);

            if ($statement->execute()) {
                header("location: login.php?new_user=true");
                exit;
            } else {
                $error = "An error occured during registration";
            }
        } else {
            $error = "The email provided is already in use. Please try another one.";
        }
    } else {
        $error = "Passwords didn't match. Try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/resources/styles/register.css">
    <title>Login | SignUp</title>
</head>

<body>
    <main>
        <form action="register.php" method="post">
            <h3>Register</h3>

            <label>
                <span>Email</span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="off" />
            </label>

            <label>
                <span>Username</span>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    autocomplete="off" />
            </label>

            <label>
                <span>Password</span>
                <div class="passwd-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="pass"
                        placeholder="Enter your password"
                        autocomplete="off" />

                    <button type="button" class="show-passwd">
                        <img src="../resources/images/eye_closed.svg" alt="Show Password" />
                    </button>
                </div>
            </label>

            <label>
                <span>Confirm Password</span>
                <div class="passwd-wrap">
                    <input
                        type="password"
                        id="password"
                        name="confirm-pass"
                        class="pass"
                        placeholder="Confirm your password"
                        autocomplete="off" />

                    <button type="button" class="show-passwd">
                        <img src="../resources/images/eye_closed.svg" alt="Show Password" />
                    </button>
                </div>
            </label>

            <label>
                <button class="button" type="submit">
                    Sign Up
                </button>
            </label>
            <?php if (!empty($error)): ?>
                <div class="message">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif ?>
        </form>
    </main>
    <script src="../resources/scripts/main.js"></script>
</body>

</html>