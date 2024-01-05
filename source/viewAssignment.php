<?php
require_once 'resources/includes/lms.inc.php';
lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();

$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'viewAssignment';
$page[ 'user_role' ] = lmsGetUserRole();
$page[ 'body' ] .= "";

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"]) ){
    $_GET["id"] = stripslashes( $_GET["id"] );
    $_GET["id"] = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $_GET["id"] ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    $query  = "SELECT * FROM `assignments` WHERE id = " . $_GET["id"] . ";";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
    if (!$result) {
        lmsMessagePush("Lỗi truy vấn CSDL");
        lmsRedirect("addAssignment.php");
    }else{
        $row = mysqli_fetch_assoc($result);
        $page[ 'body' ] .= "
        <div class=\"container mt-5\">
            <div class=\"card\">
                <div class=\"card-header\">
                    <h5 class=\"card-title\">Assignment Details</h5>
                </div>
                <div class=\"card-body\">
                    <h4 class=\"card-subtitle mb-2 text-muted\">Title: " . $row["title"] . "</h4>
                    <p class=\"card-text\">Description: " . $row["description"] . "</p>
                    <p class=\"card-text\">Due Date: " . $row["due_date"] . "</p>
                    <a href=\"resources/upload/assignment/" . $row["file"] . "\" class=\"btn btn-primary\">Download</a>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS and dependencies (jQuery, Popper.js) -->
        <script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\"></script>
        <script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js\"></script>
        <script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js\"></script>"; 
    }
    // check if user already submitted assignment
    $query  = "SELECT * FROM `returnAssignment` WHERE assignment_id = " . $_GET["id"] . " AND student_id = " . lmsGetCurrentUserId() . ";";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
    if (!$result) {
        lmsMessagePush("Lỗi truy vấn CSDL");
        lmsRedirect("viewAssignment.php");
    }else{
        // if user have not already submitted assignment
        if (mysqli_num_rows($result) == 0) {
            $page[ 'body' ] .= "
            <div class=\"container mt-5\">
                <div class=\"card\">
                    <div class=\"card-header\">
                        <h5 class=\"card-title\">Upload Return</h5>
                    </div>
                    <div class=\"card-body\">
                        <form method=\"post\" enctype=\"multipart/form-data\">
                            <div class=\"form-group\">
                                <label for=\"description\">Description:</label>
                                <textarea class=\"form-control\" id=\"description\" name=\"description\" rows=\"3\" placeholder=\"Enter description\"></textarea>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"fileUpload\">Upload Assignment:</label>
                                <input type=\"file\" class=\"form-control-file\" id=\"fileUpload\" name=\"fileUpload\">
                            </div>
                            <button type=\"submit\" id=\"submit\" name=\"submit\" class=\"btn btn-primary\">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bootstrap JS and dependencies (jQuery, Popper.js) -->
            <script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\"></script>
            <script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js\"></script>
            <script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js\"></script>
            ";
        }else{
            lmsMessagePush("Bạn đã nộp bài tập này rồi");
        }

    }


    lmsHtmlEcho( $page);
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"]) ){
    $_POST["description"] = stripslashes( $_POST["description"] );
    $_POST["description"] = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $_POST["description"] ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $query = "INSERT INTO `returnAssignment` (`assignment_id`, `student_id`, `description`, `file`, `submitted_at`) VALUES (" . $_GET["id"] . ", " . lmsGetCurrentUserId() . ", '" . $_POST["description"] . "', '" . $_FILES["fileUpload"]["name"] . "', NOW());";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
    if (!$result) {
        lmsMessagePush("Lỗi truy vấn CSDL");
        lmsRedirect("viewAssignment.php");
    }else{
        $targetDir = "resources/upload/return-Assignment/";
        $targetFile = $targetDir . basename($_FILES["fileUpload"]["name"]);

        move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $targetFile);
        lmsRedirect("viewAssignment.php?id=" . $_GET["id"]);
    }
}

$query  = "SELECT * FROM `assignments`;";
$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
if (!$result) {
    lmsMessagePush("Lỗi truy vấn CSDL");
    lmsRedirect("addAssignment.php");
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
                <a href=\"viewAssignment.php?id=" . $row["id"] . "\" class=\"btn btn-primary\">Xem chi tiết</a>
            </div>
        </div>
    </div>";
}

lmsHtmlEcho( $page);

?>
