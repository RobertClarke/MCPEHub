<?php

require_once( 'core.php' );
show_header( 'Edit Profile', TRUE );

$post_inputs = $form_input = $form_rules = array();

$post_inputs = array( 'name', 'bio', 'twitter', 'youtube', 'devices' );

foreach( $post_inputs as $input ) {
	$form_inputs[$input] = TRUE;
	$form_input[$input] = isset( $_POST[$input] ) ? $_POST[$input] : '';
}

if ( !isset( $form_input['bio'] ) ) $form_input['bio'] = '';
if ( !isset( $form_input['name'] ) ) $form_input['name'] = '';
if ( !isset( $form_input['twitter'] ) ) $form_input['twitter'] = '';
if ( !isset( $form_input['youtube'] ) ) $form_input['youtube'] = '';
if ( !isset( $form_input['devices'] ) ) $form_input['devices'] = '';

if ( empty( $form_input['bio'] ) ) $form_input['bio'] = $user->info('bio');
if ( empty( $form_input['name'] ) ) $form_input['name'] = $user->info('name');
if ( empty( $form_input['twitter'] ) ) $form_input['twitter'] = $user->info('twitter');
if ( empty( $form_input['youtube'] ) ) $form_input['youtube'] = $user->info('youtube');
if ( empty( $form_input['devices'] ) ) $form_input['devices'] = explode( ',', $user->info('devices') );

// Declare all form inputs. Some important values also used in submission code.
$the_inputs = array(
	
	'name' => array(
		'type'			=> 'text',
		'name'			=> 'name',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-male fa-fw"></i> Display Name',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 50,
		'helper'		=> 'This will appear on your profile instead of your username.',
		
		'friendly_name' => 'Display Name'
	),
	
	'twitter' => array(
		'type'			=> 'text',
		'name'			=> 'twitter',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-twitter fa-fw"></i> Twitter Username',
		'placeholder'	=> '@example',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 50,
		
		'friendly_name' => 'Twitter Username',
		'db_id'			=> 'twitter'
	),
	
	'youtube' => array(
		'type'			=> 'text',
		'name'			=> 'youtube',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-youtube fa-fw"></i> YouTube Username',
		'placeholder'	=> '@example',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 50,
		
		'friendly_name' => 'YouTube Username',
		'db_id'			=> 'youtube'
	),
	
	'bio' => array(
		'type'			=> 'textarea',
		'name'			=> 'bio',
		///'class_cont'	=> '',
		//'label'			=> '',
		//'placeholder'	=> '',
		//'autocomplete'	=> TRUE,
		//'spellcheck'	=> TRUE,
		//'maxlength'		=> 50,
		
		'friendly_name' => 'Profile Bio',
		'db_id'			=> 'bio'
	),
	
	'devices' => array(
		'type'			=> 'select',
		'multi'			=> TRUE,
		'name'			=> 'devices',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-mobile fa-fw"></i> Play MCPE On',
		'placeholder'	=> 'Click to select devices',
		'selected'		=> $form_input['devices'],
		'options'		=> array( 'Android', 'iOS' ),
		
		'friendly_name' => 'Devices'
	),
	
);

