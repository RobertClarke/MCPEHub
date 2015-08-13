<?php

/**
 * Main Website Functions
 *
 * Contains all general functions necessary for the website to
 * work. These functions are used by most of the other classes
 * and scripts around the website.
**/

/**
 * Sanitizes usernames for database checks
 *
 * @since 3.0.0
 *
 * @param string $username The username to be sanitized
 * @return string Sanitized username value
**/
function sanitize_user( $username ) {
	return preg_replace('/[^\w]/', '', trim(strtolower($username)) );
}



function alphanum( $input ) {
	if ( preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $input) ) return true;
	else return false;
}



/**
 * Sets the value of a cookie
 *
 * @since 3.0.0
 *
 * @param string $name The name of the cookie to set
 * @param string|int $value The value to assign to the cookie
 * @param int $expiry The expiry time, in seconds
 * @param string $path The path for the cookie to be valid in
 * @param string $domain The domain for the cookie to be valid in
 * @param boolean $secure Whether or not this is an HTTPS cookie
 * @param boolean $http Whether or not this is an HTTP only cookie
**/
function cookie_set( $name, $value, $expiry, $path='/', $domain='', $secure=false, $http=true ) {
	setcookie($name, $value, $expiry, $path, $domain, $secure, $http);
	return;
}

/**
 * Expires a given cookie
 *
 * @since 3.0.0
 *
 * @param string $name The name of the cookie to expire
**/
function cookie_expire( $name ) {
	return cookie_set($name, '', time()-1);
}

/**
 * Redirects the browser to a given page
 *
 * @since 3.0.0
 *
 * @param string $page The page to redirect to
**/
function redirect( $page ) {

	if ( !headers_sent() )
		header('Location: '.$page, TRUE, 302);

	// Fallback to Javascript meta redirect
	else {
		echo '<script type="text/javascript">window.location.href="'.$page.'";</script>';
		echo '<noscript><meta http-equiv="refresh" content="0;url='.$page.'" /></noscript>';
	}

	die();
}

/**
 * Returns the offset required for pagination purposes
 *
 * @since 3.0.0
 *
 * @param int $total Total number of posts in query
 * @param int $per Total number of posts to show per page
 * @param int $page Current page (optional)
 * @return int The offset for the pagination query
**/
function pagination_offset( $total, $per, $page=null ) {

	$max_pages = ceil($total / $per);

	$page = is_numeric($page) ? (int) $page : 1;

	if ( $page > $max_pages )
		$page = $max_pages;

	if ( $page < 1 )
		$page = 1;

	$offset = ($page - 1) * $per;

	return $offset;

}



function input_POST( $id ) {
	return filter_input(INPUT_POST, $id);
}

function input_GET( $id ) {
	return filter_input(INPUT_GET, $id);
}

function submit_POST() {
	return ( filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST' );
}



function is_email($value) {
    $atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]";
    $localPart = "(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)";
    $alpha = "a-z\x80-\xFF";
    $domain = "[0-9$alpha](?:[-0-9$alpha]{0,61}[0-9$alpha])?";
    $topDomain = "[$alpha](?:[-0-9$alpha]{0,17}[$alpha])?";
    return (bool) preg_match("(^$localPart@(?:$domain\\.)+$topDomain\\z)i", $value);
}


function is_url($url) {
	return ( filter_var($url, FILTER_VALIDATE_URL) === false ) ? false : true;
}


function random_string($length=10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) $randomString .= $characters[rand(0, strlen($characters) - 1)];
    return $randomString;
}



function length($input, $max, $min=0) {

	if ( $min < 0 ) $min = 0;

	if ( $min != 0 ) {
		if ( strlen($input)<$min || strlen($input)>$max ) return FALSE;
	} else {
		if ( strlen($input)>$max ) return FALSE;
	}

	return TRUE;

}


function generate_slug( $str, $replace=array(), $delimiter='-' ) {
	if( !empty($replace) )
		$str = str_replace((array)$replace, ' ', $str);

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	$clean = ltrim( $clean, '-' );
	$clean = rtrim( $clean, '-' );

	return $clean;
}

function clean_slug( $slug ) {
	global $db;

	$slug = strtolower($db->escape($slug));

	// Remove dashes from beginning and end of slug
	if ( substr( $slug, -1) == '-' )
		$slug = rtrim( $slug, '-' );

	if ( substr( $slug, 0, 1 ) == '-' )
		$slug = ltrim( $slug, '-');

	return $slug;

}