<?php ob_start(); ?>
<?php

require_once( 'core.php' );

// Only allow admins/mods on this page!
if ( !$user->is_admin() ) {
	if ( $user->logged_in() ) redirect('/');
	else redirect('/login?auth_req');
}

if ( !empty( $_POST ) ) {
	
	if ( !empty( $_POST['username'] ) ) {
	
	$username = $_POST['username'];
	$verified = isset( $_POST['verified'] ) ? TRUE : FALSE;
	$youtuber = isset( $_POST['youtuber'] ) ? TRUE : FALSE;
	
	$update = array();
	
	if ( $verified ) $update['verified'] = 1;
	if ( $youtuber ) $update['youtuber'] = 1;
	
	if ( !empty($update)) {
	
	$db->where( array( 'username' => $username ) )->update( 'users', $update );
	
	redirect('give_rank?success=TRUE');
	
	}
	
	}
	
}

?>

<html>
<head>
<title>Ghetto Rank Giver</title>
<meta name="robots" content="noindex">

<style>
* { margin: 0; padding: 0; outline: 0; }
body {
	font: 13px/20px Helvetica, Arial, sans-serif;
	min-width: 350px;
	background: #eee;
}
h1 {
	font-size: 30px;
	font-weight: normal;
}
.cont {
	width: 330px;
	margin: 0 auto;
}
#header {
	color: #fff;
	
	margin-top: 20px;
	
	text-align: center;
}
#header .cont {
	background: #222;
	padding: 20px 0;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
	border-top-left-radius: 5px;
	border-top-right-radius: 5px;
}
#body {
	
}
#body .cont {
	width: 300px;
	background: #fff;
	padding: 15px;
	
	-webkit-border-bottom-right-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;
	-moz-border-radius-bottomright: 5px;
	-moz-border-radius-bottomleft: 5px;
	border-bottom-right-radius: 5px;
	border-bottom-left-radius: 5px;
}
.warn {
	color: red;
	padding-bottom: 15px;
	margin-bottom: 15px;
	border-bottom: 1px dashed #ddd;
	text-align: center;
}
.warn.good {
	font-weight: bold;
	color: green;
}
input.text {
	width: 200px;
	padding: 8px 8px;
	background: #fff;
	border: 1px solid #ddd;
	font: 13px Helvetica, Arial, sans-serif;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}
label {
	cursor: pointer;
	margin-right: 5px;
}
.sep {
	margin-bottom: 8px;
}
</style>

</head>
<body>

<div id="header">
    <div class="cont"><h1>Ghetto Rank Giver</h1></div>
</div>

<div id="body">
    <div class="cont">
        
        <?php if (isset($_GET['success'] ) ) { ?><div class="warn good">User updated!</div><?php } else { ?><div class="warn">Warning: very ghetto.</div><?php } ?>
        
        <form action="" method="POST">
        
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" class="text" value="" /><br /><br />
        
        <div class="sep"><input type="checkbox" name="verified" id="verified" /> <label for="verified">Verified</label></div>
        <div class="sep"><input type="checkbox" name="youtuber" id="youtuber" /> <label for="youtuber">YouTuber</label></div><br />
        
        <input type="submit" value="Make Changes" /><br /><br />
        
        </form>
        
        <span style="color: #777;font-size: 12px;">Note: can only add, not remove ranks. Do that in SQL, will be added at a later time.</span>
        
    </div>
</div>

</body>
</html>