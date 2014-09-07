<?php

class Form {
	
	function build_input( $options, $force = null ) {
		
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
		
		$output = '<div class="input'.$c_class.'">';
		
		// Add label, if exists.
		$output .= $i_label;
		
		switch ( $options['type'] ) {
			
			case 'text':
				
				$i_auto 	= isset( $options['autocomplete'] ) ? 'autocomplete="off" ' : '';
				$i_spell 	= isset( $options['spellcheck'] ) 	? 'spellcheck="false" ' : '';
				$i_max 		= isset( $options['length'] ) 		? 'maxlength="'.$options['length'].'" ' : '';
				
				$type = isset( $options['password'] ) ? 'password' : 'text';
				
				// Build input.
				$output .= '<input type="'.$type.'" ';
				
				$output .= 'name="'.$options['name'].'" ';
				$output .= 'id="'.$options['id'].'" ';
				$output .= 'class="text'.$i_class.'" ';
				$output .= 'value="'. htmlspecialchars($i_value).'" ';
				$output .= 'placeholder="'.$i_holder.'" ';
				$output .= $i_max.$i_auto.$i_spell;
				
				$output .= '/>';
				// End input.
				
				// Add helper class, if exists.
				$output .= $i_helper;
				
			break;
			
			case 'textarea':
				
				// TODO, whenever this actually comes handy.
				
			break;
			
			case 'select':
				
				// If no options provided or options aren't an array, return false.
				if ( !is_array( $options['options'] ) || !isset( $options['options'] ) ) return FALSE;
				
				// If multiple, set a variable, $multi.
				$multi = isset( $options['multi'] ) ? 'multiple ' : '';
				
				$output .= '<select ';
				$output .= 'name="'.$options['name'].'[]" ';
				$output .= 'id="'.$options['id'].'" ';
				$output .= 'class="chosen-select'.$i_class.'" ';
				$output .= 'data-placeholder="'.$i_holder.'" ';
				$output .= $multi;
				$output .= '>';
				
				//$output .= '<option value=""></option>';
				
				
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
		
		$output .= '</div>';
		$output .= "\n\n";
		
		echo $output;
		
	}
	
}

?>