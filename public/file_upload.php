<?php
require('/config/config.php');
require('authenticate.php');

require '\xampp\htdocs\wd2\assignments\Project\DecodeGLow_CMS.v0\app\ImageResizer.php';
require '\xampp\htdocs\wd2\assignments\Project\DecodeGLow_CMS.v0\app\ImageResizeException.php';

// Safely build a path string using OS-specific directory separators.
function file_upload_path($original_filename, $upload_subfolder_name = 'uploads')
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
        $parent_folder,            // New parent folder
        $upload_subfolder_name,            // Target subfolder
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

use \Gumlet\ImageResize;

// If the button in the post is the submit-post button, Insert a new post.
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

    $statement->execute();
}

// Detect if an image upload has been submitted and if there were any errors.
$file_upload_detected = isset($_FILES['file']) && $_FILES['file']['error'] === 0;

// Create the 'uploads' sub-folder
// file_exists('../storage/uploads') ?: mkdir('../storage/uploads');
$uploads_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads';

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

        $querySelect = "SELECT post_id, post_title, post_date
        FROM posts
        WHERE post_title = :post_title";

        // A PDO::Statement is prepared from the query.
        $statementRetrieve = $db->prepare($querySelect);
        $statementRetrieve->bindValue(':post_title', $post_title);

        // Execution on the DB server is delayed until we execute().
        $statementRetrieve->execute();

        if ($statementRetrieve->rowCount() > 0) {
            $posts = $statementRetrieve->fetch();
        } else {
            header("Location: insert.php");
            exit;
        }

        if (isset($filename)) {
            $queryAlbum = "INSERT INTO album (relative_path, alt_text, post_id) VALUES (:file_name, :alt_text, :post_id)";
            $statementAlbum = $db->prepare($queryAlbum);
            $statementAlbum->bindValue(':file_name', $filename);
            $statementAlbum->bindValue(':alt_text', $post_title);
            $statementAlbum->bindValue(':post_id', $posts['post_id']);

            if ($statementAlbum->execute()) {
                unset($_SESSION['uploaded_file_path']);
                header("Location: index.php");
                exit;
            } else {
                echo 'Error uploading file details to album table.';
            }
        } else {
            header("Location: insert.php");
            exit;
        }
    } else {
        $_SESSION['upload_error'] = "Error uploading file.";

        // Redirect to the Insert page after upload.
        header("Location: insert.php");
        exit;
    }
} else {
    header("Location: index.php");
}
