<?php
require_once 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsIsAdmin();
lmsDatabaseConnect();

$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'modifyStudent';
$page[ 'user_role' ] = lmsGetUserRole();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
    $id = $_POST["id"];
    $username = $_POST["username"];
    $username = stripslashes( $username );
    $username = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $username ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    $password = $_POST["password"];
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
        // remove old avatar
        $query  = "SELECT * FROM `users` WHERE id='$id';";
        $result = @mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '.<br /> Something wrong with database.</pre>' );
        if( $result && mysqli_num_rows( $result ) == 1 ) {    // Login Successful...
            // get user role from result
            $row = mysqli_fetch_assoc( $result );
            $oldAvatar = $row[ 'avatar' ];
            // check if file exists
            if (file_exists($oldAvatar)) {
                unlink($oldAvatar);
            }
        }
        $targetDir = "resources/upload/avatar/";
        $targetFilePath = $targetDir . $username . "." . $imageFileType;
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFilePath)) {
            lmsMessagePush("The file ". htmlspecialchars( basename( $_FILES["avatar"]["name"])). " has been uploaded.");
        } else {
            lmsMessagePush("Sorry, there was an error uploading your file.");
        }
        $hashedPassword = md5($password);
        // update user with new info based on id
        $query = "UPDATE `users` SET `username`='$username',`password`='$hashedPassword',`full_name`='$fullname',`email`='$email',`phone_number`='$phone',`avatar`='$targetFilePath' WHERE id='$id';";
        $result = @mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '.<br /> Something wrong with database.</pre>' );
        if($result){
            // reload session info
            lmsLogin( $username, lmsGetUserRole(), $targetFilePath, $id );
            lmsMessagePush("Chỉnh sửa thông tin sinh viên thành công");
            lmsRedirect('modifyStudent.php');
        }
        else{
            lmsMessagePush("Chỉnh sửa thông tin sinh viên thất bại");
            lmsRedirect('modifyStudent.php');
        }
    }
}


if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["username"])){
    $username = $_GET["username"];
    $username = stripslashes( $username );
    $username = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $username ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

    $query  = "SELECT * FROM `users` WHERE username='$username';";
    $result = @mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '.<br /> Something wrong with database.</pre>' );
    if( $result && mysqli_num_rows( $result ) == 1 ) {    // Login Successful...
        // get user role from result
        $row = mysqli_fetch_assoc( $result );
        $id = $row[ 'id' ];
        $fullname = $row[ 'full_name' ];
        $email = $row[ 'email' ];
        $phone = $row[ 'phone_number' ];
        $avatar = $row[ 'avatar' ];
        $page[ 'body' ] .= "
        <div class=\"container mt-5\">
            <form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">
                <div class=\"form-group\" style=\"display: none\">
                    <label for=\"id\">Mã số sinh viên:</label>
                    <input type=\"text\" class=\"form-control\" id=\"id\" name=\"id\" value=\"$id\" readonly>
                </div>
                <div class=\"form-group\">
                    <label for=\"username\">Tên đăng nhập:</label>
                    <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" value=\"$username\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"fullname\">Họ và tên:</label>
                    <input type=\"text\" class=\"form-control\" id=\"fullname\" name=\"fullname\" value=\"$fullname\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"password\">Mật khẩu:</label>
                    <input type=\"password\" class=\"form-control\" id=\"password\" name=\"password\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"email\">Email:</label>
                    <input type=\"email\" class=\"form-control\" id=\"email\" name=\"email\" value=\"$email\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"phone\">Số điện thoại:</label>
                    <input type=\"text\" class=\"form-control\" id=\"phone\" name=\"phone\" value=\"$phone\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"avatar\">Ảnh đại diện:</label>
                    <input type=\"file\" class=\"form-control-file\" id=\"avatar\" name=\"avatar\" accept=\"image/*\" required>
                </div>
                <div class=\"form-group\">
                <button type=\"submit\" class=\"btn btn-primary\" name=\"submit\">Xác nhận chỉnh sửa thông tin</button>
            </div>
            </form>
        </div>
        ";
    }else{
        $page[ 'body' ] .= "
                <form>
                    <div class=\"form-group\">
                        <label for=\"username\">Tên đăng nhập:</label>
                        <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" required>
                    </div>
                    <div class=\"form-group\">
                        <button type=\"submit\" class=\"btn btn-primary\">Lấy thông tin sinh viên</button>
                    </div>
                </form>
        ";
        lmsMessagePush("Không tìm thấy sinh viên");
    }
}else{
    $page[ 'body' ] .= "
    
            <form>
                <div class=\"form-group\">
                    <label for=\"username\">Tên đăng nhập:</label>
                    <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" required>
                </div>
                <div class=\"form-group\">
                    <button type=\"submit\" class=\"btn btn-primary\">Lấy thông tin sinh viên</button>
                </div>
            </form>
    ";
}






lmsHtmlEcho( $page );
?>