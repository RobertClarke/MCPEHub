<?php

// TODO: Batch add errors.
// TODO: Error report if error set but no output defined.

class Error {
	
	public $errors = array();
	public $selected;
	private $forced;
	
	function __construct() {
		
	}
	
	function add( $id, $message, $type = 'error', $icon = '' ) {
		
		// Make sure $type is an allowed value.
		if ( !in_array( $type, array( 'error', 'warning', 'success', 'info' ) ) ) $type = 'error';
		
		$error = array(
			'id'		=>	$id,
			'message'	=>	$message,
			'type'		=>	$type,
			'icon'		=>	( !empty( $icon ) ) ? "<i class='fa fa-".$icon." fa-fw'></i> " : ''
		);
		
		// Manually set in case we have to overwrite an error.
		$this->errors[$id] = $error;
		return;
		
	}
	
	function remove() {
		
		// TODO: Remove or code, not sure if it's necessary.
		
	}
	
	// Forces an error. All other errors will be ignored throughout.
	// If you run one force(), you must run a second one to overwrite.
	function force( $id ) {
		
		// Set only if error identifier exists.
		if ( array_key_exists( $id, $this->errors ) ) {
			
			// Sets the error. This will overwrite any array.
			$this->selected = $id;
			
			$this->forced = TRUE;
			
			return;
			
		} else return FALSE;
		
	}
	
	// Unforces errors. This unlocks the errors so you can add more.
	function unforce() {
		
		$this->forced = FALSE;
		return;
		
	}
	
	// Clears all errors and unforces errors. Resets the whole error system.
	function reset() {
		
		$this->forced = FALSE;
		$this->selected = NULL;
		return;
		
	}
	
	// Sets an error. Overwrites all other error(s).
	function set( $id ) {
		
		// Prevent adding an error if one is already forced.
		if ( $this->forced ) return FALSE;
		
		// Set only if error identifier exists.
		if ( array_key_exists( $id, $this->errors ) ) {
			
			// Sets the error. This will overwrite any array.
			$this->selected = $id;
			return;
			
		} else return FALSE;
		
	}
	
	function append( $id ) {
		
		// Prevent adding an error if one is already forced.
		if ( $this->forced ) return FALSE;
		
		// Set only if error identifier exists.
		if ( array_key_exists( $id, $this->errors ) ) {
			
			// Only append if an error is already in place.
			if ( !empty( $this->selected ) ) {
				
				// Set an array to verify.
				if ( !is_array( $this->selected ) ) $this->selected = array( $this->selected );
				
				// Only append if the error isn't already set.
				if ( !in_array( $id, $this->selected ) ) {
					
					// Set the variable to an array if it already isn't.
					if ( !is_array( $this->selected ) )
						$this->selected = array( $this->selected );
					
					// Add the error to the selected variable.
					array_push( $this->selected, $id );
					
				}
				
			}
			
			// Mo errors in place, just set the first error instead.
			else $this->set( $id );
			
		} else return FALSE;
		
	}
	
	function display() {
		
		// Show messages only if there's at least one message selected.
		if ( !empty( $this->selected ) ) {
			
			// If there's a single error.
			if ( !is_array( $this->selected ) ) {
				
				// Set current error in variable for ease of use.
				$error = $this->errors[$this->selected];
				
				// If there's an icon, add a container around the text, for indent.
				$error['message'] = '<div class="indent">'.$error['message'].'</div>';
				
				echo "<div class='message {$error[ 'type' ]}'>{$error[ 'icon' ]}{$error[ 'message' ]}</div>";
				return;
				
			}
			
			else { // If there's multiple errors.
				
				echo "<div class='message " . $this->errors[ end( $this->selected ) ]['type'] . "'><span class=\"title\">The following errors occurred:</span><ul class=\"list\">";
				
				foreach ( $this->selected as $err ) {
					
					// Set current error in variable for ease of use.
					$error = $this->errors[$err];
					
					// No error icons if it's a list.
					echo "<li>{$error[ 'message' ]}</li>";
					
				}
				
				echo "</ul></div>";
				
				return;
				
			}
			
		} else return FALSE;
		
	}
	
}

?>