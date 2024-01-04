<?php

define( 'LMS_WEB_PAGE_TO_ROOT', '' );
require_once LMS_WEB_PAGE_TO_ROOT . 'resources/includes/lms.inc.php';
lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();
// handle POST request to add new assignment
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
    $title = $_POST["title"];
    $title = stripslashes( $title );
    $title = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $title ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $description = $_POST["description"];
    $description = stripslashes( $description );
    $description = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $description ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $deadline = $_POST["deadline"];
    $deadline = stripslashes( $deadline );
    $deadline = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $deadline ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $pdfFile = $_FILES["pdfFile"]["name"]; // Tên file ảnh
    $pdfFile = stripslashes( $pdfFile );
    $pdfFile = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $pdfFile ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    // di chuyển file pdf vào thư mục
    
    $targetDir = LMS_WEB_PAGE_TO_ROOT. "resources/upload/avatar/";
    $targetFile = $targetDir . basename($_FILES["pdfFile"]["name"]);

    move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $targetFile);

    // insert into database
    $query = "INSERT INTO `assignments` (`title`, `description`, `due_date`, `file`, `created_at`) VALUES ('$title', '$description', '$deadline', '$pdfFile', NOW());";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
    if (!$result) {
        die('<pre>' . mysqli_error($GLOBALS["___mysqli_ston"]) . '.<br /> Something wrong with database.</pre>');
    }else{
        lmsMessagePush("Thêm assignment thành công");
    }


}



lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();
$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'createAssignment';
$page[ 'user_role' ] = lmsGetUserRole();
$page[ 'body' ] .= "
<div class=\"container mt-5\">
    <h2>Tạo Assignment</h2>
    <form method=\"post\" enctype=\"multipart/form-data\">
        <div class=\"form-group\">
            <label for=\"title\">Title:</label>
            <input type=\"text\" class=\"form-control\" id=\"title\" name=\"title\" placeholder=\"Enter title\">
        </div>
        <div class=\"form-group\">
            <label for=\"description\">Description:</label>
            <textarea class=\"form-control\" id=\"description\" name=\"description\" rows=\"3\" placeholder=\"Enter description\"></textarea>
        </div>
        <div class=\"form-group\">
            <label for=\"pdfFile\">Upload PDF File:</label>
            <input type=\"file\" class=\"form-control-file\" id=\"pdfFile\" name=\"pdfFile\" accept=\".pdf\">
        </div>
        <div class=\"form-group\">
            <label for=\"deadline\">Deadline:</label>
            <input type=\"date\" class=\"form-control\" id=\"deadline\" name=\"deadline\">
        </div>
        <button type=\"submit\" id=\"submit\" name=\"submit\" class=\"btn btn-primary\">Upload</button>
    </form>
</div>";

// list all assignments in database

$query  = "SELECT * FROM `assignments`;";
$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
if (!$result) {
    die('<pre>' . mysqli_error($GLOBALS["___mysqli_ston"]) . '.<br /> Something wrong with database.</pre>');
}

foreach($result as $row){
    $page[ 'body' ] .= "
    <div class=\"container mt-5\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3>" . $row["title"] . "</h3>
            </div>
            <div class=\"card-body\">
                <h5 class=\"card-title\">Deadline: " . $row["due_date"] . "</h5>
                <p class=\"card-text\">" . $row["description"] . "</p>
                <a href=\"assignmentDetail.php?id=" . $row["id"] . "\" class=\"btn btn-primary\">Xem chi tiết</a>
            </div>
        </div>
    </div>";
}

lmsHtmlEcho( $page);
?>
