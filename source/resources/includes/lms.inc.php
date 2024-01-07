<?php

session_start(); // Creates a 'Full Path Disclosure' vuln.

if (!file_exists('config/config.inc.php')) {
	die ("System error - config file not found. Copy config/config.inc.php.dist to config/config.inc.php and configure to your environment.");
}

// Include configs
require_once 'config/config.inc.php';

// Declare the $html variable
if( !isset( $html ) ) {
	$html = "";
}

// Start session functions --

function &lmsSessionGrab() {
	if( !isset( $_SESSION[ 'lms' ] ) ) {
		$_SESSION[ 'lms' ] = array();
	}
	return $_SESSION[ 'lms' ];
}


function lmsPageStartup( $pActions ) {
	if( in_array( 'authenticated', $pActions ) ) {
		if( !lmsIsLoggedIn()) {
			lmsRedirect('login.php' );
		}
	}
}

function lmsLogin( $pUsername, $pRole, $pAvatar, $pID ) {
	$lmsSession =& lmsSessionGrab();
	$lmsSession[ 'username' ] = $pUsername;
	$lmsSession[ 'role' ] = $pRole;
	$lmsSession[ 'avatar'] = $pAvatar;
	$lmsSession[ 'id' ] = $pID;
}


function lmsIsLoggedIn() {
	$lmsSession =& lmsSessionGrab();
	return isset( $lmsSession[ 'username' ] );
}

function lmsGetUserRole() {
	$lmsSession =& lmsSessionGrab();
	return ( isset( $lmsSession[ 'role' ] ) ? $lmsSession[ 'role' ] : '') ;
}

function lmsGetCurrentUserId() {
	$lmsSession =& lmsSessionGrab();
	return ( isset( $lmsSession[ 'id' ] ) ? $lmsSession[ 'id' ] : '') ;
}

function lmsIsAdmin() {
	if(lmsCurrentUser() != "admin"){
		// forbidden
		$page = lmsPageNewGrab();
		$page[ 'title' ]   = 'forbidden';
		$page[ 'page_id' ] = 'forbidden';
		$page[ 'user_role' ] = lmsGetUserRole();
		$page[ 'body' ] .= "
		<div class=\"container mt-5\">
			<h2>403 Forbidden</h2>
			<p>Bạn không có quyền truy cập vào trang này</p>
		</div>
		";
		lmsHtmlEcho( $page );
		exit();
	}
}

function lmsLogout() {
	$lmsSession =& lmsSessionGrab();
	unset( $lmsSession[ 'username' ] );
}


function lmsPageReload() {
	lmsRedirect( $_SERVER[ 'PHP_SELF' ] );
}

function lmsCurrentUser() {
	$lmsSession =& lmsSessionGrab();
	return ( isset( $lmsSession[ 'username' ]) ? $lmsSession[ 'username' ] : '') ;
}

// -- END (Session functions)

function &lmsPageNewGrab() {
	$returnArray = array(
		'title'           => 'LMS',
		'title_separator' => ' :: ',
		'user_role'       => '',
		'body'            => '',
		'page_id'         => '',
	);
	return $returnArray;
}


// Start message functions --

function lmsMessagePush( $pMessage ) {
	$lmsSession =& lmsSessionGrab();
	if( !isset( $lmsSession[ 'messages' ] ) ) {
		$lmsSession[ 'messages' ] = array();
	}
	$lmsSession[ 'messages' ][] = $pMessage;
}


function lmsMessagePop() {
	$lmsSession =& lmsSessionGrab();
	if( !isset( $lmsSession[ 'messages' ] ) || count( $lmsSession[ 'messages' ] ) == 0 ) {
		return false;
	}
	return array_shift( $lmsSession[ 'messages' ] );
}


function messagesPopAllToHtml() {
	$messagesHtml = '';
	while( $message = lmsMessagePop() ) {   // TODO- sharpen!
		$messagesHtml .= "<div class=\"message\">{$message}</div>";
	}

	return $messagesHtml;
}

// --END (message functions)

