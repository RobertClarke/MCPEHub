<?php

/**
  
  * Form Class
  *
  * Functions for building forms and any form-related functions
  * are located here.
  *
  * post_val();		Echoes safe $_POST HTML input value.
  * get_val();		Echoes safe $_GET HTML input value.
  
**/

class Form {
	
	// Echoes safe $_POST HTML input value.
	public function post_val($input) {
		if ( isset( $_POST[$input] ) ) echo htmlspecialchars($_POST[$input]);
		else return FALSE;
	}
	
	// Echoes safe $_GET HTML input value.
	public function get_val($input) {
		if ( isset( $_GET[$input] ) ) echo htmlspecialchars($_GET[$input]);
		else return FALSE;
	}
	
	public function alphanum($input) {
		if ( preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $input) ) return TRUE;
		else return FALSE;
	}
	
	public function length($input, $max, $min=0) {
		
		if ( $min < 0 ) $min = 0;
		
		if ( $min != 0 ) {
			if ( strlen($input)<$min || strlen($input)>$max ) return FALSE;
		} else {
			if ( strlen($input)>$max ) return FALSE;
		}
		
		return TRUE;
		
	}
	
	public function build_input($options, $force=NULL) {
		
		// If $options is empty, return false.
		if ( empty( $options ) ) return FALSE;
		
		// Force all values in $force array given.
		if ( !empty( $force ) && is_array( $force ) ) {
			foreach ( $force as $key => $value ) {
				$options[$key] = $value;
			}
		}
		
		if ( !isset( $options['type'] ) ) $options['type'] = 'text';
		
		// Allow only specific types of inputs to be created.
		$allowed_types = array( 'text', 'textarea', 'select' );
		if ( !in_array( $options['type'], $allowed_types ) ) return FALSE;
		
		// If $options isn't an array, assume $options is a string for
		// the name of the input (text/textarea only).
		if ( !is_array( $options ) && $options['type'] == 'text' || $options['type'] == 'textarea' ) $options = array( 'name' => $options );
		
		// If name is missing in $options, return false.
		if ( !isset( $options['name'] ) ) return FALSE;
		
		// Set id as name, if missing from $options.
		if ( !isset( $options['id'] ) ) $options['id'] = $options['name'];
		
		$i_class 	= isset( $options['class'] ) 		? ' '.$options['class'] : '';
		$i_value 	= isset( $options['value'] ) 		? $options['value'] : '';
		$i_holder 	= isset( $options['placeholder'] ) 	? $options['placeholder'] : '';
		
		$i_label 	= isset( $options['label'] ) 		? '<div class="label"><label for="'.$options['id'].'">'.$options['label'].'</label></div>' : '';
		$i_helper 	= isset( $options['helper'] ) 		? '<div class="form_helper">'.$options['helper'].'</div>' : '';
		
		$c_class 	= isset( $options['class_cont'] ) 	? ' '.$options['class_cont'] : '';
		
		$output = '<div class="group'.$c_class.'">';
		
		// Add label, if exists.
		$output .= $i_label;
		
		switch ( $options['type'] ) {
			
			case 'text':
				
				$i_auto 	= isset( $options['autocomplete'] ) ? 'autocomplete="off" ' : '';
				$i_spell 	= isset( $options['spellcheck'] ) 	? 'spellcheck="false" ' : '';
				$i_max 		= isset( $options['length'] ) 		? 'maxlength="'.$options['length'].'" ' : '';
				
				// Build input.
				$output .= '<input type="text" ';
				
				$output .= 'name="'.$options['name'].'" ';
				$output .= 'id="'.$options['id'].'" ';
				$output .= 'class="full'.$i_class.'" ';
				$output .= 'value="'. htmlspecialchars($i_value).'" ';
				$output .= 'placeholder="'.$i_holder.'" ';
				$output .= $i_max.$i_auto.$i_spell;
				
				$output .= '/>';
				// End input.
				
				// Add helper class, if exists.
				$output .= $i_helper;
				
			break;
			
			case 'select':
				
				// If no options provided or options aren't an array, return false.
				if ( !is_array( $options['options'] ) || !isset( $options['options'] ) ) return FALSE;
				
				// If multiple, set a variable, $multi.
				$multi = isset( $options['multi'] ) ? 'multiple ' : '';
				
				$output .= '<select ';
				$output .= 'name="'.$options['name'].'[]" ';
				$output .= 'id="'.$options['id'].'" ';
				$output .= 'class="chosen full'.$i_class.'" ';
				$output .= 'data-placeholder="'.$i_holder.'" ';
				$output .= $multi;
				$output .= '>';
				
				// Check if options array is associative.
				$assocArr = array_keys($options['options']) !== range(0, count($options['options']) - 1) ? TRUE : FALSE;
				
				// Don't bother checking for selected if there are none.
				if ( isset( $options['selected'] ) ) {
					
					// If selected value is a string, convert it to an array to prevent errors.
					if ( !is_array( $options['selected'] ) ) $options['selected'] = array( $options['selected'] );
					
					// If not multi select, set first value of selected array as selected.
					else if ( empty( $multi ) ) $options['selected'] = array( reset( $options['selected'] ) );
					
					$sel = TRUE;
				}
				
				if ( $assocArr ) { // Associative (values & display).
					
					foreach( $options['options'] as $option => $value ) {
						$selected = ( isset( $sel ) && in_array( $option, $options['selected'] ) ) ? ' selected' : '';
						$output .= '<option value="'.$option.'"'.$selected.'>'.$value.'</option>';
					}
					
				} else { // Sequential (values = display).
					
					foreach( $options['options'] as $option ) {
						$selected = ( isset( $sel ) && in_array( $option, $options['selected'] ) ) ? ' selected' : '';
						$output .= '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
					}
					
				}
				
				$output .= '</select>';
				
			break;
			
		}
		
		$output .= "</div>\n\n";
		
		echo $output;
		
	}
	
	public function show_inputs($inputs, $options) {
		
		$counter = 0;
		foreach( $inputs as $input ) {
			
			$last = ( ($counter+1)%2 != 1 ) ? ' last' : NULL;
			
			echo '<div class="half'.$last.'">';
			$this->build_input($options[$input]);
			echo '</div>';
			
			$counter++;
			
		}
		
	}
	
	public function clean_inputs($options, $f, $submitted) {
		
		$return = [];
		
		foreach( $options as $input ) {
			
			$input['clean_val'] = ( isset($input['value']) ) ? $input['value'] : NULL;
			
			if ( $input['type'] == 'text' ) {
				
				if ( isset($input['maxlength']) )
					$input['clean_val'] = substr($input['clean_val'], 0, $input['maxlength']);
				
				if ( !isset($input['html_allowed']) )
					$input['clean_val'] = strip_tags($input['clean_val']);
				
				$return[$input['name']] = $input;
				
			}
			elseif ( $input['type'] == 'select' ) {
				
				$f_input = ( !empty($f[$input['name']]) ) ? $f[$input['name']] : [];
				
				$input['allowed_options'] = [];
				
				// Check if allowed options are an associative array.
				$aArray = array_keys($input['options']) !== range(0, count($input['options'])-1) ? TRUE : FALSE;
				
				// Grab all possible allowed options.
				if ( $aArray ) foreach( $input['options'] as $option => $value ) $input['allowed_options'][] = $option;
				else
					$input['allowed_options'] = $input['options'];
				
				// Check if each input submitted is valid.
				foreach( $f_input as $val ) {
					if ( in_array($val, $input['allowed_options']) )
						$input['clean_val'] .= $val.',';
				}
				
				$input['clean_val'] = trim($input['clean_val'], ',');
				
				$return[$input['name']] = $input;
				
			}
			
		}
		
		return $return;
		
	}
	
	public function validate_inputs($options, $f, $submitted) {
		
		$clean = $this->clean_inputs($options, $f, $submitted); 
		
		$missing = NULL;
		
		// Check if any required inputs are missing.
		foreach( $clean as $input ) {
			
			if ( isset($input['required']) && empty($input['clean_val']) )
				$missing .= $input['friendly_name'].', ';
			
		}
		
		if ( !empty($missing) ) $missing = trim($missing, ', ');
		
		$return = [
			'inputs'	=> $clean,
			'missing'	=> ( empty($missing) ) ? NULL : $missing
		];
		
		return $return;
		
	}
	
}

?>