<?php

/**
 * User Logout
 *
 * Will log out any users who hit this page if they are logged in
 * already. If not, it will redirect them back to the login page.
**/

require_once('loader.php');

if ( logged_in() )
	logout();

else
	redirect('/login');