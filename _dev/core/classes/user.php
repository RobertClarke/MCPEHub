<?php

/**
  
  * User Class
  *
  * Deals with all user-related functions that are needed for
  * authentication and identification around the website.
  *
  * auth();				Verifies or redirects the user, as needed.
  * logout();			Logs users out of their accounts.
  * logged_in();		Returns if user is logged in.
  * info();				Returns user data from database.
  * update_activity();	Updates user last activity time and ip.
  * check_id();			Checks if user id exists in users table.
  * check_username();	Checks if username exists in users table.
  * check_email();		Checks if email exists in users table.
  * get_id();			Grab user id from a given username.
  * suspended();		Returns if user is currently suspended.
  * is_admin();			Returns if user is an admin.
  * is_mod();			Returns if user is a mod.
  * auth_set();			Set authentication cookie.
  * auth_parse();		Parse authentication cookie.
  * auth_validate();	Validate authentication cookie.
  * auth_exprie();		Expire authentication cookie.
  
**/

class User {
	
	function __construct($db) {
		
		global $user_data;
		if ( !isset( $user_data ) ) $user_data = [];
		
		$this->db = $db;
		
	}
	
	// Main user authentication function.
	public function auth() {
		
		// If user logged in, lets verify that they're still a valid user.
		if ( $this->logged_in() ) {
			
			// Check if user still exists in database.
			if ( !$this->check_id( $this->info('id') ) ) $this->logout();
			
			// Check if user is suspended.
			else if ( $this->suspended() ) $this->logout();
			
			return TRUE;
			
		}
		
		// User isn't logged in, redirect them to the login screen.
		else {
			
			// Add a redirect URL, if the user is outside of the home/login page.
			$page = basename( $_SERVER['PHP_SELF'], '.php' );
			
			if ( $page != 'index' && $page != 'login' ) {
				
				// Convert "&" symbol, build URL and redirect.
				$redirect = str_replace( '&', '%26', basename( $_SERVER['REQUEST_URI'] ) );
				redirect('/login?auth_req&r='.$redirect);
				
			} else redirect('/login?auth_req');
			
			// Make sure we don't let the user load anything else.
			die();
			
		}
		
	}
	
	// Log users out.
	public function logout() {
		
		if ( !$this->logged_in() ) return FALSE;
		
		// Expire auth cookie.
		set_cookie(AUTHCOOKIE, $_COOKIE[AUTHCOOKIE], time()-1);
		
		redirect('/login?logged_out');
		die();
		
	}
	
	// Returns if user is logged in.
	public function logged_in() {
		
		// Check for auth cookie existence.
		if ( !isset( $_COOKIE[AUTHCOOKIE] ) || empty ( $_COOKIE[AUTHCOOKIE] ) ) return FALSE;
		
		// Validate auth cookie.
		if ( $this->auth_validate() ) return TRUE;
		else return FALSE;
		
	}
	
	// Grabs given user data from database.
	public function info($part='', $user='') {
		
		global $user_data;
		
		// If no $user, assume we're getting data for logged in user.
		if ( empty( $user ) ) {
			if ( $this->logged_in() ) $user = $this->auth_parse()['username'];
			else return FALSE;
		}
		
		// If $user not numeric, search for username.
		if ( !is_numeric( $user ) ) {
			
			// First, check if username is valid.
			if ( !$this->check_username($user) ) return FALSE;
			
			// Check if username is in array (set $user to id).
			foreach( $user_data as $id => $u ) {
				
				if ( $u['username'] == $user ) {
					$user = $id;
					$found = TRUE; // Set variable to say we found the user.
					break;
				}
				
			}
			
			// If username not found in array, find users id.
			if ( !isset($found) ) $user = $this->get_id($user);
			
		}
		
		// If $user numeric, check if its a valid user id.
		else if ( !$this->check_id($user) ) return FALSE;
		
		// User info already in array, return it.
		if ( isset( $user_data[$user] ) ) {
			
			if ( !empty($part) ) return $user_data[$user][$part];
			else return $user_data[$user];
			
		}
		
		// User info isn't in array, grab, store and return.
		else {
			
			// Grab from database.
			$query = $this->db->from('users')->where( ['id' => $user] )->limit(1)->fetch();
			if ( !$this->db->affected_rows ) return FALSE;
			
			// Store in array.
			$u = $user_info[$user] = $query[0];
			
			if ( !empty($part) ) return $u[$part];
			else return $u;
			
		}
		
	}
	
