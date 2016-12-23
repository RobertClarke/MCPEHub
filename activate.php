<?php

/**
  * User Activation
**/

require_once('core.php');
show_header('Activate Account', FALSE, ['body_id' => 'auth_action', 'body_class' => 'boxed']);

$f['code'] = ( isset($_GET['code']) ) ? $_GET['code'] : NULL;

$error->add('MISSING',	'Activation token is missing.');
$error->add('ACTIVE',	'Your account is already activated.', 'success');
$error->add('SUCCESS',	'Your account has been activated!', 'success');

if		( isset( $_GET['success'] ) ) $error->force('SUCCESS');
elseif	( isset( $_GET['activated'] ) ) $error->force('ACTIVE');

// Check if activation code exists in URL.
if ( isset($_GET['code']) ) {
	
	// Check if activation code missing.
	if ( empty($_GET['code']) ) $error->force('MISSING');
	else {
		
		$error->add('INVALID',	'Activation token provided is invalid.');
		
		$f['code'] = $db->escape($f['code']);
		
		$activate = $db->select('id, activated')->from('users')->where(['activate_token' => $f['code']])->limit(1)->fetch();
		
		// Check if activation code exists in database.
		if ( !$db->affected_rows ) $error->set('INVALID');
		else {
			
			$activate = $activate[0];
			
			// If user is already activated, redirect.
			if ( $activate['activated'] == 1 ) redirect('/activate?activated');
			else {
				
				// Token verified, activate.
				$db->where(['id' => $activate['id']])->update('users', ['activated' => 1]);
				
				$db->delete()->from('activate_resend')->where('user', $activate['id'])->execute();
				
				redirect('/activate?success');
				
			} // End: If user is already activated, redirect.
			
		} // End: Check if activation code exists in database.
		
	} // End: Check if activation code missing.

} // End: Check if activation code exists in URL.

?>

<div class="header"><h1>Activate Account</h1></div>
<div class="body">
    <form action="/activate" method="GET">
        <?php echo $error->display(); ?>
<?php if ( !isset($_GET['success']) && !isset($_GET['activated']) ) { // If the activation was successful. ?>
        <div class="group">
            <div class="label"><label for="code">Activation Code</label></div>
            <input type="text" name="code" id="code" class="full" value="<?php $form->get_val('code'); ?>" spellcheck="false">
        </div>
        <div class="submit">
            <button type="submit" class="bttn big green">Activate Account</button>
        </div>
<?php } ?>
    </form>
</div>
<div class="footer">
    <a href="/login" class="bttn mini"><i class="fa fa-long-arrow-left"></i> Back to Sign In</a>
</div>

<?php if ( empty( $f['code'] ) ) { ?><script>window.onload = function() { document.getElementById('code').focus(); };</script><?php } ?>

<?php show_footer(); ?>