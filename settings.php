<?php

require_once( 'core.php' );

$tools = FALSE;
if ( !isset( $_GET['tab'] ) ) $_GET['tab'] = '';

// Setting page titles.
if ( $_GET['tab'] == 'avatar' ) $title = 'Change Avatar';
else $title = 'Account Settings';

show_header( $title );

// Tab nav: the nav in the title for the different tabs. Up here for convenience.
$tabs = array(
	1 => array( 'Account', 'settings.php', 'fa-user', '' ),
	2 => array( 'Change Avatar', 'settings.php?tab=avatar', 'fa-picture-o', 'avatar' ),
	3 => array( 'Email Preferences', 'settings.php?tab=email', 'fa-envelope', 'email' ),
);

$tab_nav = '<ul class="tabs">';
foreach ( $tabs as $tab ) {
	if ( $tab[3] != $_GET['tab'] ) $tab_nav .= '<li><a href="'.$tab[1].'"><i class="fa '.$tab[2].' fa-fw"></i> '.$tab[0].'</a></li>';
	else $tab_nav .= '<li class="active"><a href="'.$tab[1].'"><i class="fa '.$tab[2].' fa-fw"></i> '.$tab[0].'</a></li>';
}
$tab_nav .= '</ul>';

switch ( $_GET['tab'] ) { // START SWITCH

default: // CASE: Editing general settings.
	
	$errors = array(
		1 => array( 'The current password you entered was incorrect, no changes were made.', 'error', 'times' ),
		2 => array( 'The new passwords you entered didn\'t match.', 'error', 'times' ),
		3 => array( 'Account settings have been saved.', 'success', 'check' ),
		4 => array( 'The activation e-mail has been re-sent to your e-mail, <i>'.$user->info()['email'].'</i>.', 'success', 'envelope' )
	);
	
	// Resending verification e-mail, if requested.
	if ( isset( $_GET['action'] ) && $_GET['action'] == 'send_confirm' ) {
		
		// If the user isn't already verified.
		if ( $user->info()['verified'] == 0 ) {
			
			// SEND EMAIL
			$message = '<html><body><style>body{font: 13/25pxpx Arial, sans-serif; color: #333; background: #f7f7f7;} p{margin-bottom: 15px;}h1{font-size:25px;font-weight:normal;margin-bottom:10px;}</style>';
			$message .= '<h1>MCPE Hub Activation</h1>';
			$message .= '<p>Hey <strong>'.$user->info()['username'].'</strong>!</p>';
			$message .= '<p>Please confirm your account by clicking on this link: <a href="'.MAIN_URL.'activate.php?user='.$user->info()['id'].'&code='.$user->info()['verified_code'].'">'.MAIN_URL.'activate.php?user='.$user->info()['id'].'&code='.$user->info()['verified_code'].'</a></p>';
			$message .= '<p>MCPE Hub Team.</p>';
			$message .= '</body></html>';
			
			$headers = 'From: MCPE Hub <noreply@mcpehub.com>' . "\r\n";
			$headers .= 'Reply-To: MCPE Hub <noreply@mcpehub.com>' . "\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-Type: text/html;' . "\r\n";
			
			mail( $user->info()['email'], 'Activate Your Account', $message, $headers );
			
			$error = 4;
			
		}
		
	}
	
	if ( !empty( $_POST ) ) {
		
		$change_password = FALSE;
		
		$password_old = strip_tags( $_POST['password'] );
		$password_new = strip_tags( $_POST['password-new'] );
		$password_repeat = strip_tags( $_POST['password-repeat'] );
		$email = strip_tags( $_POST['email'] );
		
		// Check if the old password was correct.
		if ( password_verify( $password_old, $user->info()['password'] ) ) {
			
			// Checking if we need to change the password & if they match.
			if ( !empty( $password_new ) ) {
				if ( $password_new == $password_repeat ) {
					
					$change_password = TRUE;
					$password_new = password_hash( $password_new, PASSWORD_DEFAULT );
					
				} else $error = 2; // New passwords don't match.
			}
			
			// If no errors, continue and update values.
			if ( !isset( $error ) ) {
				
				// Update in database (with password).
				if ( $change_password ) $update_vals = array( 'email' => $email, 'password' => $password_new );
				
				// Update in database (no password).
				else $update_vals = array( 'email' => $email );
				
				$db->where( array( 'id' => $user->info()['id'] ) )->update( 'users', $update_vals );
				
				// Success message.
				$error = 3;
				
			}
			
		} else $error = 1; // Old password incorrect.
		
	}
			
?>

<div id="page-head">
    <h2>Account Settings</h2>
    <?php echo $tab_nav; ?>
    <div class="clear"></div>
</div>

<?php if ( !empty( $error ) ) { $m = TRUE; echo '<div class="message '.$errors[$error][1].'"><i class="fa fa-'.$errors[$error][2].' fa-fw"></i> '.$errors[$error][0].'</div>'; } ?>

<form action="settings.php" method="POST" class="form">
    
    <div class="input">
        <label for="password"><i class="fa fa-star fa-fw"></i> <strong>Current Password:</strong></label>
        <input type="password" name="password" id="password" class="text" value="" />
        <div class="sub-info"><strong>Required to make any changes to your account.</strong></div>
    </div>
    
    <div class="spacer"></div>
    
    <div class="input">
        <label for="email"><i class="fa fa-envelope fa-fw"></i> Account E-Mail:</label>
        <input type="text" name="email" id="email" class="text" value="<?php if ( !empty( $_POST ) ) echo $_POST['email']; else if ( !empty( $user->info()['email'] ) ) echo $user->info()['email']; ?>" />
        <div class="sub-info">Change your e-mail notification preferences <a href="settings.php?tab=email">here</a>.</div>
    </div>
    
    <div class="spacer"></div>
    
    <div class="input">
        <label for="password-new"><i class="fa fa-lock fa-fw"></i> Change Password:</label>
        <input type="password" name="password-new" id="password-new" class="text" value="" />
    </div>
    
    <div class="input">
        <label for="password-repeat"><i class="fa fa-lock fa-fw"></i> Repeat Password:</label>
        <input type="password" name="password-repeat" id="password-repeat" class="text" value="" />
        <div class="sub-info">Leave these inputs blank for no password change.</div>
    </div>
    
    <input type="submit" name="submit" id="submit" class="submit button" value="Save Changes" />
    
</form>

<?php
	
break; // END: Editing general settings.

case 'avatar': // CASE: Changing avatar.
	
	$errors = array(
		1 => array( 'You didn\'t upload an image!', 'warning', 'exclamation-triangle' ),
		2 => array( 'Only image files are allowed to be uploaded, please try again.', 'error', 'times' ),
		3 => array( 'The image you uploaded was too big. Max file size: 3MB.', 'error', 'times' ),
		4 => array( 'Image couldn\'t be uploaded. Please try updating your avatar again later.', 'warning', 'exclamation-triangle' ),
		5 => array( 'Avatar has been uploaded and saved!', 'success', 'check' ),
	);
	
	if ( isset( $_GET['success'] ) ) $error = 5;
	
	if ( !empty( $_POST ) ) {
		
		$avatar = $_FILES['avatar'];
		$upload_dir = MAINPATH . 'uploads/avatars/';
		
		if ($avatar['error'] == 4) $error = 1; // No image uploaded.
		else if ($avatar['error'] == 1) $error = 3; // Maximum upload size reached.
		else if (@!getimagesize($avatar['tmp_name'])) $error = 2; // Only image files are allowed.
		
		$file_extension = '.' . @strtolower( end( explode( '.', $avatar['name'] ) ) );
		$file_name = $user->info()['username'] . '-' . uniqid();
		$avatar_file = $file_name . $file_extension;
		
		// If no errors, continue with upload.
		if ( !isset( $error ) ) {
			
			// Moving the image to the avatar folder, if successful, update database.
			if ( @move_uploaded_file($avatar['tmp_name'], $upload_dir . $file_name . $file_extension ) ) {
				
				// Delete the old avatar image. * Only if it's not a default avatar *
				$d_avatars = array( 'default_cow.png', 'default_creeper.png', 'default_pig.png', 'default_skeleton.png', 'default_zombie.png', 'default_zombiepig.png' );
				
				if ( !in_array( $user->info()['avatar_file'], $d_avatars ) ) {
					@unlink( MAINPATH . 'uploads/avatars/' . $user->info()['avatar_file'] );
				}
				
				// Update in database.
				$db->where( array( 'id' => $user->info()['id'] ) )->update( 'users', array( 'avatar_file' => $avatar_file ) );
				
				// Redirect to success page (because we want the image to update in sidebar).
				redirect( 'settings.php?tab=avatar&success=true' );
				
			} else $error = 4; // Access denied for upload.
			
		}
		
	}
		
?>

<div id="page-head">
    <h2>Change Avatar</h2>
    <?php echo $tab_nav; ?>
    <div class="clear"></div>
</div>

<?php if ( !empty( $error ) ) { $m = TRUE; echo '<div class="message '.$errors[$error][1].'"><i class="fa fa-'.$errors[$error][2].' fa-fw"></i> '.$errors[$error][0].'</div>'; } ?>

<form action="settings.php?tab=avatar" method="POST" class="form" enctype="multipart/form-data">
    
    <p>Your avatar will be used on your profile and throughout the site to identify you. Please keep it appropriate or we will suspend your account.</p>
    <p>Click below to select a picture from your computer and then click 'Upload Avatar' to upload it.</p>
    <br />
    <div class="input no-bottom">
        <label for="avatar"><i class="fa fa-picture-o fa-fw"></i> Upload File:</label>
        <input type="file" name="avatar" id="avatar" class="file" />
        <div class="sub-info">Maximum 3MB. Recommended image size: 256px * 256px.</div>
    </div>
    
    <input type="submit" name="submit" id="submit" class="submit button" value="Upload Avatar" />
    
</form>

<?php
	
break; // END: Changing avatar.

case 'email': // CASE: Email preferences.
	
	$error = 1;
	
	$errors = array(
		1 => array( 'This feature is under construction and will be released soon.', 'warning', 'exclamation-triangle' )
	);
	
?>

<div id="page-head">
    <h2>Email Preferences</h2>
    <?php echo $tab_nav; ?>
    <div class="clear"></div>
</div>

<?php if ( !empty( $error ) ) { $m = TRUE; echo '<div class="message '.$errors[$error][1].'"><i class="fa fa-'.$errors[$error][2].' fa-fw"></i> '.$errors[$error][0].'</div>'; } ?>

<?php
	
break; // END: Email preferences.

} // END SWITCH

?>

<?php if ( !empty( $error ) && !isset( $m ) ) echo '<div class="message '.$errors[$error][1].'"><i class="fa fa-'.$errors[$error][2].' fa-fw"></i> '.$errors[$error][0].'</div>'; ?>

<?php show_footer(); ?>