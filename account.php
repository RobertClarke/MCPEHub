<?php

/**
  * Account Settings
**/

require_once('core.php');

$tabs = [
	'general'	=> [ 'My Account',		$url->show('', TRUE), 'wrench' ],
	'profile_edit'	=> [ 'Edit Profile',	'/profile_edit', 'pencil' ],
	'avatar'	=> [ 'Change Avatar',	$url->show('tab=avatar', TRUE), 'camera' ],
];

// Activation tab for non-activated users.
if ( $user->info('activated') == 0 ) $tabs['resend'] = [ 'Resend Activation', $url->show('tab=resend', TRUE), 'envelope' ];

$tab = ( isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) )  ? $_GET['tab'] : 'general';

show_header($tabs[$tab][0], TRUE, ['title_main' => 'Settings', 'title_sub' => 'My Account']);

// Generate tab navigation HTML.
$html['tabs'] = NULL;
foreach ( $tabs as $id => $t ) {
	$active = ( $id == $tab ) ? ' gold' : '';
	$html['tabs'] .= '<a href="'.$t[1].'" class="bttn mid'.$active.'"><i class="fa fa-'.$t[2].'"></i> '.$t[0].'</a>';
}

// Quick function for showing page title in code.
function show_title() { global $tab, $tabs, $html, $error; ?>
<div id="p-title">
    <h1><?php echo $tabs[$tab][0]; ?></h1>
    <div class="tabs"><?php echo $html['tabs']; ?></div>
</div>

<?php

$error->display();

} // End: show_title() function.

