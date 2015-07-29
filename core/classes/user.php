<?php

/**
 * User Class
 *
 * An object containing all information related to a given user,
 * including any database values, permissions and authentication
 * related information.
**/

class User {

	// User info
	public $id = 0;
	public $username = '';

	// Data container
	public $data;

	// User's permission level
	public $level = 0;

	/**
	 * Constructor
	 *
	 * Determines and stores the various variations of the current page value.
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $id User identifier. Can be either ID (int) or username (string).
	**/
	public function __construct( $id ) {

		// If $id passed isn't numeric, we have to initialize by username.
		if ( is_numeric($id) ) {
			$this->id = $id;
			$data = self::get_by('id', $id);
		} else {
			$data = self::get_by('username', $id);
		}

		if ( $data ) {
			$this->id = $data['id'];
			$this->username = $data['username'];
			$this->level = $data['permission'];

			$this->data = $data;
		}
		else {
			$this->id = 0;
			return false;
		}

		// Cache this user object for later use.
		cache_add($this->id, $this, 'users_objects');

	}

	/**
	 * Grabs user information from the database
	 *
	 * Determines and stores the various variations of the current page value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The field to perform a query with ('id' or 'username')
	 * @param string|int $value The value to perform the query with
	**/
	public static function get_by( $key, $value ) {
		global $db;

		if ( $key == 'id' ) {
			if ( !is_numeric($value) || $value < 1 )
				return false;

			$value = intval($value);
		}

		if ( !$value )
			return false;

		// Check if valid $key given
		switch ( $key ) {
			case 'id':
				$db_field = 'id';
				$user_id = $value;
			break;
			case 'username':
				$db_field = 'username';
				$user_id = cache_get($value, 'users_username');
			break;
			case 'email':
				$db_field = 'email';
				$user_id = cache_get($value, 'users_email');
			break;
			default:
				return false; // None of the above valid options, return false

		}

		// Get from cache if user ID exists to search with.
		if ( $user_id !== false ) {
			if ( $user = cache_get($user_id, 'users') )
				return $user;
		}

		// Get from database if not found in cache.
		if ( !$user = $db->from('users')->where([$db_field => $value])->fetch_first() )
			return false;

		cache_add_user($user);

		return $user;

	}

	/**
	 * Returns whether or not user has a given permission level
	 *
	 * @since 3.0.0
	 *
	 * @param string $permission The permission value to check
	 * @return boolean Whether or not the user has the permission
	**/
	public function is($level) {

		// Load permissions from cache.
		$permissions = get_user_permissions();

		// Check if key is valid.
		if ( !array_key_exists($level, $permissions) )
			return false;

		// Check if user is at the given permission level.
		if ( $this->level !== $permissions[$level] )
			return false;

		return true;

	}

}

/**
 * Adds a user to the user cache
 *
 * @since 3.0.0
 *
 * @param object $user The user object to be cached
**/
function cache_add_user( $user ) {
	cache_add($user['id'], $user, 'users');
	cache_add($user['username'], $user['id'], 'users_username');
}

/**
 * Loads core database permission values to cache
 *
 * Permissions will be grabbed from the database table and will be stored in
 * the cache if they don't already exist.
 *
 * @since 3.0.0
 *
 * @return array Array containing permissions and order
**/
function get_user_permissions() {
	global $db;

	if ( $cache = cache_get('permissions', 'core') )
		return $cache;

	$permissions = [];

	foreach( $db->from('permissions')->fetch() as $id => $val )
	    $permissions[$val['key']] = $val['position'];

	cache_add('permissions', $permissions, 'core');
	return $permissions;
}

/**
 * User authentication
 *
 * Can authenticate a user using the credentials passed through an array
 * containing 'username', 'password' and 'remember' (optional) values.
 * If the $login array is missing, the $_POST values will be assumed.
 *
 * @since 3.0.0
 *
 * @param array $login Optional - User info passed in array to login
 * @return User|Error Objects depending on success/fail
**/
function login( $login=[] ) {

	// $login empty, use $_POST values instead
	if ( empty($login) ) {
		$login['username'] = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
		$login['password'] = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
		$login['remember'] = filter_input(INPUT_POST, 'remember', FILTER_VALIDATE_BOOLEAN);
	}

	$user = authenticate($login['username'], $login['password']);

	if ( !is_error($user) )
		auth_set($user->username, $login['remember']);

	return $user;

}






