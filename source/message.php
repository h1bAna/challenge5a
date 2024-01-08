<?php
// list all users in database 
require_once 'resources/includes/lms.inc.php';
lmsPageStartup( array( 'authenticated') );
lmsDatabaseConnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Xác thực token ở đây, ví dụ: kiểm tra xem token có tồn tại không
  $headers = apache_request_headers();
  $receivedToken = $headers['Authorization'] ?? null;
  $validToken = $_SESSION['session_token'] ?? null;

  if ($receivedToken != $validToken) {
      http_response_code(401); // Trả về mã lỗi 401 nếu token không hợp lệ
      exit();
  }

  // Lấy dữ liệu gửi từ yêu cầu POST
  $postData = json_decode(file_get_contents('php://input'), true);

  // Kiểm tra và xử lý dữ liệu từ yêu cầu POST
  if (isset($postData['userId1'], $postData['userId2'], $postData['time'])) {
      // Xử lý yêu cầu và trả về dữ liệu (ở đây là một mảng giả định)
      $userId1 = $postData['userId1'];
      $userId2 = $postData['userId2'];
      $time = $postData['time'];

      // prevent SQL injection
      $userId1 = stripslashes( $userId1 );
      $userId1 = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $userId1 ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
      $userId2 = stripslashes( $userId2 );
      $userId2 = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $userId2 ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
      $time = stripslashes( $time );
      $time = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $time ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

      $query = 
      // Ví dụ: Trả về dữ liệu như là một mảng JSON
      $messages = [
          ['id' => 1, 'sender' => $userId1, 'receiver' => $userId2, 'content' => 'Hello', 'sent_time' => $time],
          ['id' => 2, 'sender' => $userId2, 'receiver' => $userId1, 'content' => 'Hi', 'sent_time' => $time]
      ];

      header('Content-Type: application/json');
      echo json_encode($messages);
      exit();
  } else {
      http_response_code(400); // Trả về mã lỗi 400 nếu thiếu thông tin
      echo json_encode(['error' => 'Missing data']);
      exit();
  }
} else {
  http_response_code(405); // Trả về mã lỗi 405 nếu phương thức không hợp lệ
  echo json_encode(['error' => 'Method Not Allowed']);
  exit();
}

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
  " . tokenField() . "
  <!-- Link tới Bootstrap JS và thư viện jQuery -->
  <script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\"></script>
  <script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js\"></script>
  <script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js\"></script>
  <!-- Script JavaScript của bạn -->
  <script src=\"resources/js/message.js\"></script>
  
";

// set environment variables

lmsHtmlEcho( $page);
?>