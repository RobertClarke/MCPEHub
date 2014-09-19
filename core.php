<?php

/**
  * Website Core Functions
**/

function redirect( $target ) {
	
	if ( !headers_sent() ) {
		header( 'Location: ' . $target, TRUE, 302 );
	}
	
	else {
		
		echo '<script type="text/javascript">';
		echo 'window.location.href="'.$target.'";';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url='.$target.'" />';
		echo '</noscript>';
		
	}
	
	die();
}

function set_cookie( $name, $value, $expiry, $path = '/', $domain = '', $secure = FALSE, $http = TRUE ) {
	setcookie( $name, $value, $expiry, $path, $domain, $secure, $http );
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


function time_since( $ptime ) {
	
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



// Create our secured value.
function create_secure_value( $data, $salt = '' ) {
	
	if ( empty( $salt ) ) $salt = hash_hmac( 'sha256', SECRET_KEY, SECRET_KEY );
	
	$options = array( 'salt' => $salt, 'cost' => 11 );
	return password_hash( $data, PASSWORD_BCRYPT, $options );
	
}


function is_email( $value )
  {
    $atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
    $localPart = "(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)"; // quoted or unquoted
    $alpha = "a-z\x80-\xFF"; // superset of IDN
    $domain = "[0-9$alpha](?:[-0-9$alpha]{0,61}[0-9$alpha])?"; // RFC 1034 one domain component
    $topDomain = "[$alpha](?:[-0-9$alpha]{0,17}[$alpha])?";
    return (bool) preg_match( "(^$localPart@(?:$domain\\.)+$topDomain\\z)i", $value );
}


function random_str( $length = 10 ) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

?>