<?php
ob_start();
session_start();

require __DIR__ . '/../config/config.php';

if (isset($_POST) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "SELECT * FROM users WHERE user_name = :username";

    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);

    $statement->execute();

    if ($statement->rowCount() > 0) {

        while ($row = $statement->fetch()) {
            // Verify password
            if (password_verify($password, $row['user_password_hash'])) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $row['user_role_id'];

                // Redirect user to homepage
                header("Location: index.php?id=$row[user_id]");
            } else {
                $error = "Incorrect pasword. Try again.";
            }
        }
    } else {
        $error = "The email doesn't match any records in our system.";
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
        <div class="container">
            <?php if (isset($_GET['new_user'])): ?>
                <div class="login-message">
                    Your registration is successful! Please login.
                </div>
            <?php endif ?>
            <form action="login.php" method="post">
                <h3>Log In</h3>
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
                    <button class="button" type="submit">
                        Log In
                    </button>
                </label>
                <?php if (!empty($error)): ?>
                    <div class="message">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <div class="links">
                    Don't have an account yet? <a href="register.php">Sign up now</a>
                </div>
            </form>
        </div>

    </main>
    <script src="../resources/scripts/main.js"></script>
</body>

</html>