<?php

class User {
	
	private $db;
	public function __construct( $db ) {
		
		global $user_info;
		if ( !isset( $user_info ) ) $user_info = array();
		
		$this->db = $db;
		
	}
	
	function checkUsername( $username ) {
		
		$this->db->from( 'users' )->where( array( 'username' => $username ) )->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
		
	}
	
	function checkID( $id = '' ) {
		
		// If no ID, assume it's for the user logged in.
		if ( empty( $id ) ) $id = $_SESSION['mcpe_user'];
		
		$this->db->from( 'users' )->where( array( 'id' => $id ) )->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
		
	}
	
	function getID( $username ) {
		
		if ( $this->checkUsername( $username ) ) {
			
			$query = $this->db->select( 'id' )->from( 'users' )->where( array( 'username' => $username ) )->fetch();
			return $query[0]['id'];
		
		} else return FALSE;
		
	}
	
	function info( $id = '' ) {
		
		global $user_info;
		
		// If no ID, assume it's for the user logged in.
		if ( empty( $id ) && !$this->loggedIn() ) return FALSE;
		else if ( empty( $id ) ) $id = $_SESSION['mcpe_user'];
		
		// If user info already in array, return it.
		if ( isset( $user_info[$id] ) ) return $user_info[$id];
		
		// User isn't in the array, grab from database and store it.
		else {
			
			$query = $this->db->from( 'users' )->where( array( 'id' => $id ) )->fetch();
			$user_info[$id] = $query[0];
			
			return $query[0];
			
		}
		
	}
	
	function suspended( $id = '' ) {
		
		// If no ID, assume it's for the user logged in.
		if ( empty( $id ) ) $id = $_SESSION['mcpe_user'];
		
		// Return if user is suspended.
		return $this->info( $id )['suspended'] ? TRUE : FALSE;
		
	}
	
	function level( $id = '' ) {
		
		// If no ID, assume it's for the user logged in.
		if ( $this->loggedIn() && empty( $id ) ) $id = $_SESSION['mcpe_user'];
		else if ( empty( $id ) ) return 0;
		
		return $this->info( $id )['level'];
		
	}
	
	function isMod( $id = '' ) {
		
		// If no ID, assume it's for the user logged in.
		if ( $this->loggedIn() && empty( $id ) ) $id = $_SESSION['mcpe_user'];
		else if ( empty( $id ) ) return FALSE;
		
		if ( $this->level( $id ) == 1 ) return TRUE;
		else return FALSE;
		
	}
	
	function isAdmin( $id = '' ) {
		
		// If no ID, assume it's for the user logged in.
		if ( $this->loggedIn() && empty( $id ) ) $id = $_SESSION['mcpe_user'];
		else if ( empty( $id ) ) return FALSE;
		
		if ( $this->level( $id ) == 9 ) return TRUE;
		else return FALSE;
		
	}
	
	function loggedIn() {
		
		return isset( $_SESSION['mcpe_user'] ) ? TRUE : FALSE;
		
	}
	
	function isSubbed( $subscriber ) {
		
		if ( $this->loggedIn() ) {
			
			$this->db->from( 'subscriptions' )->where( array( 'user_subscribed' => $subscriber, 'user_sub' => $this->info()['id'] ) )->fetch();
			if ( $this->db->affected_rows == 1 ) return TRUE;
			else return FALSE;
			
		} else return FALSE;
		
	}
	
}

?>