// Tab switch.
switch($tab) {

// General settings.
default:

if ( !empty($_POST) ) {
	
	$error->add('SUCCESS',		'Your new account settings have been saved.', 'success');
	
	$error->add('P_MISSING',	'You must fill in your current password in order to make changes to your account.');
	$error->add('P_INCORRECT',	'The password you entered doesn\'t match your current password.');
	
	$error->add('E_MISSING',	'You must enter an email to be associated to your account.');
	$error->add('E_LENGTH',		'Your new email cannot be longer than 50 characters.');
	$error->add('E_USED',		'Your new email is already in use by another member.');
	$error->add('E_INVALID',	'The new email submitted isn\'t in valid email format.');
	
	$error->add('C_MATCH',		'The passwords submitted don\'t match.');
	$error->add('C_LENGTH',		'Your new password must be 6-30 characters long.');
	$error->add('C_USERNAME',	'Your password can\'t be the same as your username.');
	
	$f['password']		= isset( $_POST['password'] )		? $db->escape($_POST['password']) : NULL;
	$f['email']			= isset( $_POST['email'] )			? $db->escape($_POST['email']) : NULL;
	
	$f['passwordc']		= isset( $_POST['passwordc'] )		? $db->escape($_POST['passwordc']) : NULL;
	$f['passwordc2']	= isset( $_POST['passwordc2'] )		? $_POST['passwordc2'] : NULL;
	
	// Check if current password missing.
	if ( empty($f['password']) ) $error->set('P_MISSING');
	else {
		
		// Check if current password correct.
		if ( !password_verify($f['password'], $user->info('password')) ) $error->set('P_INCORRECT');
		else {
			
			// EMAIL field checks.
			if ( empty($f['email']) ) $error->set('E_MISSING');
			else {
				if		( !$form->length($f['email'], 50) )			$error->append('E_LENGTH');
				elseif	( !is_email($f['email']) )					$error->append('E_INVALID');
				
				elseif	( $user->check_email($f['email']) && $f['email'] != $user->info('email') )	$error->append('E_USED');
			}
			
			// PASSWORD field checks.
			if ( !empty($f['passwordc']) ) {
				if		( !$form->length($f['passwordc'], 30, 6) )		$error->append('C_LENGTH');
				elseif	( $f['passwordc'] == $user->info('username') )	$error->append('C_USERNAME');
				elseif	( $f['passwordc'] != $f['passwordc2'] )			$error->append('C_MATCH');
				else $pw_change_valid = TRUE;
			}
			
			if ( !$error->exists() ) {
				
				// If email change requested.
				if ( isset($f['email']) ) $change['email'] = $f['email'];
				
				// If password change requested.
				if ( isset($pw_change_valid) ) $change['password'] = password_hash($f['passwordc'], PASSWORD_DEFAULT);
				
				if ( isset($change) ) {
					$db->where(['id' => $user->info('id')])->update('users', $change); 
				}
				
				$error->set('SUCCESS');
				
				if ( isset($pw_change_valid) ) redirect('/login?auth&redirect=account');
				
			} // End: No errors found in inputs.
			
		} // End: Check if current password correct.
		
	} // End: Check if current password missing.
	
} // End: Form submitted.

show_title();

?>
<form action="/account" method="POST">
    <div class="half">
        <div class="group">
            <div class="label"><label for="password">Current Password (Required)</label></div>
            <input type="password" name="password" id="password" class="full">
        </div>
        <div class="group">
            <div class="label"><label for="passwordc">Change Password (Optional)</label></div>
            <input type="password" name="passwordc" id="passwordc" class="full">
        </div>
    </div>
    <div class="half last">
        <div class="group">
            <div class="label"><label for="email">Email Address</label></div>
            <input type="text" name="email" id="email" class="full" value="<?php $form->post_val('email'); ?>" spellcheck="false">
        </div>
        <div class="group">
            <div class="label"><label for="passwordc2">Repeat New Password</label></div>
            <input type="password" name="passwordc2" id="passwordc2" class="full">
        </div>
    </div>
    <div class="submit">
        <button type="submit" class="bttn big green"><i class="fa fa-check"></i> Save Changes</button>
    </div>
</form>
<?php

break; // End: General settings.

// Change avatar.
case 'avatar':

$error->add('SUCCESS', 'Your new avatar has been uploaded to the website.', 'success');
if ( isset($_GET['success']) ) $error->set('SUCCESS');

if ( !empty($_FILES) ) {
	
	$error->reset();
	
	$error->add('I_MISSING',	'You didn\'t upload an image to change your avatar.');
	$error->add('I_MAX',		'The avatar uploaded exceeded the maximum upload file size. Please upload a smaller image file.');
	$error->add('I_INVALID',	'The avatar provided isn\'t a valid image file.');
	
	// Check if at least one image uploaded.
	$upload = gather_files($_FILES['avatar']);
	if ( empty( $_FILES['avatar'] ) || count($upload) == 1 && $upload[0]['error'] == 4 ) $error->append('I_MISSING');
	else {
		
		$i = 0;
		$images_confirmed = FALSE;
		$upload = array_slice( $upload, 0, 5 );
		foreach( $upload as $image ) {
			
			// No image uploaded, unset and ignore.
			if ( $image['error'] == 4 ) {
				unset( $upload[$i] );
			}
			elseif ( $image['error'] == 1 ) {
				$error->append('I_MAX');
				break;
			}
			elseif ( @!getimagesize( $_FILES['avatar']['tmp_name'][$i] ) ) {
				$error->append('I_INVALID');
				break;
			}
			
			$i++;
			
		} // End: Uploads foreach loop.
		
	} // End: Check if at least one image uploaded.
	
	if ( !$error->exists() ) {
		
		// Process uploaded images.
		$images = '';
		$upload_dir = ABS . 'uploads/avatars/';
		
		foreach( $upload as $i => $image ) {
			
			$f_ext = '.' . strtolower( end( explode( '.', $image['name'] ) ) );
			$f_name = $user->info('username') . '-' . uniqid() . strtolower(random_str(3));
			
			@move_uploaded_file( $_FILES['avatar']['tmp_name'][$i], $upload_dir . $f_name . $f_ext );
			
			$images .= $f_name . $f_ext.',';
			
		}
		
		$defaults = ['default_cow.png', 'default_creeper.png', 'default_pig.png', 'default_skeleton.png', 'default_zombie.png', 'default_zombiepig.png'];
		
		// Delete the old avatar image, only if it's not a default avatar.
		if ( !in_array( $user->info('avatar'), $defaults ) ) {
			@unlink( ABS.'uploads/avatars/'.$user->info('avatar') );
		}
		
		$avatar = trim($images, ',');
		
		$db->where(['id' => $user->info('id')])->update('users', ['avatar' => $avatar]);
		
		redirect( '/account?tab=avatar&success' );
		
	} // End: No errors found in input.
	
} // End: Form submitted.

show_title();

?>
<form action="/account?tab=avatar" method="POST" enctype="multipart/form-data">
    <div class="input-rules">
        <p>Your avatar will be used on your profile and throughout the site to identify you.</p>
        <ol>
            <li>Recommended avatar size: 256 x 256 pixels.</li>
            <li>Please keep your avatar appropriate or we will <b>suspend</b> your account.</li>
        </ol>
    </div>
    <div class="input-uploads">
        <input type="file" name="avatar[]" id="image" class="file-upload" />
    </div>
    <div class="submit">
        <button type="submit" class="bttn big green"><i class="fa fa-upload"></i> Upload Avatar</button>
    </div>
</form>
<?php

break; // End: Change avatar.

// Resending activation email.
if ( $user->info('activated') != 0 ) break;
case 'resend':

if ( !empty($_POST) ) {
	
	$error->add('SUCCESS',		'We sent <b>'.$user->info('email').'</b> another activation email.<br>The email may take a few minutes to send and make sure to check your SPAM inbox.', 'success');
	$error->add('LIMIT',		'You\'ve reached the maximum amount of email requests in the past 24 hours.');
	
	$today = date('Y-m-d');
	
	$current_user = $user->info('id');
	
	// Grab resets from database. Max 3.
	$resets = $db->select('id')->from('activate_resend')->limit(3)->where('`user` = "'.$current_user.'" AND DATE(`requested`) = "'.$today.'"')->fetch();
	
	// Only allow maximum 3 requests per day per user.
	if ( $db->affected_rows > 2 ) $error->set('LIMIT');
	else {
		
		$insert = ['user' => $user->info('id'), 'email' => $user->info('email'), 'requested' => time_now()];
		$db->insert('activate_resend', $insert);
		
		$token = $user->info('activate_token');
		
		// Send welcome email to user with activation instructions.
		$e_body	 = "<p>You requested that we send you another activation email since the first one didn't make it to you.</p>\n";
		$e_body .= "<p>Activate your account by clicking on <a href=\"".MAINURL."activate?code={$token}\">this link</a>.</p>\n";
		$e_body .= "<p>Once you've activated your account, you'll be able to publish content, comment and participate in our growing community.</p>\n";
		$e_body .= "<p>Thanks for participating in our community!</p><p>- MCPE Hub Team</p>\n";
		$e_body .= "<p><i>Follow us on Twitter: <a href=\"http://twitter.com/MCHubCommunity\">@MCHubCommunity</a> for news, updates, giveaways and more!</i></p>\n";
		$e_body .= "<div class=\"bottom\">If you have issues opening the link above, use the following link: <a href=\"".MAINURL."activate?code={$token}\">".MAINURL."activate?code={$token}</a></div>\n";
		
		$email = $mail->format($e_body);
		$email = $mail->send($user->info('email'), $user->info('username'), 'Activate Your Account', $email);
		
		$error->set('SUCCESS');
		$success = TRUE;
		
	}
	
} // End: Form submitted.

show_title();

if ( !isset($success) ) { ?>
<form action="/account?tab=resend" method="POST">
    <div class="input-rules">
        <p>If you didn't receive an activation email when you registered, you can request another one here.</p>
        <ol>
            <li>Your email is currently set to <b><?php echo $user->info('email'); ?></b>, we'll sent it to that email.</li>
            <li>If the email above is incorrect, <a href="/account">click here</a> to change it.</li>
        </ol>
    </div>
    <div class="submit">
        <button type="submit" class="bttn big green"><i class="fa fa-envelope"></i> Request Activation Email</button>
    </div>
    <input type="hidden" name="resend" value="true">
</form>
<?php

}

break; // End: Resending activation email.

} // End: Tab switch.

?>
<?php show_footer(); ?>