// To be used on all external links --
function dvwaExternalLinkUrlGet( $pLink,$text=null ) {
	if(is_null( $text )) {
		return '<a href="' . $pLink . '" target="_blank">' . $pLink . '</a>';
	}
	else {
		return '<a href="' . $pLink . '" target="_blank">' . $text . '</a>';
	}
}
// -- END ( external links)
// Database Management --

if( $DBMS == 'MySQL' ) {
	$DBMS = htmlspecialchars(strip_tags( $DBMS ));
	$DBMS_errorFunc = 'mysqli_error()';
}
elseif( $DBMS == 'PGSQL' ) {
	$DBMS = htmlspecialchars(strip_tags( $DBMS ));
	$DBMS_errorFunc = 'pg_last_error()';
}
else {
	$DBMS = "No DBMS selected.";
	$DBMS_errorFunc = '';
}

//$DBMS_connError = '
//	<div align="center">
//		<img src="' . DVWA_WEB_PAGE_TO_ROOT . 'dvwa/images/logo.png" />
//		<pre>Unable to connect to the database.<br />' . $DBMS_errorFunc . '<br /><br /></pre>
//		Click <a href="' . DVWA_WEB_PAGE_TO_ROOT . 'setup.php">here</a> to setup the database.
//	</div>';

function lmsDatabaseConnect() {
	global $_LMS;
	global $DBMS;
	//global $DBMS_connError;
	global $db;

	if( $DBMS == 'MySQL' ) {
		if( !@($GLOBALS["___mysqli_ston"] = mysqli_connect( $_LMS[ 'db_server' ],  $_LMS[ 'db_user' ],  $_LMS[ 'db_password' ], "", $_LMS[ 'db_port' ] ))
		|| !@((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $_LMS[ 'db_database' ])) ) {
			//die( $DBMS_connError );
			lmsLogout();
        	$DBMS_errorFunc = 'mysqli_error()';

			lmsMessagePush( 'Unable to connect to the database.<br />' . $DBMS_errorFunc );
		}
		// MySQL PDO Prepared Statements (for impossible levels)
		$db = new PDO('mysql:host=' . $_LMS[ 'db_server' ].';dbname=' . $_LMS[ 'db_database' ].';port=' . $_LMS['db_port'] . ';charset=utf8', $_LMS[ 'db_user' ], $_LMS[ 'db_password' ]);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}
	else {
		die ( "Unknown {$DBMS} selected." );
	}
}

// -- END (Database Management)


function lmsRedirect( $pLocation ) {
	session_commit();
	header( "Location: {$pLocation}" );
	exit;
}

// XSS Stored guestbook function --
// -- END (XSS Stored guestbook)


// Token functions --
function checkToken( $user_token, $session_token, $returnURL ) {  # Validate the given (CSRF) token
	if( $user_token !== $session_token || !isset( $session_token ) ) {
		lmsMessagePush( 'CSRF token is incorrect' );
		lmsRedirect( $returnURL );
	}
}

function generateSessionToken() {  # Generate a brand new (CSRF) token
	if( isset( $_SESSION[ 'session_token' ] ) ) {
		destroySessionToken();
	}
	$_SESSION[ 'session_token' ] = md5( uniqid() );
}

function destroySessionToken() {  # Destroy any session with the name 'session_token'
	unset( $_SESSION[ 'session_token' ] );
}

function tokenField() {  # Return a field for the (CSRF) token
	return "<input type='hidden' name='user_token' value='{$_SESSION[ 'session_token' ]}' />";
}
// -- END (Token functions)