// If submit form is submitted.
if ( !empty( $_POST ) ) {
	
	$error->reset();
	
	// Clean up bio HTML using HTMLPurifier.
	require( 'core/htmlpurifier/HTMLPurifier.standalone.php' );
	$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
	
	$form_input['bio'] = $purifier->purify( $form_input['bio'] );
	
	$inputs = $required = array();
	
	// Loop through every additional input and process.
	foreach( $post_inputs as $post_input ) {
		
		// Set current input info var.
		$input = $the_inputs[$post_input];
		
		// Grab value of input from form, in var for clean access.
		$input_val = $form_input[ $input['name'] ];
		
		// Use db_id, if exists.
		if ( !isset( $input['db_id'] ) ) $input['db_id'] = $post_input;
		
		// Push input to $inputs array to insert into processing.
		$inputs[ $input['db_id'] ] = '';
		
		
		// If input is a select, we must validate every option.
		if ( $input['type'] == 'select' && isset( $input['options'] ) && is_array( $input['options'] ) ) {
			
			$input['clean_val'] = '';
			$allowed_options = array();
			
			$assocArr = array_keys($input['options']) !== range(0, count($input['options']) - 1) ? TRUE : FALSE;
			
			// Grab all possible options for input.
			if ( $assocArr ) foreach( $input['options'] as $option => $value ) $allowed_options[] = $option;
			else $allowed_options = $input['options'];
			
			// Check if input values match possiblities. Separate values using commas (value,value,value).
			foreach( $input_val as $option ) if ( in_array( $option, $allowed_options ) ) $input['clean_val'] .= $option.',';
			
			// Strip last comma from list, push to inputs.
			$input['clean_val'] = trim( $input['clean_val'], ',' );
			
			// If empty value (no validated), set default if it isn't multi-select.
			if ( !isset( $input['multi'] ) && empty( $input['clean_val'] ) ) $input['clean_val'] = $allowed_options[0];
			
			$inputs[ $input['db_id'] ] = $input['clean_val'];
			
		}
		
		// If input is text, clean the text and push to inputs array.
		else if ( $input['type'] == 'text' ) {
			
			// Handle max length + strip tags.
			if ( isset( $input['maxlength'] ) ) $input['clean_val'] = substr( $input_val, 0, $input['maxlength'] );
			else $input['clean_val'] = $input_val;
			
			$inputs[ $input['db_id'] ] = strip_tags( $input['clean_val'] );
			
		}
		
		else if ( $input['type'] == 'textarea' ) {
			
			$input['clean_val'] = $input_val;
			$inputs[ $input['db_id'] ] = $purifier->purify( $input['clean_val'] );
			
		}
		
		if ( isset( $input['required'] ) ) $required[] = $input['db_id'];
		
	}
	
	
	// Check if any required inputs are missing.
	$inputs_missing = '';
	foreach ( $inputs as $input => $value ) {
		if ( in_array( $input, $required ) && empty( $value ) ) $inputs_missing .= $the_inputs[$input]['friendly_name'].', ';
	}
	$inputs_missing = trim( $inputs_missing, ', ' );
	
	// Show an error for missing inputs.
	if ( !empty( $inputs_missing ) ) {
		$error->add( 'INPUT_MISSING', 'The following inputs must be filled out: '.$inputs_missing.'.', 'error', 'times' );
		$error->append( 'INPUT_MISSING' );
	}
	
	
	// If we have no errors in the form, lets continue.
	if ( empty( $error->selected ) ) {
			
		$update_vals = array();
		
		if ( $inputs['name'] != $user->info('name') ) $update_vals['name'] = $inputs['name'];
		if ( $inputs['bio'] != $user->info('bio') ) $update_vals['bio'] = $inputs['bio'];
		if ( $inputs['twitter'] != $user->info('twitter') ) $update_vals['twitter'] = $inputs['twitter'];
		if ( $inputs['youtube'] != $user->info('youtube') ) $update_vals['youtube'] = $inputs['youtube'];
		if ( $inputs['devices'] != $user->info('devices') ) $update_vals['devices'] = $inputs['devices'];
		
		if ( !empty( $update_vals ) )
			$db->where( array( 'id' => $user->info('id') ) )->update( 'users', $update_vals );
		
		$error->add( 'SUCCESS', 'Profile changes have been saved! Check our your <a href="/profile">new profile</a>.', 'success', 'check' );
		$error->set( 'SUCCESS' );
		
	}
	
}

?>

<div id="page-title">
    <h1>Edit Profile</h1>
    <div class="links">
        <a href="/profile" class="bttn"><i class="fa fa-arrow-left"></i> Back to Profile</a>
    </div>
</div>

<?php $error->display(); ?>

<form action="/edit_profile" method="POST" class="form submission">
    
    <div class="inputs clearfix">
    
        <div class="input half"><div class="label"><label for="name"><i class="fa fa-male fa-fw"></i> Display Name</label></div><input type="text" name="name" id="name" class="text" value="<?php echo htmlspecialchars( $form_input['name'] ); ?>" maxlength="50" /><div class="form_helper">This will appear on your profile instead of your username.</div></div>
        
        <div class="input half last"><div class="label"><label for="devices"><i class="fa fa-mobile fa-fw"></i> Play MCPE On</label></div><select name="devices[]" id="devices" class="chosen" data-placeholder="Click to select devices" multiple ><option value="iOS"<?php if ( in_array( 'iOS', $form_input['devices'] ) ) echo ' selected'; ?>>iOS</option><option value="Android"<?php if ( in_array( 'Android', $form_input['devices'] ) ) echo ' selected'; ?>>Android</option></select></div>
        
    </div>
    
    <div class="main-inputs">
        <div class="label"><label for="bio">Profile Bio &nbsp;<span>Will appear on your profile as your personal bio.</span></label></div>
        <textarea name="bio" id="bio" class="visual"><?php echo $form_input['bio']; ?></textarea>
    </div>
    
    <div class="inputs clearfix">
    
        <div class="input half"><div class="label"><label for="twitter"><i class="fa fa-twitter fa-fw"></i> Twitter Username</label></div><input type="text" name="twitter" id="twitter" class="text" value="<?php echo htmlspecialchars( $form_input['twitter'] ); ?>" placeholder="@example_user" autocomplete="off" spellcheck="false" maxlength="50" /></div>
        
        <div class="input half last"><div class="label"><label for="youtube"><i class="fa fa-youtube-play fa-fw"></i> YouTube Username</label></div><input type="text" name="youtube" id="youtube" class="text" value="<?php echo htmlspecialchars( $form_input['youtube'] ); ?>" placeholder="example_user" autocomplete="off" spellcheck="false" maxlength="50" /></div>
        
    </div>
    
    <div class="buttons-cont">
        <button type="submit" id="submit" class="bttn green large"><i class="fa fa-check"></i> Save Changes</button>
    </div>
    
</form>

<script type="text/javascript" src="/assets/js/tinymce/tinymce.min.js"></script>
    
<script type="text/javascript">
tinymce.init({
	selector: "textarea.visual",
	height: "150px",
	theme: "modern",
	skin: "light",
	plugins: ["link smileys paste"],
	toolbar: "bold underline italic strikethrough | smileys | alignleft aligncenter alignright | bullist numlist | link unlink | undo redo",
	statusbar: false,
	menubar: false,
	paste_as_text: true,
	object_resizing : false
});

</script>

<?php show_footer(); ?>