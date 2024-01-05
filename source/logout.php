<?php

require_once 'resources/includes/lms.inc.php';

if( !lmsIsLoggedIn() ) {	// The user shouldn't even be on this page
	// dvwaMessagePush( "You were not logged in" );
	lmsRedirect( 'login.php' );
}

lmsLogout();
lmsMessagePush( "You have logged out" );
lmsRedirect( 'login.php' );

?>
