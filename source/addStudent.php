<?php

define( 'LMS_WEB_PAGE_TO_ROOT', '' );
require_once LMS_WEB_PAGE_TO_ROOT . 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    // prevent SQL injection
    $username = stripslashes( $username );
    $username = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $username ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $password = $_POST["password"];
    $password = stripslashes( $password );
    $password = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $password ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    
    $fullname = $_POST["fullname"];
    $fullname = stripslashes( $fullname );
    $fullname = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $fullname ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $email = $_POST["email"];
    $email = stripslashes( $email );
    $email = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $email ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $phone = $_POST["phone"];
    $phone = stripslashes( $phone );
    $phone = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $phone ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $avatar = $_FILES["avatar"]["name"]; // Tên file ảnh
    $avatar = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $avatar ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    // Kiểm tra và di chuyển file ảnh vào thư mục
    // kiểm tra kiểu file
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($avatar,PATHINFO_EXTENSION));
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
        if($check !== false) {
            lmsMessagePush("File is an image - " . $check["mime"] . ".");
            $uploadOk = 1;
        } else {
            lmsMessagePush("File is not an image.");
            $uploadOk = 0;
        }
    }

    // Check file size
    if ($_FILES["avatar"]["size"] > 500000) {
        lmsMessagePush("Sorry, your file is too large.");
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        lmsMessagePush("Sorry, only JPG, JPEG, PNG files are allowed.");
        $uploadOk = 0;
    }
    
    if ($uploadOk == 0) {
        lmsMessagePush("Sorry, your file was not uploaded.");
      // if everything is ok, try to upload file
    } else {
        $targetDir = LMS_WEB_PAGE_TO_ROOT. "resources/upload/avatar/";
        $targetFilePath = $targetDir . $username . "." . $imageFileType;
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFilePath)) {
            lmsMessagePush("The file ". htmlspecialchars( basename( $_FILES["avatar"]["name"])). " has been uploaded.");
        } else {
            lmsMessagePush("Sorry, there was an error uploading your file.");
        }
        $hashedPassword = md5($password);

        $sql = "INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `phone_number`,`role`, `avatar`) VALUES ('$username', '$hashedPassword', '$fullname' , '$email', '$phone','student', '$targetFilePath')";
        $result = @mysqli_query($GLOBALS["___mysqli_ston"],  $sql ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '.<br /> Something wrong with database.</pre>' );
        if($result){

            lmsMessagePush("Thêm sinh viên thành công");
        }
        else{
            lmsMessagePush("Thêm sinh viên thất bại");
        
        }
    }
}


$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'addStudent';
$page[ 'user_role' ] = lmsGetUserRole();
$page[ 'body' ] .= "
<form action=\"\" method=\"post\" enctype=\"multipart/form-data\">
            <div class=\"form-group\">
                <label for=\"username\">Tên đăng nhập:</label>
                <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"password\">Mật khẩu:</label>
                <input type=\"password\" class=\"form-control\" id=\"password\" name=\"password\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"fullname\">Họ tên:</label>
                <input type=\"text\" class=\"form-control\" id=\"fullname\" name=\"fullname\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"email\">Email:</label>
                <input type=\"email\" class=\"form-control\" id=\"email\" name=\"email\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"phone\">Số điện thoại:</label>
                <input type=\"text\" class=\"form-control\" id=\"phone\" name=\"phone\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"avatar\">Avatar:</label>
                <input type=\"file\" class=\"form-control-file\" id=\"avatar\" name=\"avatar\" accept=\"image/*\" required>
            </div>
            <div class=\"form-group\">
                <button type=\"submit\" class=\"btn btn-primary\" name=\"submit\">Đăng ký</button>
            </div>
        </form>";

lmsHtmlEcho( $page );



?>