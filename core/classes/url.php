<?php

/**
  
  * URL Class
  *
  * Used around the website to quickly build and modify the
  * current page URL. Much more flexible than grabbing server
  * URL using PHP.
  *
  * add();		Adds element into the URL.
  * show();		Returns the (current) URL string.
  * reset();	Clears all URL parts. Resets the whole URL generation system.
  
**/

class Url {
	
	private $url = '';
	private $ending = '';
	private $parts_temp = '';
	private $parts = [];
	
	// Adds element into the URL.
	public function add($key, $val='') {
		
		$val = htmlspecialchars($val);
		
		// Add the key & value into the URL.
		$this->parts[$key] = $val;
		return;
		
	}
	
	// Returns the (current) URL string.
	public function show($add='', $reset=FALSE) {
		
		// Setting $reset will just echo out a raw URL.
		if (!$reset) $this->parts_temp = $this->parts;
		else $this->parts_temp = [];
		
		// If something needs to be added to the URL.
		if ( !empty($add) ) {
			
			$add = explode('&', $add);
			
			foreach ( $add as $the_add ) {
				$the_add = explode('=', $the_add);
				$this->parts_temp[$the_add[0]] = $the_add[1];
			}
			
		}
		
		// Build the URL from the array.
		foreach ( $this->parts_temp as $key => $val ) $this->ending .= $key.'='.$val.'&';
		
		// Cut off last '&' from URL.
		$this->ending = substr( $this->ending , 0, -1 );
		
		$this->url = trim($_SERVER['PHP_SELF'], '.php') . '?' . $this->ending;
		
		// Cut off last '?' if no variables.
		if ( empty($this->ending) ) $this->url = substr($this->url , 0, -1);
		
		// Reset values for future overwriting.
		$this->ending = NULL;
		$this->parts_temp = NULL;
		
		return $this->url;
		
	}
	
	// Clears all URL parts. Resets the whole URL generation system.
	public function reset() {
		
		$this->url = NULL;
		$this->ending = NULL;
		$this->parts = array();
		$this->parts_temp = NULL;
		
		return;
		
	}
	
}

?>