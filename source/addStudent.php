<?php

define( 'LMS_WEB_PAGE_TO_ROOT', '' );
require_once LMS_WEB_PAGE_TO_ROOT . 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();



$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'addStudent';
$page[ 'user_role' ] = lmsGetUserRole();


lmsHtmlEcho( $page );

?>