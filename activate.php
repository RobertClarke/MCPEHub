<?php

require_once( 'core.php' );
show_header( 'Activate Account', FALSE, 'boxed' );

// Default POST variables.
$form_code = isset( $_GET['code'] ) ? $_GET['code'] : '';

// Form error messages.
$error->add( 'INPUT_MISSING', 'You must enter an activation code.', 'error', 'times' );
$error->add( 'INCORRECT_CODE', 'The activation code is invalid.', 'error', 'times' );

$error->add( 'ACTIVATED', 'Your account is already activated!', 'success', 'check' );
$error->add( 'SUCCESS', 'Your account has been activated!', 'success', 'check' );

if ( isset( $_GET['success'] ) ) $error->force( 'SUCCESS' );
if ( isset( $_GET['activated'] ) ) $error->force( 'ACTIVATED' );

// If activation form is submitted.
if ( isset( $_GET['code'] ) ) {
	
	$error->reset();
	
	// Check if code is missing.
	if ( empty( $_GET['code'] ) ) $error->set( 'INPUT_MISSING' );
	else {
		
		$code = $db->escape( $_GET['code'] );
		
		// Check if code exists in the database.
		$db_user = $db->select( 'id,activated' )->from( 'users' )->where( array( 'activate_code' => $code ) )->fetch();
		
		if ( !$db->affected_rows ) $error->set( 'INCORRECT_CODE' );
		else {
			
			$db_user = $db_user[0];
			
			// If the user is already activated.
			if ( $db_user['activated'] == 1 ) redirect( '/activate?activated' );
			else {
				
				// Activate the user!
				$db->where( array( 'id' => $db_user['id'] ) )->update( 'users', array( 'activated' => 1 ) );
				redirect( '/activate?success' );
				
			} // END: If user isn't already activated.
			
		} // END: Check if code in database.
		
	} // END: Activation code not missing.
	
} // END: Form submitted.

?>

<script>window.onload = function() { document.getElementById('code').focus(); };</script>

<h1>Activate Account</h1>
<?php $error->display(); ?>

<?php if ( !isset( $_GET['success'] ) && !isset( $_GET['activated'] ) ) { ?>
<form action="/activate" method="GET" class="form">
    
    <div class="group">
        <label for="code">Activation Code</label>
        <input type="text" name="code" id="code" class="text" value="<?php echo htmlspecialchars($form_code); ?>" autocomplete="off" spellcheck="false" maxlength="25" />
    </div>
    
    <button type="submit" id="submit">Activate Account</button>

</form>
<?php } ?>

<div class="links"><a href="/login">&laquo; Back to Login</a></div>

<?php show_footer(); ?>