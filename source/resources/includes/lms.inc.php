<?php

if( !defined( 'LMS_WEB_PAGE_TO_ROOT' ) ) {
	die( 'LMS System error- WEB_PAGE_TO_ROOT undefined' );
	exit;
}

session_start(); // Creates a 'Full Path Disclosure' vuln.

if (!file_exists(LMS_WEB_PAGE_TO_ROOT . 'config/config.inc.php')) {
	die ("DVWA System error - config file not found. Copy config/config.inc.php.dist to config/config.inc.php and configure to your environment.");
}

// Include configs
require_once LMS_WEB_PAGE_TO_ROOT . 'config/config.inc.php';
// require_once( 'dvwaPhpIds.inc.php' );

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
			lmsRedirect( LMS_WEB_PAGE_TO_ROOT . 'login.php' );
		}
	}
}

function lmsLogin( $pUsername ) {
	$lmsSession =& lmsSessionGrab();
	$lmsSession[ 'username' ] = $pUsername;
}


function lmsIsLoggedIn() {
	$lmsSession =& lmsSessionGrab();
	return isset( $lmsSession[ 'username' ] );
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
		'body'            => '',
		'page_id'         => '',
		'help_button'     => '',
		'source_button'   => '',
	);
	return $returnArray;
}


function lmsSecurityLevelGet() {
	return isset( $_COOKIE[ 'security' ] ) ? $_COOKIE[ 'security' ] : 'impossible';
}


function lmsSecurityLevelSet( $pSecurityLevel ) {
	if( $pSecurityLevel == 'impossible' ) {
		$httponly = true;
	}
	else {
		$httponly = false;
	}
	setcookie( session_name(), session_id(), null, '/', null, null, $httponly );
	setcookie( 'security', $pSecurityLevel, NULL, NULL, NULL, NULL, $httponly );
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
			lmsRedirect( LMS_WEB_PAGE_TO_ROOT . 'setup.php' );
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


$phpDisplayErrors = 'PHP function display_errors: <em>' . ( ini_get( 'display_errors' ) ? 'Enabled</em> <i>(Easy Mode!)</i>' : 'Disabled</em>' );                                                  // Verbose error messages (e.g. full path disclosure)
$phpSafeMode      = 'PHP function safe_mode: <span class="' . ( ini_get( 'safe_mode' ) ? 'failure">Enabled' : 'success">Disabled' ) . '</span>';                                                   // DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0
$phpMagicQuotes   = 'PHP function magic_quotes_gpc: <span class="' . ( ini_get( 'magic_quotes_gpc' ) ? 'failure">Enabled' : 'success">Disabled' ) . '</span>';                                     // DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0
$phpURLInclude    = 'PHP function allow_url_include: <span class="' . ( ini_get( 'allow_url_include' ) ? 'success">Enabled' : 'failure">Disabled' ) . '</span>';                                   // RFI
$phpURLFopen      = 'PHP function allow_url_fopen: <span class="' . ( ini_get( 'allow_url_fopen' ) ? 'success">Enabled' : 'failure">Disabled' ) . '</span>';                                       // RFI
$phpGD            = 'PHP module gd: <span class="' . ( ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) ? 'success">Installed' : 'failure">Missing - Only an issue if you want to play with captchas' ) . '</span>';                    // File Upload
$phpMySQL         = 'PHP module mysql: <span class="' . ( ( extension_loaded( 'mysqli' ) && function_exists( 'mysqli_query' ) ) ? 'success">Installed' : 'failure">Missing' ) . '</span>';                // Core DVWA
$phpPDO           = 'PHP module pdo_mysql: <span class="' . ( extension_loaded( 'pdo_mysql' ) ? 'success">Installed' : 'failure">Missing' ) . '</span>';                // SQLi
$DVWARecaptcha    = 'reCAPTCHA key: <span class="' . ( ( isset( $_DVWA[ 'recaptcha_public_key' ] ) && $_DVWA[ 'recaptcha_public_key' ] != '' ) ? 'success">' . $_DVWA[ 'recaptcha_public_key' ] : 'failure">Missing' ) . '</span>';

$DVWAUploadsWrite = '[User: ' . get_current_user() . '] Writable folder ' . $PHPUploadPath . ': <span class="' . ( is_writable( $PHPUploadPath ) ? 'success">Yes' : 'failure">No' ) . '</span>';                                     // File Upload
$bakWritable = '[User: ' . get_current_user() . '] Writable folder ' . $PHPCONFIGPath . ': <span class="' . ( is_writable( $PHPCONFIGPath ) ? 'success">Yes' : 'failure">No' ) . '</span>';   // config.php.bak check                                  // File Upload
$DVWAPHPWrite     = '[User: ' . get_current_user() . '] Writable file ' . $PHPIDSPath . ': <span class="' . ( is_writable( $PHPIDSPath ) ? 'success">Yes' : 'failure">No' ) . '</span>';                                              // PHPIDS

$DVWAOS           = 'Hệ điều hành: <em>' . ( strtoupper( substr (PHP_OS, 0, 3)) === 'WIN' ? 'Windows' : '*nix' ) . '</em>';
$SERVER_NAME      = 'Web Server SERVER_NAME: <em>' . $_SERVER[ 'SERVER_NAME' ] . '</em>';                                                                                                          // CSRF

$MYSQL_USER       = 'Database username: <em>' . $_LMS[ 'db_user' ] . '</em>';
$MYSQL_PASS       = 'Database password: <em>' . ( ($_LMS[ 'db_password' ] != "" ) ? '******' : '*blank*' ) . '</em>';
$MYSQL_DB         = 'Database database: <em>' . $_LMS[ 'db_database' ] . '</em>';
$MYSQL_SERVER     = 'Database host: <em>' . $_LMS[ 'db_server' ] . '</em>';
$MYSQL_PORT       = 'Database port: <em>' . $_LMS[ 'db_port' ] . '</em>';
// -- END (Setup Functions)

?>
