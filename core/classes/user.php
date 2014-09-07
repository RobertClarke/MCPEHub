<?php

class User {
	
	function __construct( $db ) {
		
		global $user_info;
		if ( !isset( $user_info ) ) $user_info = array();
		
		$this->db = $db;
		
	}
	
	function last_active() {
		
		// Only update if user is logged in.
		if ( $this->logged_in() ) {
			
			// Update database values.
			$update_vals = array( 'last_ip' => $this->info('id'), 'last_active' => date( 'Y-m-d H:i:s' ) );
			$this->db->where( array( 'id' => $this->info('id') ) )->update( 'users', $update_vals );
			
			return TRUE;
			
		} else return FALSE;
		
	}
	
	// Grab ID from username.
	public function get_id( $username ) {
		
		if ( !$this->check_username( $username ) ) return FALSE;
		
		$query = $this->db->select( 'id' )->from( 'users' )->where( array( 'username' => $username ) )->fetch();
		return $query[0]['id'];
		
	}
	
	// Check if username exists.
	public function check_username( $username ) {
		
		$this->db->from( 'users' )->where( array( 'username' => $username ) )->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
		
	}
	
	// Check if email exists.
	public function check_email( $email ) {
		
		$this->db->from( 'users' )->where( array( 'email' => $email ) )->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
		
	}
	
	// Check if user ID exists.
	public function check_id( $id ) {
		
		$this->db->from( 'users' )->where( array( 'id' => $id ) )->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
		
	}
	
	// Check users permission level.
	public function get_level( $user = '' ) {
		
		// info() will handle most of the hard work.
		if ( $return = $this->info( 'level', $user ) ) return $return;
		else return 0;
		
	}
	
	public function is_admin( $user = '' ) {
		
		if ( $this->get_level( $user ) == 9 ) return TRUE;
		else return FALSE;
		
	}
	
	public function is_mod( $user = '' ) {
		
		if ( $this->get_level( $user ) == 1 ) return TRUE;
		else return FALSE;
		
	}
	
	public function is_verified( $user = '' ) {
		
		if ( $this->info( 'verified', $user ) == 1 ) return TRUE;
		else return FALSE;
		
	}
	
	public function badges( $user = '' ) {
		
		// Check if user exists.
		if ( !$this->info('', $user) ) return FALSE;
		
		$badges = array(
			'admin'		=> array( 'Admin', 'bolt' ),
			'youtuber'	=> array( '', 'youtube-play', 'Verified YouTuber' ),
			'mod'		=> array( 'Mod', 'gavel' ),
			'verified'	=> array( '', 'check', 'Verified Member' )
		);
		
		// Verified check.
		if ( $this->is_verified( $user ) ) $badge[] = 'verified';
		
		// YouTuber check.
		if ( $this->info( 'youtuber', $user ) == 1 ) $badge[] = 'youtuber';
		
		// Admin check.
		if ( $this->is_admin( $user ) ) $badge[] = 'admin';
		
		// Mod check.
		else if ( $this->is_mod( $user ) ) $badge[] = 'mod';
		
		// No badges to show.
		if ( empty( $badge ) ) return FALSE;
		
		$return = '';
		
		foreach( $badge as $the_badge ) {
			
			$solo = ( empty( $badges[$the_badge][0] ) ) ? ' solo' : '';
			
			$class = ( isset( $badges[$the_badge][2] ) ) ? ' tip' : '';
			$tip = ( isset( $badges[$the_badge][2] ) ) ? ' data-tip="'.$badges[$the_badge][2].'"' : '';
			
			$return .= '<span class="rank '.$the_badge.$class.$solo.'"'.$tip.'><i class="fa fa-'.$badges[$the_badge][1].'"></i>'.$badges[$the_badge][0].'</span> ';
			
		}
		
		return $return;
		
	}
	
	// Check if user is logged in.
	public function logged_in() {
		
		if ( !isset( $_COOKIE[AUTHCOOKIE] ) || empty( $_COOKIE[AUTHCOOKIE] ) ) return FALSE;
		
		if ( $this->validate_auth_cookie() ) return TRUE;
		else return FALSE;
		
	}
	
	// Log user out.
	public function log_out() {
		
		if ( !$this->logged_in() ) return FALSE;
		
		// Expire the cookie.
		set_cookie( AUTHCOOKIE, $_COOKIE[AUTHCOOKIE], time() - 1 );
		
		redirect( '/login?logged_out' );
		die();
		
	}
	
	// Check if user is suspended.
	public function suspended( $user = '' ) {
		
		return ( $this->info( 'suspended', $user ) == 1 ) ? TRUE : FALSE;
		
	}
	
	// Authenticate user.
	public function auth() {
		
		// If user is logged in, check if they exist/suspended.
		if ( $this->logged_in() ) {
			
			// If user ID no longer exists, log out.
			if ( !$this->check_id( $this->info( 'id', '' ) ) ) $this->log_out();
			
			// If user is suspended, log out.
			else if ( $this->suspended() ) $this->log_out();
			
			return TRUE;
			
		}
		
		// User isn't logged in, redirect them to login.
		else {
			
			$base = basename( $_SERVER['PHP_SELF'], '.php' );
			
			// Redirect, as needed.
			if ( $base != 'login' && $base != 'index' ) {
				$currentPage = str_replace( '&', '%26', basename( $_SERVER['REQUEST_URI'] ) );
				redirect( "/login?auth_req&redirect=$currentPage" );
			}
			else redirect( '/login?auth_req' );
			
			die();
			
		}
		
	}
	
