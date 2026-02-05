 <?php
   /* define('DB_DSN', 'mysql:host=localhost;dbname=decodeglow;charset=utf8');
    define('DB_USER', 'administrator');
    define('DB_PASS', '');

    //  PDO is PHP Data Objects
    //  mysqli <-- BAD. 
    //  PDO <-- GOOD.
    try {
        // Try creating new PDO connection to MySQL.
        $db = new PDO(DB_DSN, DB_USER, DB_PASS);
        //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    } catch (PDOException $e) {
        print "Error: " . $e->getMessage();
        die(); // Force execution to stop on errors.
        // When deploying to production you should handle this
        // situation more gracefully. ¯\_(ツ)_/¯
    } */
    $host = getenv('DB_HOST');
    $db   = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $port = getenv('DB_PORT') ?: 3306;

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

    try {
        $db = new PDO($dsn, $user, $pass);
    } catch (PDOException $e) {
        print "Error: " . $e->getMessage();
        die();
    }
    ?>