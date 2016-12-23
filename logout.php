<?php

/**
  * User Logout
**/

require_once('core.php');

if ( $user->logged_in() ) $user->logout();
else redirect('/login');

?>