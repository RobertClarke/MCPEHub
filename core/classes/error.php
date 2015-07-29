<?php

/**
 * Error Class
 *
 * An object containing an error message and all necessary
 * options & values. Combined with the ErrorContainer class,
 * allows for errors to be easily created & displayed.
**/

class Error {

	public $id = '';
	public $text = '';
	public $type = '';

	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $id Unique error identifier
	 * @param string $text Error message text
	 * @param string $type Type of error message
	**/
	public function __construct( $id, $text, $type='error' ) {

		$this->id = $id;
		$this->text = $text;

		// Check if valid type
		if ( in_array($type, ['error', 'warning', 'success', 'info']) )
			$this->type = $type;
		else
			$this->type = 'error';

	}

}

/**
 * Error Container
 *
 * This class acts as a wrapper for any errors displayed
 * around the website. It allows to display & combine
 * error messages for simple & easy display.
**/

class ErrorContainer {

	private $errors = [];

	private $selected;
	private $type;
	private $forced = false;

	/**
	 * Creates and adds an error to the container
	 *
	 * Note: use add_object() to add an Error object if it already exists.
	 *
	 * @since 3.0.0
	 *
	 * @param string $id The unique idenitifier for the error message
	 * @param string $text The message to display for the error
	 * @param string $type The type of error (error|warning|success|info)
	**/
	public function add( $id, $text, $type='error' ) {
		$this->errors[$id] = new Error($id, $text, $type);
		return $this;
	}

	/**
	 * Adds an Error object to the container
	 *
	 * Note: use add() to add to the container if an Error object doesn't already exist.
	 *
	 * @since 3.0.0
	 *
	 * @param Error $error The error object to add to the container
	**/
	public function add_object( $error ) {
		if ( is_error($error) )
			$this->errors[$error->id] = $error;

		return $this;
	}

	/**
	 * Sets the message to display in the container
	 *
	 * Note: running set() after running an append() call will change the selected
	 * value from an array to a string, effectively forgetting all other errors.
	 *
	 * @since 3.0.0
	 *
	 * @param string $id The unique identifier of the error to display (optional)
	 * @return boolean Returns false if error couldn't be set
	**/
	public function set( $id='' ) {

		if ( $this->forced )
			return false;

		// If no $id given, assume setting the last added error.
		if ( empty($id) && !empty($this->errors) ) {
			$this->selected = end($this->errors)->id;
			return $this;
		}

		// Check if error id exists.
		else if ( array_key_exists( $id, $this->errors ) ) {
			$this->selected = $id;
			return $this;
		}

		else return false;

	}

	/**
	 * Appends an error message to the error container
	 *
	 * Note: if no errors exist in the container, it will simply set the first
	 * error to the container, just like set() does.
	 *
	 * @since 3.0.0
	 *
	 * @param string $id The unique identifier of the error to display
	 * @return boolean Returns false if error couldn't be set
	**/
	public function append( $id ) {

		if ( $this->forced )
			return false;

		// Check if error id exists.
		if ( array_key_exists( $id, $this->errors ) ) {

			// If no errors set yet, set the first error.
			if ( empty( $this->selected ) )
				$this->set($id);

			// Error is already set, append onto the error list.
			else {

				// Convert non-array variable into an array, if needed.
				if ( !is_array( $this->selected ) )
					$this->selected = [$this->selected];

				// Append only if current error isn't the same as appended.
				if ( !in_array( $id, $this->selected ) )
					array_push( $this->selected, $id );

			}

			return $this;

		}
		else return false;

	}

	/**
	 * Forces the container to display a specific error
	 *
	 * This will prevent any other errors from displaying, until the unforce() function
	 * is run to reset the container's forced state.
	 *
	 * @since 3.0.0
	 *
	 * @param string $id The unique identifier of the error to display (optional)
	 * @return boolean Returns false if error couldn't be set
	**/
	public function force( $id='' ) {

		// If no $id given, assume setting the last added error.
		if ( empty($id) && !empty($this->errors) ) {
			$this->forced = true;
			$this->selected = end($this->errors)->id;
			return $this;
		}

		// Check if error id exists.
		if ( array_key_exists( $id, $this->errors ) ) {
			$this->forced = true;
			$this->selected = $id;
			return $this;
		}
		else return false;

	}

	/**
	 * Resets the forced state of the container
	 *
	 * @since 3.0.0
	**/
	public function unforce() {
		$this->forced = false;
		return $this;
	}

	/**
	 * Displays error messages within the container
	 *
	 * Note: if there is more than one error, an HTML list will be created
	 * for all error messages.
	 *
	 * @since 3.0.0
	**/
	public function display() {

		if ( empty($this->selected) )
			return false;

		// Single error display
		if ( !is_array($this->selected) ) {

			$error = $this->errors[$this->selected];
			echo '<div class="alert '.$error->type.'">'.$error->text.'</div>';

		}

		// Multiple error display (array format)
		else {

			// Populate HTML list of errors.
			$list = '';
			foreach ( $this->selected as $e ) {
				$list .= '<li>'.$this->errors[$e]->text.'</li>';
			}

			// Last error added defines the error type.
			$type = $this->errors[ end($this->selected) ]->type;

			echo '<div class="alert '.$type.'"><ul>'.$list.'</ul></div>';

		}

	}

	/**
	 * Returns whether or not error(s) are selected in the container
	 *
	 * @since 3.0.0
	**/
	public function exist() {
		return ( !empty($this->selected) ? true : false );
	}

	/**
	 * Resets the containers forced & selected values
	 *
	 * Note: use clear() to reset the container AND clear all errors.
	 *
	 * @since 3.0.0
	**/
	public function reset() {
		$this->forced = false;
		$this->selected = null;
		return $this;
	}

	/**
	 * Resets the containers forced & selected values and clears all errors
	 *
	 * @since 3.0.0
	**/
	public function clear() {
		$this->errors = [];
		$this->reset();
		return $this;
	}

}

/**
 * Error object check
 *
 * Checks whether or not the given $check value is an instance of Error.
 *
 * @since 3.0.0
 *
 * @param Object $string The object to check the instance of
 * @return boolean True if is Error object, false otherwise
**/
function is_error( $check ) {
	return ( $check instanceof Error );
}