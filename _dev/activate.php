<?php

/**
  
  * User Activation
  *
  * Allows users to activate their account after they
  * click on the link in their welcome email.
  
**/

require_once('core.php');
show_header('Activate Account', FALSE, ['body_id' => 'boxed']);

$f['code'] = ( isset($_GET['code']) ) ? $_GET['code'] : NULL;

// Setting default error messages.
$error->add('MISSING',	'The activation token is missing.');
$error->add('ACTIVE',	'Your account has already been activated.', 'success');
$error->add('SUCCESS',	'Your account has been successfully activated.', 'success');

if		( isset( $_GET['success'] ) ) $error->force('SUCCESS');
elseif	( isset( $_GET['activated'] ) ) $error->force('ACTIVE');

// Check if activation code exists in URL.
elseif ( empty($_GET['code']) ) $error->force('MISSING');
else {
	
	$error->add('INVALID',	'The activation token provided is invalid.');
	
	$f['code'] = $db->escape($f['code']);
	
	$activate = $db->select('id, activated')->from('users')->where(['activate_token' => $f['code']])->limit(1)->fetch();
	
	// Check if activation code exists in database.
	if ( !$db->affected_rows ) $error->set('INVALID');
	else {
		
		$activate = $activate[0];
		
		// If user is already activated, redirect.
		if ( $activate['activated'] == 1 ) redirect('/activate?activated');
		else {
			
			/*** Token verified, activate ***/
			$db->where(['id' => $activate['id']])->update('users', ['activated' => 1]);
			redirect('/activate?success');
			
		} // End: If user is already activated, redirect.
		
	} // End: Check if activation code exists in database.
	
} // End: Check if activation code exists in URL.

?>

<div class="title"><h2>Activate Account</h2></div>
<div class="body">
    <?php echo $error->display(); ?>
<?php if ( !isset($_GET['success']) && !isset($_GET['activated']) ) { // If the activation was successful. ?>
    <form action="/activate" method="GET" class="form">
        <div class="group">
            <label for="code">Activation Code</label>
            <input type="text" name="code" id="code" value="<?php $form->GET_value('code'); ?>" spellcheck="false">
        </div>
        <button type="submit" class="bttn large purple full">Activate Account</button>
    </form>
<?php } // End: If the form hasn't been successfully submitted. ?>
</div>
<div class="footer">
    <a href="/login" class="full">Back to Sign In</a>
</div>

<?php if ( empty( $f['username'] ) ) { ?><script>window.onload = function() { document.getElementById('code').focus(); };</script><?php } ?>

<?php show_footer(); ?>