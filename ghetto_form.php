<?php

require_once( 'core.php' );

if ( $user->loggedIn() ) {
	
	if ( $user->isAdmin() ) {
		
		if ( $_POST ) {
			
			$username = $_POST['username'];
			$password = $_POST['password'];
			
			if ( !empty( $username ) && !empty( $password ) ) {
				
				
				if ( $user->checkUsername( $username ) ) {
				
					$pass_new = password_hash( $password, PASSWORD_DEFAULT );
				
					$update_vals = array( 'password' => $pass_new );
					
					$db->where( array( 'username' => $username ) )->update( 'users', $update_vals );
					
					echo '<b style="color:green;">SUCCESS: password updated.</b><br /><br />';
				
				}
				
				else echo '<b style="color:red;">ERROR: Username doesnt exist.</b><br /><br />';
				
				
			}
			
			else echo '<b style="color:red;">ERROR: Missing username or password.</b><br /><br />';
			
		}
		
		echo 'Welcome to the ghetto password change script.<br /><br />';
		
		?>
		
		
		<form action="ghetto_form.php" method="POST">
		
		Username: <input type="text" name="username" id="username" value="<?php if ( isset( $username ) ) echo $username; ?>" /><br />
		Password: <input type="text" name="password" id="password" value="" /><br /><br />
		
		<span style="color:red;">Make sure username is correct, changes are instant!</span>
		
		<br /><br />
		
		<input type="submit" name="submit" value="Change Password" />
		
		</form>
		
		
		<?php
		
	} else redirect('index.php');

} else redirect('index.php');

?>