function lmsHtmlEcho( $pPage ) {
	$menuBlocks = array();

	$menuBlocks[ 'home' ] = array();
	
	if($pPage[ 'user_role'] == "admin"){
		$menuBlocks[] = array( 'id' => 'home', 'name' => 'Trang chủ', 'url' => '.' );
		$menuBlocks[] = array( 'id' => 'addStudent', 'name' => 'Thêm sinh viên', 'url' => 'addStudent.php' );
		$menuBlocks[] = array( 'id' => 'modifyStudent', 'name' => 'Sửa thông tin sinh viên', 'url' => 'modifyStudent.php' );
		$menuBlocks[] = array( 'id' => 'delStudent', 'name' => 'Xoá sinh viên', 'url' => 'delStudent.php' );
		$menuBlocks[] = array( 'id' => 'createAssignment', 'name' => 'Thêm bài tập', 'url' => 'addAssignment.php' );
	}elseif($pPage[ 'user_role'] == "student"){
		$menuBlocks[] = array( 'id' => 'home', 'name' => 'Trang chủ', 'url' => '.' );
		$menuBlocks[] = array( 'id' => 'viewAssignment', 'name' => 'Xem bài tập', 'url' => 'viewAssignment.php' );
	}

	$menuBlocks[] = array( 'id' => 'message', 'name' => 'Tin nhắn', 'url' => 'message.php' );
	$menuBlocks[] = array( 'id' => 'selfModify', 'name' => 'Thay đổi thông tin bản thân', 'url' => 'selfModify.php' );
	$menuBlocks[] = array( 'id' => 'viewUser', 'name' => 'Xem người dùng', 'url' => 'userView.php' );

	



	$menuHtml = '';
	$user = lmsCurrentUser();
	$avatar = lmsSessionGrab()['avatar'];
	foreach( $menuBlocks as $menuItem ) {
		if( $menuItem[ 'id' ] == '' ) {
			continue;
		}
		$selectedClass = ( $menuItem[ 'id' ] == $pPage[ 'page_id' ] ) ? 'selected' : '';
		if($menuItem[ 'id' ] == $pPage[ 'page_id' ]){
			$pageName = $menuItem[ 'name' ];
		}
		$fixedUrl = $menuItem[ 'url' ];
		$menuHtml .= "<li class=\"{$selectedClass}\" ><a class=\"nav-item\" href=\"{$fixedUrl}\">{$menuItem[ 'name' ]}</a></li>";
	}



	$messagesHtml = messagesPopAllToHtml();
	if( $messagesHtml ) {
		$messagesHtml = "<div class=\"body_padded\">{$messagesHtml}</div>";
	}


	// Send Headers + main HTML code
	Header( 'Cache-Control: no-cache, must-revalidate');   // HTTP/1.1
	Header( 'Content-Type: text/html;charset=utf-8' );     // TODO- proper XHTML headers...
	Header( 'Expires: Tue, 23 Jun 2009 12:00:00 GMT' );    // Date in the past

	echo "<!DOCTYPE html>

<html lang=\"en-GB\">

	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />

		<title>{$pPage[ 'title' ]}</title>


		<link rel=\"icon\" type=\"\image/ico\" href=\"favicon.ico\" />

		<link rel=\"stylesheet\" type=\"text/css\" href=\"resources/css/styleHeader.css\">
		<link href=\"https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap\" rel=\"stylesheet\">
		<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css\">
		<link rel=\"stylesheet\" type=\"text/css\" href=\"resources/css/styleMain.css\">
		<link href=\"https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&display=swap\" rel=\"stylesheet\">
		<link href=\"https://fonts.googleapis.com/css2?family=Amatic+SC:wght@700&display=swap\" rel=\"stylesheet\">	
		<link href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css\" rel=\"stylesheet\">

	</head>

	<body>
		<div class =\"web-container\">
			<div class=\"head\">
				<ul class=\"head-nav\">
					{$menuHtml}
					<li><img src=\"{$avatar}\" content-type=  alt=\"avatar\" class=\"ava-img\" ></li>
					<li><a href=\"\" class=\"nav-item\">{$user}</a></li>
					<li><a href=\"logout.php\" class=\"nav-item\">Đăng xuất</a></li>
				</ul>
			</div>


			<div class=\"main\">
				<div class=\"main-title\">
					<span class=\"main-title-big\">{$pageName}</span>
				</div>
				<div class=\"main-content\">
					<div class=\"conten\">
						<div class=\"container mt-5\">
							{$pPage[ 'body' ]}
							<br /><br />
							{$messagesHtml}
						</div>
					</div>
				</div>
				
			</div>
		</div><div>
		</div>
		<script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js\"></script>
	</body>

</html>";
}

// -- END (Setup Functions)

?>
