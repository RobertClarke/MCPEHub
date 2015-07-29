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



/*
class Pagination {

	private $html = '';

	function __construct($url) {
		$this->url = $url;
	}

	// Builds pagination, outputs offset.
	public function build($total_posts, $per_page, $current_page='') {

		//$total_pages = ceil($total_posts / $per_page);

		//$page = ( !empty($current_page) && is_numeric($current_page) ) ? (int)$current_page : 1;

		//if ( $page > $total_pages ) $page = $total_pages;
		//if ( $page < 1 ) $page = 1;

		// Set page number through URL class.
		if ( $page != 1 ) $this->url->add('page', $page);

		//$offset = ($page - 1) * $per_page;

		$range = 2;

		if ( $total_pages > 1 ) {

			// Back link.
			if ( $page>1 ) $this->html .= '<a href="'.$this->url->show('page='.($page-1)).'" class="bttn"><i class="fa fa-angle-double-left solo"></i></a>';

			for ( $i = ($page - $range); $i < ($page + $range + 1); $i++ ) {

				if ( ($i > 0) && ($i <= $total_pages) ) {

					if ( $i == $page ) $this->html .= '<a href="'.$this->url->show('page='.$i).'" class="bttn active">'.$i.'</a>';
					else $this->html .= '<a href="'.$this->url->show('page='.$i).'" class="bttn">'.$i.'</a>';

				}

			}

			// Forward link.
			if ($page != $total_pages) $this->html .= '<a href="'.$this->url->show('page='.($page+1)).'" class="bttn"><i class="fa fa-angle-double-right solo"></i></a>';

		}

		return $offset;

	}

	// Echoes HTML version of pagination links.
	public function html($container_class='') {
		if ( !empty($this->html) ) echo '<div class="pagination bttn-group '.$container_class.'"><div class="pages">'.$this->html.'</div></div>';
	}

}*/