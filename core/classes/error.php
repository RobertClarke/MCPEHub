<?php

/**
  
  * Error Class
  *
  * Allows for easy management of error messages.
  *
  * add();			Creates an error.
  * display();		Displays error message(s).
  * set();			Sets an error to display.
  * append();		Appends an error to the errors list.
  * force();		Forces a specific error.
  * unforce();		Unlocks the error class, if locked.
  * reset();		Resets selected & forced variables.
  * clear();		Same as reset(); except clears $errors array.
  * isset();		Returns boolean if an error is set or not.
  
**/

class Error {
	
	private $errors = [];
	private $selected;
	private $forced = FALSE;
	
	// Creating an error.
	public function add($id, $msg, $type='error') {
		
		// Make sure type is valid.
		$type = ( in_array( $type, array('error','warning','success','info') ) ) ? $type : 'error';
		
		$error = array(
			'id'	=> $id,
			'msg'	=> $msg,
			'type'	=> $type
		);
		
		// Push out error to array.
		$this->errors[$id] = $error;
		
		return;
		
	}
	
	// Display error messages.
	public function display() {
		
		// Show messages only if there's at least one message to show.
		if ( empty( $this->selected ) ) return FALSE;
		
		// For single error display:
		if ( !is_array( $this->selected ) ) {
			
			$e = $this->errors[$this->selected];
			
			echo '<div class="alert '.$e['type'].'">';
			echo $e['msg'];
			echo '</div>';
			
		}
		
		// For multiple error display:
		else {
			
			echo '<div class="alert '.$this->errors[end($this->selected)]['type'].'"><ul class="list">';
			
			foreach ( $this->selected as $error ) {
				
				$e = $this->errors[$error];
				
				echo '<li>'.$e['msg'].'</li>';
				
			}
			
			echo '</ul></div>';
			
		}
		
		return;
		
	}
	
	// Set the error to display.
	public function set($id) {
		
		// Prevent setting an error if one is already forced.
		if ( $this->forced ) return FALSE;
		
		// Check if error exists.
		if ( array_key_exists( $id, $this->errors ) ) {
			
			// Set the error, overwrites old errors.
			$this->selected = $id;
			return;
			
		} else return FALSE;
		
	}
	
	// Appends an error (useful for displaying multiple messages at once).
	public function append($id) {
		
		// Prevent adding an error if one is already forced.
		if ( $this->forced ) return FALSE;
		
		// Check if error exists.
		if ( array_key_exists( $id, $this->errors ) ) {
			
			// Only append if an error is already set.
			if ( !empty( $this->selected ) ) {
				
				if ( !is_array( $this->selected ) ) $this->selected = [$this->selected];
				
				// Only append if current error isn't the same.
				if ( !in_array( $id, $this->selected ) ) {
					
					array_push( $this->selected, $id );
					
				}
				
			}
			
			// No errors set, just set this as first error.
			else $this->set($id);
			
		} else return FALSE;
		
	}
	
	// Forces an error. All other set()'s will be ignored.
	public function force($id) {
		
		// Check if error exists.
		if ( array_key_exists( $id, $this->errors ) ) {
			
			// Set and force the error, overwrites old errors.
			$this->selected = $id;
			$this->forced = TRUE;
			return;
			
		} else return FALSE;
		
	}
	
	// Unforces errors. This unlocks the error class, if locked.
	public function unforce() {
		$this->forced = FALSE;
		return;
	}
	
	// Resets all set variables. Does NOT delete errors.
	public function reset() {
		$this->forced = FALSE;
		$this->selected = NULL;
		return;
	}
	
	// Clears the errors class and resets the variables.
	public function clear() {
		$this->errors = [];
		$this->reset();
		return;
	}
	
	// Returns boolean if an error is set or not.
	public function exists() {
		if ( empty($this->selected) ) return FALSE;
		return TRUE;
	}
	
}

?>