	// Update users last activity time and IP address in db.
	public function update_activity() {
		
		if ( !$this->logged_in() ) return FALSE;
		
		$update = array(
			'last_ip'		=> $_SERVER['REMOTE_ADDR'],
			'last_active'	=> date('Y-m-d H:i:s')
		);
		
		$this->db->where( ['id' => $this->info('id')] )->update('users', $update);
		return;
		
	}
	
	// Check if user id exists in users table.
	public function check_id($id) {
		$this->db->from('users')->where( ['id' => $id] )->limit(1)->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
	}
	
	// Check if username exists in users table.
	public function check_username($username) {
		$this->db->from('users')->where( ['username' => $username] )->limit(1)->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
	}
	
	// Check if user email exists in users table.
	public function check_email($email) {
		$this->db->from('users')->where( ['email' => $email] )->limit(1)->fetch();
		return ( $this->db->affected_rows ) ? TRUE : FALSE;
	}
	
	// Grab user id from a given username.
	public function get_id($username) {
		$query = $this->db->select('id')->from('users')->where( ['username' => $username] )->fetch();
		return ( $this->db->affected_rows ) ? $query[0]['id'] : FALSE;
	}
	
	// Returns if user currently suspended.
	public function suspended($user='') {
		return ( $this->info('suspended', $user) == 1 ) ? TRUE : FALSE;
	}
	
	// Returns if user is an admin.
	public function is_admin($user='') {
		return ( $this->info('level', $user) == 9 ) ? TRUE : FALSE;
	}
	
	// Returns if user is a mod.
	public function is_mod($user='') {
		return ( $this->info('level', $user) == 1 ) ? TRUE : FALSE;
	}
	
	// Set authentication cookie.
	public function auth_set($username, $remember=FALSE) {
		
		// Set expiry to 1 month if remember is set, otherwise session.
		$expiry = ( $remember ) ? time()+(60*60*24*30) : 0;
		
		// Check if user exists in database.
		if ( !$user_pass = $this->info('password', $username) ) return FALSE;
		
		// Get password fragment for auth cookie.
		$pass_frag = substr( $user_pass, 4, 5 );
		
		$exp_time = time()+(60*60*24*30);
		
		// Create and encrypt cookie.
		$enc_key	= hash_hmac('sha256', $username . $pass_frag . '|' . $exp_time, SECRET_KEY);
		$enc_hash	= hash_hmac('sha256', $username . '|' . $exp_time, $enc_key);
		
		set_cookie(AUTHCOOKIE, $username.'|'.$exp_time.'|'.$enc_hash, $expiry);
		
	}
	
	// Parse authentication cookie.
	private function auth_parse() {
		
		$cookie = $_COOKIE[AUTHCOOKIE];
		
		// If cookie isn't set or empty, parse failed.
		if ( !isset($cookie) || empty($cookie) ) return FALSE;
		
		// Explode the cookie... BOOM!
		$cookie = explode('|', $cookie);
		
		// If cookie doesn't have 3 values, parse failed.
		if ( count($cookie) != 3 ) return FALSE;
		
		// Parse success! List & return cookie values.
		list($username, $expiry, $token) = $cookie;
		return compact('username', 'expiry', 'token');
		
	}
	
	// Validate authentication cookie.
	public function auth_validate() {
		
		// Check if cookie is in valid format + set variable.
		if ( !$cookie = $this->auth_parse() ) return FALSE;
		
		// Check if cookie has expired, unset if expired.
		if ( time() > $cookie['expiry'] && $cookie['expiry'] != 0 ) {
			$this->auth_expire();
			return FALSE;
		}
		
		// ** Reverse algoritm magic! ** //
		
		// Check if user exists in database.
		if ( !$user_pass = $this->info('password', $cookie['username']) ) {
			$this->auth_expire();
			return FALSE;
		}
		
		// Get password fragment for auth cookie.
		$pass_frag = substr( $user_pass, 4, 5 );
		
		$enc_key	= hash_hmac('sha256', $cookie['username'] . $pass_frag . '|' . $cookie['expiry'], SECRET_KEY);
		$enc_hash	= hash_hmac('sha256', $cookie['username'] . '|' . $cookie['expiry'], $enc_key);
		
		// Check if calculated value matches cookie value.
		if ( $enc_hash != $cookie['token'] ) {
			$this->auth_expire();
			return FALSE;
		}
		
		// Success, auth cookie valid.
		return TRUE;
		
	}
	
	// Expire authentication cookie.
	private function auth_expire() {
		set_cookie( AUTHCOOKIE, $_COOKIE[AUTHCOOKIE], time()-1);
		return;
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
	
}

?>