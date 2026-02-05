<?php
require('/config/config.php');
require('authenticate.php');

require '\xampp\htdocs\wd2\assignments\Project\DecodeGLow_CMS.v0\app\ImageResizer.php';
require '\xampp\htdocs\wd2\assignments\Project\DecodeGLow_CMS.v0\app\ImageResizeException.php';

// Safely build a path string using OS-specific directory separators.
function file_upload_path($original_filename, $upload_subfolder_name = 'product_uploads')
{
    // $current_folder = dirname(__FILE__);
    // $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

    // // Use OS-specific directory separator.
    // return join(DIRECTORY_SEPARATOR, $path_segments);

    // Get the current folder of the script (e.g., public/)
    $current_folder = __DIR__;

    // Move out of the public folder to the project root
    $project_root = dirname($current_folder);

    $parent_folder = 'storage';

    // Go into the storage/uploads directory
    $path_segments = [
        $project_root,        // Project root directory
        $parent_folder,       // New parent folder
        $upload_subfolder_name, // Target subfolder
        basename($original_filename) // The uploaded file name
    ];

    // Combine all segments into a full path
    return implode(DIRECTORY_SEPARATOR, $path_segments);

    // Now, $target_path points to the correct location for the file upload

}

// Check if the uploaded file is a valid image based on its mime-type and file extension.
function file_is_valid($temporary_path, $new_path)
{
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png', 'pdf'];

    $actual_file_extension = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type = mime_content_type($temporary_path);

    return in_array($actual_file_extension, $allowed_file_extensions) &&
        in_array($actual_mime_type, $allowed_mime_types);
}


// Upload photo to PHPMyAdmin
use \Gumlet\ImageResize;

if ($_POST && !empty($_POST['product_name']) && !empty($_POST['product_brand']) && !empty($_POST['product_category_id'])) {

    // Sanitize user input
    $product_name         = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $product_brand        = filter_input(INPUT_POST, 'product_brand', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $product_category_id  = filter_input(INPUT_POST, 'product_category_id', FILTER_VALIDATE_INT);

    // $_POST['product_description'] ? $product_descripion = filter_input(INPUT_POST, 'product_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "";
    // Removing input sanitization to make way for WYSIWYG
    $_POST['product_description'] ? $product_description = $_POST['product_description'] : "";

    // Set featured
    (isset($_POST['featureProduct']) && $_POST['featureProduct'] == 'featureProduct') ? $featured = 1 : 0;

    // Insert post details into the posts table
    $query = "INSERT INTO products (product_category_id, product_name, product_brand, product_description, featured) 
                VALUES (:product_category_id, :product_name, :product_brand, :product_description, :featured)";
    $statement = $db->prepare($query);

    $statement->bindValue(':product_category_id', $product_category_id);
    $statement->bindValue(':product_name', $product_name);
    $statement->bindValue(':product_brand', $product_brand);
    $statement->bindValue(':product_description', $product_description);
    $statement->bindValue(':featured', $featured);

    // If post insertion is successful:
    if ($statement->execute()) {
    } else {
        echo 'Error uploading post.';
        print_r($statement->errorInfo());
    }
}

// Detect if an image upload has been submitted and if there were any errors.
$file_upload_detected = isset($_FILES['file']) && $_FILES['file']['error'] === 0;

// Create the 'uploads' sub-folder
// file_exists('../storage/uploads') ?: mkdir('../storage/uploads');
$uploads_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'product_uploads';

if (!file_exists($uploads_dir)) {
    mkdir($uploads_dir);
}

// Check if there's a file upload:
if ($file_upload_detected) {

    $filename = $_FILES['file']['name'];
    $temporary_file_path = $_FILES['file']['tmp_name'];
    $new_file_path = file_upload_path($filename);


    if (file_is_valid($temporary_file_path, $new_file_path)) {
        move_uploaded_file($temporary_file_path, $new_file_path);

        $mime_type = mime_content_type($new_file_path);
        // Resize the image if it is not a PDF
        $image = new ImageResize($new_file_path);
        $image->resizeToWidth(400);
        $image->save(file_upload_path(basename($filename, '.' . pathinfo($filename, PATHINFO_EXTENSION)) . '_medium.' . pathinfo($filename, PATHINFO_EXTENSION)));

        $image->resizeToWidth(50);
        $image->save(file_upload_path(basename($filename, '.' . pathinfo($filename, PATHINFO_EXTENSION)) . '_thumbnail.' . pathinfo($filename, PATHINFO_EXTENSION)));

        $querySelect = "SELECT product_id, product_name, product_brand, product_description
        FROM products
        WHERE product_name = :product_name AND
              product_brand = :product_brand AND
              product_description = :product_description";

        // A PDO::Statement is prepared from the query.
        $statementRetrieve = $db->prepare($querySelect);
        $statementRetrieve->bindValue(':product_name', $product_name);
        $statementRetrieve->bindValue(':product_brand', $product_brand);
        $statementRetrieve->bindValue(':product_description', $product_description);

        // Execution on the DB server is delayed until we execute().
        $statementRetrieve->execute();

        if ($statementRetrieve->rowCount() > 0) {
            $products = $statementRetrieve->fetch();
        } else {
            header("Location: insert_poroducts.php");
            exit;
        }

        if (isset($filename)) {
            $queryAlbum = "INSERT INTO products_album (relative_path, alt_text, product_id) VALUES (:file_name, :alt_text, :product_id)";
            $statementAlbum = $db->prepare($queryAlbum);
            $statementAlbum->bindValue(':file_name', $filename);
            $statementAlbum->bindValue(':alt_text', $product_name);
            $statementAlbum->bindValue(':product_id', $products['product_id']);


            if ($statementAlbum->execute()) {
                unset($_SESSION['uploaded_file_path']);
                header("Location: products.php");
                exit;
            } else {
                echo 'Error uploading file details to album table.';
            }
        } else {
            header("Location: insert_products.php");
            exit;
        }
    } else {
        $_SESSION['upload_error'] = "Error uploading file.";

        // Redirect to the Insert page after upload.
        header("Location: insert_products.php");
        exit;
    }
} else {
    header("Location: products.php");
}
