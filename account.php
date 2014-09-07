<?php

require_once( 'core.php' );
show_header( 'Account', TRUE );

$post_inputs = $form_input = $form_rules = array();

$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

// Made this into a function just so we can use it in a clean way below.
function show_extra_inputs() {
	
	global $post_inputs, $the_inputs;
	
	require( ABSPATH . 'core/classes/form.php' );
	$form = new Form;
	
	// Push out required inputs for use in form.
	$loop_count = 0;
	foreach( $post_inputs as $input ) {
		
		// Open container div, as needed.
		if ( ( $loop_count + 1 ) % 2 == 1 )
			echo "<div class=\"inputs clearfix\">\n\n";
		
		// For every 2nd input, add the class "last" for proper spacing.
		$force = ( ( $loop_count + 1 ) % 2 != 1 ) ? array('class_cont' => 'half last') : null;
		
		$form->build_input( $the_inputs[$input], $force );
		
		// Close container div, as needed.
		if ( ( $loop_count + 1 ) % 2 != 1 || ( $loop_count + 1 ) == count( $post_inputs ) )
			echo "</div>\n";
		
		$loop_count++;
		
	}

}

// Tab navigation.
$tabs = array(
	1 => array( 'General Settings',	 '/account',			 'gears',	 '' ),
	2 => array( 'Change Avatar',	 '/account?tab=avatar',	 'camera',	 'avatar' ),
	//3 => array( 'Email Preferences', '/account?tab=email',	 'envelope', 'email' ),
);

$tab_nav = '';
foreach( $tabs as $the_tab ) {
	$active = ( $the_tab[3] == $tab ) ? ' active' : '';
	$tab_nav .= '<a href="'.$the_tab[1].'" class="bttn'.$active.'"><i class="fa fa-'.$the_tab[2].'"></i> '.$the_tab[0].'</a>';
}


switch ( $tab ) { // START: Tabs switch.

default: // Editing general settings.

if ( $user->info('activated') == 0 ) {
	$error->add( 'NOT_ACTIVATED', '<b>Your account hasn\'t been activated yet!</b><br />We sent an email to <i>'.$user->info('email').'</i> when you signed up with the activation link.<br /><br />&raquo; <a href="/account-resend">Resend activation email</a>', 'warning', 'exclamation-triangle' );
	$error->set( 'NOT_ACTIVATED' );
}

$post_inputs = array( 'current_pw', 'email', 'new_pw', 'repeat_pw' );

foreach( $post_inputs as $input ) {
	$form_inputs[$input] = TRUE;
	$form_input[$input] = isset( $_POST[$input] ) ? $_POST[$input] : '';
}

if ( !isset( $form_input['current_pw'] ) ) $form_input['current_pw'] = '';
if ( !isset( $form_input['email'] ) ) $form_input['email'] = '';
if ( !isset( $form_input['new_pw'] ) ) $form_input['new_pw'] = '';
if ( !isset( $form_input['repeat_pw'] ) ) $form_input['repeat_pw'] = '';

if ( empty( $form_input['email'] ) ) $form_input['email'] = $user->info('email');

// Declare all form inputs. Some important values also used in submission code.
$the_inputs = array(
	
	'current_pw' => array(
		'type'			=> 'text',
		'password'		=> TRUE,
		'name'			=> 'current_pw',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-asterisk fa-fw"></i> Current Password <span>(Required)</span>',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 100,
		'helper'		=> 'Required to make any changes to your account.',
		
		'friendly_name' => 'Current Password',
		'required'		=> TRUE
	),
	
	'email' => array(
		'type'			=> 'text',
		'name'			=> 'email',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-envelope fa-fw"></i> Email Address',
		'value'			=> $form_input['email'],
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 100,
		//'helper'		=> 'This is where we\'ll send all your notifications.',
		
		'friendly_name' => 'Email Address'
	),
	
	'new_pw' => array(
		'type'			=> 'text',
		'password'		=> TRUE,
		'name'			=> 'new_pw',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-lock fa-fw"></i> Change Password <span>(Optional)</span>',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 100,
		'helper'		=> 'Leave this blank for no password change.',
		
		'friendly_name' => 'Password'
	),
	
	'repeat_pw' => array(
		'type'			=> 'text',
		'password'		=> TRUE,
		'name'			=> 'repeat_pw',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-lock fa-fw"></i> Repeat Password',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 100,
		//'helper'		=> 'Required if you want to change your password.',
		
		'friendly_name' => 'Repeat Password'
	),
	
);

// If submit form is submitted.
if ( !empty( $_POST ) ) {
	
	$error->reset();
	
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
		
		if ( isset( $input['required'] ) ) $required[] = $input['db_id'];
		
	}
	
	if ( empty( $inputs['current_pw'] ) ) {
		
		$error->add( 'INPUT_MISSING', 'You must fill in your current password in order to make changes to your account.', 'error', 'times' );
		$error->append( 'INPUT_MISSING' );
		
	}
	else {
		
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
		
	}
	
	// If we have no errors in the form, lets continue.
	if ( empty( $error->selected ) ) {
		
		$error->add( 'INVALID_PASS', 'The password you entered doesn\'t match the password of your account.', 'error', 'lock' );
		$error->add( 'NOT_MATCHING_PASS', 'The passwords you entered to perform a password change don\'t match, try again.', 'error', 'times' );
		
		$error->add( 'INVALID_EMAIL', 'The e-mail entered isn\'t in valid email format.', 'error', 'times' );
		
		// Check if current password entered is correct.
		if ( !password_verify( $inputs['current_pw'], $user->info('password') ) ) $error->set( 'INVALID_PASS' );
		else {
			
			// Checking if passwords match (if they want to change them).
			if ( !empty( $inputs['new_pw'] ) ) {
				
				if ( $inputs['new_pw'] == $inputs['repeat_pw'] ) {
					$password_change = TRUE;
					$password_new = password_hash( $inputs['new_pw'], PASSWORD_DEFAULT );
				}
				else $error->append( 'NOT_MATCHING_PASS' );
				
			}
			
			// Check if email is valid format.
			if ( !is_email( $_POST['email'] ) ) $error->append( 'INVALID_EMAIL' );
			
			// If no errors, continue.
			if ( empty( $error->selected ) ) {
				
				$update_vals = array();
				
				if ( isset( $password_change ) ) $update_vals['password'] = $password_new;
				if ( $inputs['email'] != $user->info('email') ) $update_vals['email'] = $inputs['email'];
				
				if ( !empty( $update_vals ) )
					$db->where( array( 'id' => $user->info('id') ) )->update( 'users', $update_vals );
				
				$error->add( 'SUCCESS', 'Account settings have been saved.', 'success', 'check' );
				$error->set( 'SUCCESS' );
				
			}
			
		}
		
	}
	
}

