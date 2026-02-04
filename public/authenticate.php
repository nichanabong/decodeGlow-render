 <?php
  session_start();
  if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 3) {
      header("Location: index.php?unauthorized=true");
      exit;
    }
  } else {
    // Default least privilege
    $_SESSION['role'] = 3;
  }
  ?>