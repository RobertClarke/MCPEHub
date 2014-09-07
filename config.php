<?php

/**
  * Website Base Configuration
  *
  * This file contains all of the important settings used throughout
  * the entire site. Without it, the site would crumble to pieces.
  *
  * WARNING: Don't change any options in this file if you don't know
  * what they do! This might (probably will) break the entire site!
**/
  
/** MYSQL SETTINGS **/

// MySQL hostname (usually 'localhost')
define( 'DB_HOST', 'localhost' );

// MySQL port (leave blank for default)
define( 'DB_PORT', '' );

// MySQL database username
define( 'DB_USER', 'root' );

// MySQL database password
define( 'DB_PASS', 'googleplex123' );

// MySQL database name
define( 'DB_NAME', 'mcpe_newer' );

/**
  * Security Unique Keys & Salts
  *
  * Make sure these are UNIQUE phrases! They must be all random!
  *
  * You can change these at any time to invalidate all existing user
  * cookies. This will force every user to have to log in again.
**/

define( 'SECRET_KEY', 'eiC^zyqKebthN2n*818RniI4hm*uhR$bEgV:+fh=_%DF|xLZ&W~E8A| na|nwVe+' );

?>