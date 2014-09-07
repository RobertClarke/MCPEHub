<?php

class Url {
	
	public $the_url = '';
	public $url_ending = '';
	public $url_parts_temp = '';
	public $url_parts = array();
	
	function __construct() {
		
	}
	
	function add( $key, $val = '' ) {
		
		// Clean up value in case user input presented.
		$val = htmlspecialchars( $val );
		
		// Add the url part into the array.
		$this->url_parts[$key] = $val;
		return;
		
	}
	
	function remove() {
		
		// TODO: Remove or code, not sure if it's necessary.
		
	}
	
	// Clears all URL parts. Resets the whole url generation system.
	function reset() {
		
		$this->the_url = NULL;
		$this->url_ending = NULL;
		$this->url_parts = array();
		$this->url_parts_temp = NULL;
		
		return;
		
	}
	
	function show( $add = '', $reset = FALSE ) {
		
		// Throw out default URL, if we didn't just reset.
		//if ( empty( $this->url_parts ) && !$reset ) return $_SERVER['PHP_SELF'];
		
		if ( !$reset ) $this->url_parts_temp = $this->url_parts;
		else $this->url_parts_temp = array();
		
		// If something needs to be added to the URL.
		if ( !empty( $add ) ) {
			
			$add = explode( '&', $add );
			
			foreach ( $add as $the_add ) {
				$the_add = explode( '=', $the_add );
				$this->url_parts_temp[ $the_add[0] ] = $the_add[1];
			}
		}
		
		// Build the URL from the array.
		foreach ( $this->url_parts_temp as $key => $val ) $this->url_ending .= $key.'='.$val.'&';
		
		// Cut off last '&' from URL.
		$this->url_ending = substr( $this->url_ending , 0, -1 );
		
		$this->the_url = $_SERVER['PHP_SELF'] . '?' . $this->url_ending;
		
		// Cut off last '?' if no variables.
		if ( empty( $this->url_ending ) ) $this->the_url = substr( $this->the_url , 0, -1 );
		
		// Reset values for future overwriting.
		$this->url_ending = NULL;
		$this->url_parts_temp = NULL;
		
		return $this->the_url;
		
	}
	
}

?>