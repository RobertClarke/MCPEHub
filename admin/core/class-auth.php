<?php

class Auth {
	
	private $db, $user;
	public function __construct( $db, $user ) {
		$this->db = $db;
		$this->user = $user;
	}
	
	function auth() {
		
		// If user is logged in, check if they exist/suspended.
		if ( $this->loggedIn() ) {
			
			// User ID no longer exists in database, log out.
			if ( !$this->user->checkID() ) $this->logOut();
			
			// User is suspended, log out.
			else if ( $this->user->suspended() ) $this->logOut();
			
			// User isn't an admin, redirect.
			else if ( !$this->user->isAdmin() ) redirect( '../index.php' );
			
			// Everything all good.
			return TRUE;
			
		}
		
		// User isn't logged in, redirect them to the login page.
		else {
			
			// Set redirect variable.
			$base = basename( $_SERVER[ 'PHP_SELF' ], '.php' );
			
			// Redirect the user, as needed.
			$currentPage = str_replace( '&', '%26', basename( $_SERVER['REQUEST_URI'] ) );
			redirect( "../login.php?redirect=$currentPage&authreq=true" );
			
			die();
			
		}
		
	}
	
	function lastActive() {
		
		// Only update if user is logged in.
		if ( $this->loggedIn() ) {
			
			// Update database values.
			$update_vals = array( 'last_ip' => $_SERVER['REMOTE_ADDR'], 'last_active' => date( 'Y-m-d H:i:s' ) );
			$this->db->where( array( 'id' => $_SESSION['mcpe_user'] ) )->update( 'users', $update_vals );
			
			return TRUE;
			
		} else return FALSE;
		
	}
	
	function loggedIn() {
		
		return isset( $_SESSION['mcpe_user'] ) ? TRUE : FALSE;
		
	}
	
	function logOut() {
		
		unset( $_SESSION['mcpe_user'] );
		redirect( '../login.php?loggedout=true' );
		die();
		
	}
	
}

?>