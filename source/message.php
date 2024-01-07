<?php
// list all users in database 
require_once 'resources/includes/lms.inc.php';

lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();

$page = lmsPageNewGrab();
$page[ 'title' ]   = 'Welcome' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'message';
$page[ 'user_role' ] = lmsGetUserRole();

$page[ 'body' ] .= "
<div class=\"container mt-4\">
    <div class=\"row\">
      <!-- Danh sách cuộc hội thoại bên trái -->
      <div class=\"col-md-4\">
        <h2>Danh sách cuộc hội thoại</h2>
        <div class=\"conversation-list\">
          <ul class=\"list-group\" id=\"conversationList\">
            <!-- Các mục cuộc hội thoại sẽ được thêm vào đây -->
";
// get all conversations of current user

$query  = "SELECT DISTINCT u.full_name
FROM (
    SELECT sender_id AS user_id FROM messages WHERE receiver_id = " . lmsGetCurrentUserId() . "
    UNION
    SELECT receiver_id AS user_id FROM messages WHERE sender_id = " . lmsGetCurrentUserId() . "
) AS user_conversations
INNER JOIN users u ON user_conversations.user_id = u.id
WHERE u.id != " . lmsGetCurrentUserId() . ";";

$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query);
if (!$result) {
    die('<pre>' . mysqli_error($GLOBALS["___mysqli_ston"]) . '.<br /> Something wrong with database.</pre>');
}else{
    $conversations = array();
    foreach ($result as $row) {
        $conversations[] = $row["id"];
        $page[ 'body' ] .= "
        <li class=\"list-group-item\" onclick=\"loadConversation(" . $row["id"] . ")\">" . $row["full_name"] . "</li>
        ";
    }
    
}
$page[ 'body' ] .=



          "</ul>
        </div>
      </div>
      <!-- Chi tiết cuộc hội thoại bên phải -->
      <div class=\"col-md-8\">
        <h2>Chi tiết cuộc hội thoại</h2>
        <div class=\"card\" id=\"conversationDetail\">
          <div class=\"card-body\">
            <!-- Nội dung chi tiết cuộc hội thoại sẽ được thêm vào đây -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Link tới Bootstrap JS và thư viện jQuery -->
  <script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\"></script>
  <script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js\"></script>
  <script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js\"></script>
  <!-- Script JavaScript của bạn -->
  <script src=\"resources/js/message.js\"></script>

";

lmsHtmlEcho( $page);
?>