	// Get user info.
	public function info( $part = '', $user = '' ) {
		
		global $user_info;
		
		// If no value given, assume we're using currently logged in user.
		if ( empty( $user ) && !$this->logged_in() ) return FALSE;
		else if ( empty( $user ) && $this->logged_in() ) $user = $this->parse_auth_cookie()['username'];
		
		
		$user_found = FALSE;
		
		// User id found as key in array.
		//if ( is_numeric( $user ) && array_key_exists( $user, $user_info ) ) {
		//	$user_found = TRUE;
		//}
		
		// User id not found as key, search for username in array.
		if ( !is_numeric( $user ) ) {
			
			// Loop through and check if user is already in array.
			foreach( $user_info as $user_id => $info ) {
				
				// Match found.
				if ( $info['username'] == $user ) {
					
					// Set user and break out of foreach loop.
					$user = $user_id;
					$user_found = TRUE;
					
					break;
					
				} // End if username match.
				
			} // End foreach.
		
		} // End if numeric.
		
		
		// User id/username not found in array, search in database.
		if ( !$user_found ) {
			
			// If $user is numeric, then assume ID, otherwise assume username.
			// If username, we have to grab the ID from the database first.
			if ( !is_numeric( $user ) ) $user = $this->get_id( $user );
			
		}
		
		// If user doesn't exist, we can't return info!
		//if ( !$this->check_id( $user ) ) return FALSE;
		
		// If user info already in array, return it.
		if ( isset( $user_info[$user] ) ) {
			
			if ( !empty( $part ) ) return $user_info[$user][$part];
			else return $user_info[$user];
			
		}
		
		// User isn't in array, grab from database and store in array.
		else {
			
			$query = $this->db->from( 'users' )->where( array( 'id' => $user ) )->fetch();
			if ( $this->db->affected_rows != 1 ) return FALSE;
			
			$user_info[$user] = $query[0];
			
			if ( !empty( $part ) ) return $query[0][$part];
			else return $query[0];
			
		}
		
	}
	
	// Setting authentication cookies.
	public function set_auth_cookie( $username, $remember = FALSE ) {
		
		// Set expiry to 2 weeks if cookie, or set session.
		if ( $remember ) $cookie_expiry = time() + ( 60*60*24*14 );
		else $cookie_expiry = 0;
		
		// If the user isn't found in the database... can't grab password frag!
		if ( !$user = $this->info( '', $username ) )
			return FALSE;
		
		$pass_frag = substr( $user['password'], 4, 5 );
		
		$expires = time() + ( 60*60*24*14 );
		
		// Create and encrypt cookie values.
		$enc_key = hash_hmac( 'sha256', $username . $pass_frag . '|' . $expires, SECRET_KEY );
		$enc_hash = hash_hmac( 'sha256', $username . '|' . $expires, $enc_key );
		
		$cookie = $username . '|' . $expires . '|' . $enc_hash;
		
		// Finally create the cookie.		
		set_cookie( AUTHCOOKIE, $cookie, $cookie_expiry );
		
	}
	
	// Validating authentication cookies.
	public function validate_auth_cookie() {
		
		// If the auth cookie is validated, continue.
		if ( $cookie = $this->parse_auth_cookie() ) {
			
			// Check if the cookie has expired, unset if so (it's useless now).
			if ( time() > $cookie['expiry'] && $cookie['expiry'] != 0 ) {
				set_cookie( AUTHCOOKIE, $_COOKIE[AUTHCOOKIE], time() - 1 );
				return FALSE;
			}
			
			/*** Reverse algorithm magic! ***/
			
			// If the user isn't found in the database... invalid cookie!
			if ( !$user = $this->info( '', $cookie['username'] ) ) {
				set_cookie( AUTHCOOKIE, $_COOKIE[AUTHCOOKIE], time() - 1 );
				return FALSE;
			}
			
			$pass_frag = substr( $user['password'], 4, 5 );
			
			$enc_key = hash_hmac( 'sha256', $cookie['username'] . $pass_frag . '|' . $cookie['expiry'], SECRET_KEY );
			$enc_hash = hash_hmac( 'sha256', $cookie['username'] . '|' . $cookie['expiry'], $enc_key );
			
			if ( $enc_hash == $cookie['token'] ) return TRUE;
			else {
				set_cookie( AUTHCOOKIE, $_COOKIE[AUTHCOOKIE], time() - 1 );
				return FALSE;
			}
			return TRUE;
			
		} else return FALSE; // Auth cookie validation failed.
		
	}
	
	// Parse authentication cookies.
	private function parse_auth_cookie() {
		
		// Check if auth cookie exists.
		if ( empty( $_COOKIE[AUTHCOOKIE] ) )
			return FALSE;
		
		$cookie = $_COOKIE[AUTHCOOKIE];
		
		// If cookie isn't set or empty, parse failed.
		if ( !isset( $cookie ) || empty( $cookie ) ) return FALSE;
		
		// Explode the cookie using '|' for separator.
		$cookie_elements = explode( '|', $cookie );
		
		// If cookie doesn't have 3 values, it's invalid.
		if ( count( $cookie_elements ) != 3 ) return FALSE;
		
		// List and return cookie values.
		list( $username, $expiry, $token ) = $cookie_elements;
		return compact( 'username', 'expiry', 'token' );
		
	}
	
}

?>