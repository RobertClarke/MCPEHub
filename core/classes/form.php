<?php

class Form {

	// Array holding all the Input objects of this form.
	public static $inputs = [];

	/**
	 * Builds and displays inputs based on the given form and inputs values
	 *
	 * Note: both $form and $inputs must be arrays of objects.
	 *
	 * @since 3.0.0
	 *
	 * @param array $form The form inputs, given in array format
	 * @param array $inputs Inputs to display from the form, given in array format
	 * @return echo Echoes the input values in HTML format
	**/
	public static function show_inputs($form, $inputs) {

		foreach ( $inputs as $input ) {

			// Check if the key exists in the $form array first.
			if ( array_key_exists( $input, $form ) ) {
				self::$inputs[$input] = new Input( $input, $form[$input] );
				self::$inputs[$input]->display(true);
			}
		}
	}

	/**
	 * Given an array of input values, validates if they're filled
	 *
	 * @since 3.0.0
	 *
	 * @param array $inputs Array of inputs to check, format: [$key] => [$value]
	 * @return boolean False if no inputs are empty (check with === false)
	 * @return array Missing inputs, if there are any that are missing
	**/
	public static function check_missing($inputs) {

		$missing = false;

		foreach ( $inputs as $id => $val ) {
			if ( empty($val) ) {

				// Change $missing from boolean to array, if necessary
				if ( !is_array($missing) )
					$missing = [];

				// Add input to missing array
				$missing[] = $id;

			}
		}

		return $missing;

	}

	/**
	 * Cleans any input values given in a Form object
	 *
	 * @since 3.0.0
	 *
	 * @param array $form The form inputs, given in array format
	 * @param array $inputs Inputs to display from the form, given in array format
	 * @return array Clean array of inputs
	**/

	public static function clean_inputs($form, $inputs) {

		$return = $inputs;

		foreach ( $inputs as $input => $val ) {

			// Check if the key exists in the $form array first.
			if ( array_key_exists( $input, $form ) ) {
				self::$inputs[$input] = new Input( $input, $form[$input] );

				switch ( self::$inputs[$input]->type ) {

					// <select> input, check if value selected is valid
					case 'select':

						$assoc = array_keys($form[$input]['options']) !== range(0, count($form[$input]['options']) - 1) ? true : false;

						// If $options array is associative
						if ( $assoc ) {

							// Invalidate any invalid selected options
							if ( !array_key_exists($val, $form[$input]['options']) )
								$return[$input] = null;

						}

						// If $options array is sequential
						else {

							// Invalidate any invalid selected options
							if ( !in_array( $val, $form[$input]['options'] ))
								$return[$input] = null;

						}

					break;

					// Text inputs
					default:

						// Trim down text values to max length
						if ( isset( $form[$input]['maxlength'] ) )
							$return[$input] = substr( $val, 0, $form[$input]['maxlength'] );

					break;

				}
			}
		}

		return $return;

	}

}

class Input {

	// Default options for newly created inputs.
	public $id				= '';
	public $type			= 'text';
	public $label			= '';
	public $placeholder		= '';
	public $maxlength		= 0;
	public $autocomplete	= false;
	public $spellcheck		= false;
	public $options			= [];

	/**
	 * Constructor
	 *
	 * Creates a new input given the values passed in array format. If $options
	 * is given as a string, a text input will be created using that as the id.
	 *
	 * @since 3.0.0
	 *
	 * @param string $id The unique identifier of this input
	 * @param array $options An array of options given to create the input
	**/
	public function __construct( $id, $options = [] ) {

		$this->id = $id;

		if ( !empty($options) ) {

			// Array of fields to fetch into this Input object.
			$fields = ['type', 'label', 'placeholder', 'maxlength', 'autocomplete', 'spellcheck', 'options'];


			foreach ( $fields as $field ) {
				if ( isset($options[$field]) && !empty($options[$field]) )
					$this->$field = $options[$field];
			}

		}

	}

	/**
	 * Builds current Input object in HTML format
	 *
	 * @since 3.0.0
	 *
	 * @return string The Input built in HTML format
	**/
	public function build() {

		// Variable for containing generated HTML for this input
		$result = '<div class="input"><label for="'.$this->id.'">'.$this->label.'</label>';

		switch ( $this->type ) {

			// Select input with Selectize.js
			case 'select':

				// If there are no options for the <select>, return false.
				if ( empty($this->options) || !is_array($this->options) )
					return false;

				$result .= '<select name="'.$this->id.'" id="'.$this->id.'" ';
				$result .= 'class="selectize" ';
				$result .= 'placeholder="'.$this->placeholder.'"';
				$result .= '><option value=""></option>';

				$assoc = array_keys($this->options) !== range(0, count($this->options) - 1) ? true : false;

				// If $options array is associative
				if ( $assoc ) {
					foreach ( $this->options as $option => $val )
						$result .= '<option value="'.$option.'"'.( ( (input_POST($this->id) !== null) && $option == input_POST($this->id) ) ? ' selected' : '' ).'>'.$val.'</option>';
				}

				// If $options array is sequential
				else {
					foreach ( $this->options as $option )
						$result .= '<option value="'.$option.'"'.( ( (input_POST($this->id) !== null) && $option == input_POST($this->id) ) ? ' selected' : '' ).'>'.$option.'</option>';
				}

				$result .= '</select>';

			break;

			// Default input type (text)
			default:

				$result .= '<input type="'.$this->type.'" name="'.$this->id.'" id="'.$this->id.'" ';
				$result .= 'value="'.htmlspecialchars(input_POST($this->id)).'" ';
				$result .= 'placeholder="'.$this->placeholder.'"';
				$result .= ( $this->maxlength > 0 ) ? ' maxlength="'.$this->maxlength.'"' : '';
				$result .= ( $this->autocomplete ) ? ' autocomplete="off"' : '';
				$result .= ( $this->spellcheck ) ? ' spellcheck="false"' : '';
				$result .= ">";

			break;
		}

		return $result . '</div>';

	}

	/**
	 * Outputs the current Input object
	 *
	 * @since 3.0.0
	 *
	 * @param boolean $echo Whether or not to echo the output (or return)
	**/
	public function display( $echo=false ) {

		if ( $echo )
			echo $this->build();
		else
			return $this->build();

	}

}

?>