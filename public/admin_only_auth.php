 <?php

  if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] != 1) {
      header("Location: index.php?unauthorized=true");
      exit;
    }
  }

  ?>