?>

<div id="page-title">
    <h2>Account</h2>
    <ul class="tabs">
        <?php echo $tab_nav; ?>
    </ul>
</div>

<?php $error->display(); ?>

<form action="/account" method="POST" class="form submission" enctype="multipart/form-data">
    
    <?php show_extra_inputs(); // Show extra inputs using function created above. ?>
    
    <div class="buttons-cont">
        <button type="submit" id="submit" class="save"><i class="fa fa-check"></i> Save Changes</button>
    </div>
    
</form>

<?php

break; // END: Editing general settings.

case 'avatar': // START: Editing avatar.

$error->add( 'SUCCESS', 'Your new avatar has been saved!', 'success', 'check' );

if ( isset( $_GET['changed'] ) ) $error->set( 'SUCCESS' );

// If submit form is submitted.
if ( !empty( $_FILES ) ) {
	
	$error->reset();
	
	$error->add( 'IMG_MISSING', 'You didn\'t upload an image to change your avatar.', 'error', 'times' );
	$error->add( 'IMG_MAX', 'Your avatar has exceeded the maximum upload file size.', 'error', 'times' );
	$error->add( 'IMG_INVALID', 'The file provided isn\'t a valid image file.', 'error', 'times' );
	
	// Check if at least 1 image is uploaded.
	$uploaded_images = gather_files( $_FILES['avatar_file'] );
	if ( empty( $_FILES['avatar_file'] ) || count($uploaded_images) == 1 && $uploaded_images[0]['error'] == 4 ) $error->append( 'IMG_MISSING' );
	else {
		
		$i = 0;
		$images_confirmed = FALSE;
		$uploaded_images = array_slice( $uploaded_images, 0, 5 );
		foreach( $uploaded_images as $image ) {
			
			// No image uploaded in input, unset and ignore.
			if ( $image['error'] == 4 ) {
				unset( $uploaded_images[$i] );
			}
			else if ( $image['error'] == 1 ) {
				$error->append( 'IMG_MAX' );
				break;
			}
			else if ( @!getimagesize( $_FILES['avatar_file']['tmp_name'][$i] ) ) {
				$error->append( 'IMG_INVALID' );
				break;
			}
			
			$i++;
			
		}
		
		// Todo: Check for PHP max size again (new PHP standard?)
		
	}
	
	// If we have no errors in the form, lets continue.
	if ( empty( $error->selected ) ) {
		
		// Process uploaded images.
		$images = '';
		$upload_dir = ABSPATH . 'uploads/avatars/';
		
		foreach( $uploaded_images as $i => $image ) {
			
			$f_ext = '.' . strtolower( end( explode( '.', $image['name'] ) ) );
			$f_name = $user->info('username') . '-' . uniqid() . strtolower(random_str(3));
			
			@move_uploaded_file( $_FILES['avatar_file']['tmp_name'][$i], $upload_dir . $f_name . $f_ext );
			
			$images .= $f_name . $f_ext.',';
			
		}
		
		// Delete the old avatar image, only if it's not a default avatar.
		$d_avatars = array( 'default_cow.png', 'default_creeper.png', 'default_pig.png', 'default_skeleton.png', 'default_zombie.png', 'default_zombiepig.png' );
		
		if ( !in_array( $user->info('avatar_file'), $d_avatars ) )
			@unlink( ABSPATH . 'uploads/avatars/' . $user->info('avatar_file') );
		
		$avatar_file = trim( $images, ',' );
		
		$db->where( array( 'id' => $user->info('id') ) )->update( 'users', array( 'avatar_file' => $avatar_file ) );
		
		redirect( 'account?tab=avatar&changed' );
		
	}
	
}

?>

<div id="page-title">
    <h2>Change Avatar</h2>
    <ul class="tabs">
        <?php echo $tab_nav; ?>
    </ul>
</div>

<?php $error->display(); ?>

<form action="/account?tab=avatar" method="POST" class="form submission" enctype="multipart/form-data">
    
    <div class="rules">
        <p>Your avatar will be used on your profile and throughout the site to identify you.</p>
        <ol>
            <li>Recommended avatar size: 256 x 256 pixels.</li>
            <li>Please keep it appropriate or we will suspend your account.</li>
        </ol>
    </div>
    
    <div class="main-inputs uploads clearfix">
        <div id="uploadInputs" class="clearfix">
            <input type="file" name="avatar_file[]" id="image" class="file-upload" />
        </div>
    </div>
    
    <div class="buttons-cont">
        <button type="submit" id="submit" class="save"><i class="fa fa-upload"></i> Upload Avatar</button>
    </div>
    
</form>

<?php

break; // END: Editing avatar.

} // END: Tabs switch.

?>
<?php show_footer(); ?>