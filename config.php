<?php

/**
  
  * Website Configuration
  *
  * This file contains all of the important settings used throughout
  * the entire site. Without it, bad, bad things would happen.
  
**/


/**
  * Debug Mode
  *
  * Set value to FALSE if in production. This disables all visible
  * errors such as sensitive SQL errors that might cause hacking.
**/

define('DEBUG_MODE', TRUE);


/**
  * MySQL Database Settings
**/

define('DB_HOST', 'localhost');	// Usually 'localhost'.
define('DB_PORT', '');			// Leave blank for default port.

define('DB_USER', 'root');
define('DB_PASS', 'googleplex123');

define('DB_NAME', 'mcpe_NEWEST');


/**
  * Mandrill Settings
  *
  * These settings are secured and hidden from the public. If all
  * emails stop sending, DOUBLE CHECK these settings!
**/

define('MAIL_USER', 'admin@cubemotion.com');
define('MAIL_PASS', 'eiKnvMUNhH-6IqPHXHdB-A');

define('MAIL_HOST', 'smtp.mandrillapp.com');
define('MAIL_PORT', 587);


/**
  * Security Unique Keys & Salts
  *
  * Make sure these are UNIQUE phrases! They must be all random!
  *
  * You can change these at any time to invalidate all existing user
  * cookies. This will force every user to have to log in again.
**/

define('SECRET_KEY', 'eiC^zyqKebthN2n*818RniI4hm*uhR$bEgV:+fh=_%DF|xLZ&W~E8A| na|NwVe+');
define('AUTH_COOKIE', 'mcpe_auth_cookie_73x*F#f@');

?>