function authenticate( $username, $password ) {

	if ( empty($username) || empty($password) )
		return new Error('AUTH_MISSING', 'Both username &amp; password required.');

	// Get user information required + check if valid user
	if ( !$user = User::get_by('username', $username) )
		return new Error('AUTH_FAILED', 'Incorrect username or password.');

	// Verify password validity
	if ( !password_verify( $password, $user['password'] ) )
		return new Error('AUTH_FAILED', 'Incorrect username or password.');

	// Check if user is suspended or banned
	if ( $user['status'] == '-1' )
		return new Error('AUTH_SUSPENDED', 'Your account is currently suspended.');

	if ( $user['status'] == '-2' )
		return new Error('AUTH_BANNED', 'Your account has been banned.');

	// Everything checks out, user is authenticated
	return new User($user['id']);

}




/**
 * Login check
 *
 * Will check if the user is logged in. Used for pages that require part
 * of the page be accessible to users only.
 *
 * @since 3.0.0
 *
 * @return User|Error Objects depending on success/fail
**/
function logged_in() {
	return auth_validate();
}

/**
 * Login check + redirect
 *
 * Will check if the user is logged in. If they are, the page can resume
 * to load, otherwise redirect to login.php and prevent the page from
 * loading any further. Used on pages that require logged in users.
 *
 * @since 3.0.0
**/
function login_redirect() {
	if ( !logged_in() ) {

		$curent_page = basename(filter_input(INPUT_SERVER, 'PHP_SELF'), '.php');

		if ( $current_page != 'login' ) {
			$redirect = str_replace('&', '%26', basename(filter_input(INPUT_SERVER, 'REQUEST_URI')));
			redirect('/login?m=auth&redirect='.$redirect);
		}
		else redirect('/login?m=auth');
	}
}



function logout() {

	if ( !logged_in() )
		return false;

	auth_expire();
	redirect('/login?m=logout');

}



function auth_cookie() {
	return filter_input(INPUT_COOKIE, 'mcpehub_a');
}

function auth_parse() {

	$cookie = auth_cookie();

	// Check if the cookie exists
	if ( !isset($cookie) || empty($cookie) )
		return false;

	// Explode values in cookie into an array
	if ( !$cookie = explode('|', $cookie) )
		return false;

	// Check # of fields in cookie
	if ( count($cookie) != 3 )
		return false;

	list($user, $expiry, $token) = $cookie;
	return compact('user', 'expiry', 'token');

}

function auth_validate() {

	$cookie = auth_cookie();

	// Verify if cookie is in valid format
	if ( !$cookie = auth_parse($cookie) )
		return false;

	// Check if cookie is expired
	if ( time() > $cookie['expiry'] && $cookie['expiry'] != 0 ) {
		auth_expire();
		return false;
	}

	// Get user information required for decrypt + check if valid user
	if ( !$user = User::get_by('username', $cookie['user']) ) {
		auth_expire();
		return false;
	}

	$fragment = substr($user['password'], 10, 12);

	// Create cookie hashes
	$key = hash_hmac('sha1', $cookie['user'] .'|'. $fragment .'|'. $cookie['expiry'], KEY_SECRET);
	$hash = hash_hmac('sha1', $cookie['user'] .'|'. $cookie['expiry'], $key);

	// Check if computed hash matches cookie hash
	if ( $hash != $cookie['token'] ) {
		auth_expire();
		return false;
	}

	// All checks passed, cookie is valid
	return $user;

}

function auth_expire() {
	cookie_expire('mcpehub_a');
	return;
}

function auth_set( $username, $remember=false ) {

	// Default expiry time is 3 months for 'remember me' feature
	$expiry = ( $remember ) ? time()+(60*60*24*30*3) : 0;

	// Get user information required for decrypt + check if valid user
	if ( !$user = User::get_by('username', $username) )
		return false;

	$fragment = substr($user['password'], 10, 12);

	// Create cookie hashes
	$key = hash_hmac('sha1', $username .'|'. $fragment .'|'. $expiry, KEY_SECRET);
	$hash = hash_hmac('sha1', $username .'|'. $expiry, $key);

	cookie_set('mcpehub_a', $username .'|'. $expiry .'|'. $hash, $expiry);
	return;

}





function username_avail( $username ) {

	if ( !User::get_by('username', $username) )
		return true;

	return false;

}

function email_avail( $email ) {

	if ( !User::get_by('email', $email) )
		return true;

	return false;

}