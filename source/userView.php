<?php
// list all users in database 
require_once 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();

$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'viewUser';
$page[ 'user_role' ] = lmsGetUserRole();

// get all users
$query  = "SELECT * FROM `users`;";
$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
if (!$result) {
    die('<pre>' . mysqli_error($GLOBALS["___mysqli_ston"]) . '.<br /> Something wrong with database.</pre>');
}

$page[ 'body' ] .= "
<table class=\"table\">
<thead>
  <tr>
    <th>Chức vụ</th>
    <th>Họ và tên</th>
    <th>Chi tiết</th>
  </tr>
</thead>
<tbody>
";

foreach ($result as $row) {
    $page[ 'body' ] .= "
    <tr>
        <td>" . $row["role"] . "</td>
        <td>" . $row["full_name"] . "</td>
        <td><a href=\"userDetail.php?id=" . $row["id"] . "\">Chi tiết</a></td>
    </tr>";
}

$page[ 'body' ] .= "
</tbody>
</table>";

lmsHtmlEcho( $page)
?>