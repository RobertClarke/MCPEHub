<?php

/**
  
  * General Functions
  *
  * Includes generalized functions that are used throughout
  * the website. These functions aren't class-specific.
  *
  * redirect();		Redirect script.
  * set_cookie();	Shorthand function for setting cookies.
  * is_email();		Checks if string is a valid email.
  * random_str();	Generate random string of letters & numbers.
  * since();	Calculate time since specific point in time.
  
**/

// Redirect script. Uses JavaScript fallback if PHP headers already sent.
function redirect($target) {
	
	// If headers aren't sent, use PHP to redirect.
	if ( !headers_sent() ) header('Location: '.$target, TRUE, 302);
	
	// Otherwise, fallback to old-school JavaScript and meta redirect.
	else {
		echo '<script type="text/javascript">window.location.href="'.$target.'";</script>';
		echo '<noscript><meta http-equiv="refresh" content="0;url='.$target.'" /></noscript>';
	}
	
	// Prevent page from loading any further.
	die();
	
}

// Shorthand function for setting cookies.
function set_cookie($name, $value, $expiry, $path = '/', $domain = '', $secure = FALSE, $http = TRUE) {
	setcookie($name, $value, $expiry, $path, $domain, $secure, $http);
}

// Checks if string is a valid email.
function is_email($value) {
    $atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]";
    $localPart = "(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)";
    $alpha = "a-z\x80-\xFF";
    $domain = "[0-9$alpha](?:[-0-9$alpha]{0,61}[0-9$alpha])?";
    $topDomain = "[$alpha](?:[-0-9$alpha]{0,17}[$alpha])?";
    return (bool) preg_match("(^$localPart@(?:$domain\\.)+$topDomain\\z)i", $value);
}

// Generate random string of letters & numbers.
function random_str($length=10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) $randomString .= $characters[rand(0, strlen($characters) - 1)];
    return $randomString;
}

// Returns current client IP address for easy use in code.
function current_ip() {
	return $_SERVER['REMOTE_ADDR'];
}

// Returns current timestamp for easy use in code + database queries.
function time_now() {
	return date('Y-m-d H:i:s');
}

// Calculate time since specific point in time.
function since($ptime) {
	
	$etime = time() - $ptime;

	if ($etime < 1) return 'just now';
	
	$a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
				30 * 24 * 60 * 60	   	=>  'month',
				24 * 60 * 60			=>  'day',
				60 * 60				 	=>  'hour',
				60					 	=>  'minute',
				1					 	=>  'second'
	);

	foreach ( $a as $secs => $str ) {
		$d = $etime / $secs;
		if ( $d >= 1 ) {
			$r = round( $d );
			return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
		}
	}
	
}

function gather_files(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ( $i=0; $i<$file_count; $i++ ) {
        foreach ( $file_keys as $key ) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function generate_slug( $str, $replace=array(), $delimiter='-' ) {
	
	setlocale(LC_ALL, 'en_US.UTF8');
	
	if( !empty($replace) ) {
		$str = str_replace( (array)$replace, ' ', $str );
	}
	
	// Future: change IGNORE to TRANSLIT (really buggy).
	$clean = @iconv( 'UTF-8', 'ASCII//IGNORE', $str );
	$clean = preg_replace( "/[^a-zA-Z0-9\/_|+ -]/", '', $clean );
	//$clean = strtolower( trim($clean, '-') );
	$clean = preg_replace( "/[\/_|+ -]+/", $delimiter, $clean );
	
	$clean = ltrim( $clean, '-' );
	$clean = rtrim( $clean, '-' );
	
	$clean = strtolower( $clean );
	
	return $clean;
}

?>