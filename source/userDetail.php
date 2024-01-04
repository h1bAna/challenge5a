<?php
// list all users in database 
define( 'LMS_WEB_PAGE_TO_ROOT', '' );
require_once LMS_WEB_PAGE_TO_ROOT . 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();

$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'viewUser';
$page[ 'user_role' ] = lmsGetUserRole();

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])){
    $id = $_GET["id"];
    $query  = "SELECT * FROM `users` WHERE id='$id';";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
    if (!$result) {
        die('<pre>' . mysqli_error($GLOBALS["___mysqli_ston"]) . '.<br /> Something wrong with database.</pre>');
    }
    $row = mysqli_fetch_assoc($result);
    // detail user
    $page[ 'body' ] .= "
    <div class=\"body_padded\">
        <h1>Thông tin người dùng</h1>
        <hr />
        <br />
        <div class=\"row\">
            <div class=\"col-md-3\">
                <img src=\"" . LMS_WEB_PAGE_TO_ROOT . "{$row["avatar"]}\" class=\"img-thumbnail\" alt=\"Avatar\">
            </div>
            <div class=\"col-md-9\">
                <table class=\"table\">
                    <tbody>
                        <tr>
                            <td>Họ và tên</td>
                            <td>" . $row["full_name"] . "</td>
                        </tr>
                        <tr>
                            <td>Chức vụ</td>
                            <td>" . $row["role"] . "</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>" . $row["email"] . "</td>
                        </tr>
                        <tr>
                            <td>Số điện thoại</td>
                            <td>" . $row["phone_number"] . "</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class=\"container mt-5\">
    <h2>Nhập tin nhắn</h2>
    <form>
        <div class=\"form-group\">
            <label for=\"message\">Tin nhắn:</label>
            <textarea class=\"form-control\" id=\"message\" name=\"message\" rows=\"5\" placeholder=\"Nhập tin nhắn của bạn\"></textarea>
        </div>
        <button type=\"submit\" class=\"btn btn-primary\">Gửi</button>
    </form>
    </div>";
}

lmsHtmlEcho( $page)
?>