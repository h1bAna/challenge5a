<?php

define( 'LMS_WEB_PAGE_TO_ROOT', '' );
require_once LMS_WEB_PAGE_TO_ROOT . 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();



$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'addStudent';
$page[ 'user_role' ] = lmsGetUserRole();
$page[ 'body' ] .= "
<form>
            <div class=\"form-group\">
                <label for=\"username\">Tên đăng nhập:</label>
                <input type=\"text\" id=\"username\" name=\"username\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"password\">Mật khẩu:</label>
                <input type=\"password\" id=\"password\" name=\"password\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"fullname\">Họ tên:</label>
                <input type=\"text\" id=\"fullname\" name=\"fullname\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"email\">Email:</label>
                <input type=\"email\" id=\"email\" name=\"email\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"phone\">Số điện thoại:</label>
                <input type=\"text\" id=\"phone\" name=\"phone\" required>
            </div>
            <div class=\"form-group\">
                <label for=\"avatar\">Avatar:</label>
                <input type=\"file\" id=\"avatar\" name=\"avatar\" accept=\"image/*\" required>
            </div>
            <div class=\"form-group\">
                <input type=\"submit\" value=\"Đăng ký\">
            </div>
        </form>";

lmsHtmlEcho( $page );

?>