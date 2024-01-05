<?php


require_once 'resources/includes/lms.inc.php';
lmsPageStartup( array( 'authenticated') );
lmsIsAdmin();
lmsDatabaseConnect();
$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'createAssignment';
$page[ 'user_role' ] = lmsGetUserRole();
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
    // list all students have submitted assignment
    $page[ 'body' ] .= "
    <div class=\"container mt-5\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h5 class=\"card-title\">List of Submitted Assignments</h5>
            </div>
            <div class=\"card-body\">
                <table class=\"table\">
                <thead>
                    <tr>
                        <th scope=\"col\">Student Name</th>
                        
                        <th scope=\"col\">Download Assignment</th>
                        <th scope=\"col\">Time submit</th>
                    </tr>
                </thead>
                <tbody>
                ";

    $query  = "SELECT * FROM `returnAssignment` WHERE assignment_id = " . $_GET["id"] . ";";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
    if (!$result) {
        lmsMessagePush("Lỗi truy vấn CSDL");
        lmsRedirect("viewAssignment.php");
    }else{
        if (mysqli_num_rows($result) > 0) {
            // Có sinh viên đã nộp bài tập
            foreach($result as $row){
                $student_id = $row["student_id"];
                $query  = "SELECT * FROM `users` WHERE id = " . $student_id . ";";
                $result2 = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
                if (!$result2) {
                    lmsMessagePush("Lỗi truy vấn CSDL");
                    lmsRedirect("viewAssignment.php");
                }else{
                    $row2 = mysqli_fetch_assoc($result2);
                    $page[ 'body' ] .= "
                    <tr>
                            <td>" . $row2["full_name"] . "</td>
                            <td>
                                <a href=\"resources/upload/return-Assignment/" . $row["file"] . "\" class=\"btn btn-primary\">Download</a>
                            </td>
                            <td>" . $row["submitted_at"] ."</td>
                    </tr>
                    <tr>
                        <td colspan=\"3\">Description: " . $row["description"] . "</td>
                    </tr>
                    
                    ";
                }
            }
        }else{
            // Không có sinh viên nào nộp bài tập
            $page[ 'body' ] .= "
            <tr>
                <td colspan=\"3\">Không có sinh viên nào nộp bài tập</td>
            </tr>";
        }
    }


    lmsHtmlEcho( $page );
    exit();
}





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
    
    $targetDir = "resources/upload/assignment/";
    $targetFile = $targetDir . basename($_FILES["pdfFile"]["name"]);

    move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $targetFile);

    // insert into database
    $query = "INSERT INTO `assignments` (`title`, `description`, `due_date`, `file`, `created_at`) VALUES ('$title', '$description', '$deadline', '$pdfFile', NOW());";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
    if (!$result) {
        die('<pre>' . mysqli_error($GLOBALS["___mysqli_ston"]) . '.<br /> Something wrong with database.</pre>');
    }else{
        lmsMessagePush("Thêm assignment thành công");
        lmsRedirect("addAssignment.php");
    }


}


$page[ 'body' ] .= "
<div class=\"container mt-5\">
    <h2>Tạo Assignment</h2>
    <form method=\"post\" enctype=\"multipart/form-data\">
        <div class=\"form-group\">
            <label for=\"title\">Title:</label>
            <input type=\"text\" class=\"form-control\" id=\"title\" name=\"title\" placeholder=\"Enter title\" maxlength=\"100\">
        </div>
        <div class=\"form-group\">
            <label for=\"description\">Description:</label>
                <textarea class=\"form-control\" id=\"description\" name=\"description\" rows=\"3\" placeholder=\"Enter description\" maxlength=\"255\"></textarea>
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
                <a href=\"addAssignment.php?id=" . $row["id"] . "\" class=\"btn btn-primary\">Xem chi tiết</a>
            </div>
        </div>
    </div>";
}

lmsHtmlEcho( $page);
?>
