<?php
require_once 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsIsAdmin();
lmsDatabaseConnect();

$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'delStudent';
$page[ 'user_role' ] = lmsGetUserRole();

if($_SERVER["REQUEST_METHOD"] == "GET"  && isset($_GET["username"])){
    $username = $_GET["username"];
    $username = stripslashes( $username );
    $username = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $username ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    $sql = "DELETE FROM users WHERE username = '$username'";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    if($result){
        lmsMessagePush("Xoá sinh viên thành công");
    } else {
        lmsMessagePush("Xoá sinh viên thất bại");
    }
    lmsRedirect("delStudent.php");
}


$page[ 'body' ] .= "
    
            <form>
                <div class=\"form-group\">
                    <label for=\"username\">Tên đăng nhập:</label>
                    <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" required>
                </div>
                <div class=\"form-group\">
                    <button type=\"submit\" class=\"btn btn-primary\">Xoá sinh viên</button>
                </div>
            </form>
";


lmsHtmlEcho( $page );
?>