<?php

ob_start();

global $db, $no_sidebar, $page_current, $num_unapproved;

$page_current = basename( $_SERVER['PHP_SELF'], '.php' );
$no_sidebar = isset( $no_sidebar ) ? TRUE : FALSE;

// If admin/mod, get number of pending posts.
if ( $user->is_admin() || $user->is_mod() ) {
	
	$posts_unapproved = $db->query('
		(SELECT "map"	 	AS type, id FROM `content_maps` 	 WHERE active = "0") UNION ALL
		(SELECT "seed" 		AS type, id FROM `content_seeds` 	 WHERE active = "0") UNION ALL
		(SELECT "texture" 	AS type, id FROM `content_textures`	 WHERE active = "0") UNION ALL
		(SELECT "skin" 		AS type, id FROM `content_skins`	 WHERE active = "0") UNION ALL
		(SELECT "mod" 		AS type, id FROM `content_mods` 	 WHERE active = "0") UNION ALL
		(SELECT "server" 	AS type, id FROM `content_servers` 	 WHERE active = "0")
	')->fetch();
	$num_unapproved = $db->affected_rows;
	
	if ( $num_unapproved > 99 ) $num_unapproved = '99+';
	
}

?>
<!DOCTYPE html>
<html<?php if ( !empty( $page_id ) ) echo ' id="'.$page_id.'"'; ?> lang="en">
<head>
    <title><?php if ( !empty( $page_title ) ) echo $page_title.' | '; ?>MCPE Hub<?php if ( $page_current == 'index' ) echo ' | Minecraft PE Maps, Seeds, Textures, Mods, Servers &amp More!'; ?></title>
    
    <meta name="description" content="<?php echo isset( $page_description ) ? $page_description : ''; ?>">
    <meta name="keywords" content="<?php echo isset( $page_tags ) ? $page_tags : ''; ?>">
    
    <!--<base href="<?php echo MAINURL; ?>">-->
    <base href="http://mcpe.dev/">
    
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-precomposed.png" />
    <meta name="apple-mobile-web-app-title" content="MCPE Hub">
    <link rel="stylesheet" type="text/css" href="/assets/css/main.css" media="all" />
    
<?php if ( $page_id == 'boxed' ) { ?>    <link rel="stylesheet" type="text/css" href="/assets/css/boxed.css" media="all" /><?php } ?>
    
    <!-- Extra CSS -->
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css"  />
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:600,700,400,300" media="all" />
    <!-- Extra Fonts -->
    
    <meta charset="UTF-8">
    
</head>

<body>

<?php if ( $page_id == 'boxed' ) { // Boxed template. ?>

<div class="wrapper logo">
    <div class="logo-text"><a href="/">MCPE Hub</a></div>
</div>
<div id="body" class="wrapper">

<?php } else { // Default template. ?>

<div id="header">
    
    <div class="wrapper">
        
<?php if ( $user->is_admin() || $user->is_mod() ) { ?>
        <div class="nav-user left">
            <ul>
                <li><a href="/moderate" class="rounded"><i class="fa fa-gavel"></i> Moderate Posts<?php if ( $num_unapproved != 0 ) echo ' <span class="num">'.$num_unapproved.'</span>'; ?></a></li>
            </ul>
        </div>
<?php } ?>
        <div class="nav-user">
            <ul>
<?php if ( $user->logged_in() ) { ?>
                <li><a href="/dashboard" class="first tip-header" data-tip="Dashboard"><i class="fa fa-tachometer solo"></i></a></li>
                <li><a href="#" class="notifications tip-header" data-tip="Notifications"><i class="fa fa-globe solo"></i></a></li>
                <li><a href="/messages" class="tip-header" data-tip="Messages"><i class="fa fa-envelope solo"></i></a></li>
                <li><a href="/profile" class="tip-header" data-tip="My Profile"><i class="fa fa-male solo"></i></a></li>
                <li><a href="/account" class="tip-header" data-tip="Account Settings"><i class="fa fa-gears solo"></i></a></li>
                <li><a href="/login?logout" class="last tip-header" data-tip="Sign Out"><i class="fa fa-lock solo"></i></a></li>
<?php } else { ?>
                <li><a href="/login" class="first"><i class="fa fa-sign-in"></i> Sign In</a></li>
                <li><a href="/register" class="last reg"><i class="fa fa-star"></i> Create Account</a></li>
<?php } ?>
            </ul>
        </div>
        
        
        
        
        
        
        
        <div id="notifications" style="background: rgba(0,0,0,0.9);color:#fff;padding:10px;width:450px;text-align:left;position:absolute;top:10px;left:280px;z-index:99999;">
            
            
            <?php
            
            $notifications = $db->from( 'notifications' )->where( array( 'user_to' => $user->info('id') ) )->limit(5)->fetch();
$num = $db->affected_rows;


//echo '<b>'.$num.'</b> notifications for <b>'.$user->info('username').'</b><br /><br />';



foreach ( $notifications as $n ) {
	
	
	$user_from = $user->info('', $n['user_from']);
	
	echo '&bull; <b><a href="/user/'.$user_from['username'].'">'.$user_from['username'].'</a></b> ';
	
	switch( $n['type'] ) {
		
		default:
			
			echo 'default';
			
		break;
		
		case 'like':
			
			$ref = explode( '|', $n['ref'] );
			
			$type = $ref[0];
			$post = $ref[1];
			
			echo 'liked your '.$type.' ';
			
			echo "\"<a href=\"/$type/$post\">$post</a>\".";
			
			
			//echo '"<a href="/'.$type.'/'.$post.'"></a>".';
			
		break;
		
		case 'sub':
			
			echo 'subscribed to you.';
			
		break;
		
		case 'approve':
			
			$ref = explode( '|', $n['ref'] );
			
			$type = $ref[0];
			$post = $ref[1];
			
			echo 'approved your '.$type.' ';
			
			echo "\"<a href=\"/$type/$post\">$post</a>\".";
			
		break;
		
	}
	
	echo ' ' . time_since( $n['time'] );
	echo '<br />';
	
}

if ( count( $notifications ) == 5 ) echo '<br />View all notifications...';


?>
            
            
            
        </div>
        
        
        
        
        
        
        
        <div class="tagline">
            <div class="logo-main"><a href="/">MCPE Hub</a></div>
            <h2><a href="/">The #1 Minecraft PE Community</a></h2>
        </div>
        
    </div>
    
<?php

// Foreach loop to display nav options because copy pasting code is annoying.
$nav_options = array(
	'home'		=>	array( 'home',		'/',			'index',	'<i class="fa fa-home"></i>', ),
	'maps'		=>	array( 'red',		'/maps',		'map',		'Maps' ),
	'seeds'		=>	array( 'yellow',	'/seeds',		'seed',		'Seeds' ),
	'textures'	=>	array( 'green',		'/textures',	'texture',	'Textures' ),
	'skins'		=>	array( 'pink',		'/skins',		'skin',		'Skins' ),
	'mods'		=>	array( 'blue',		'/mods',		'mod',		'Mods' ),
	'servers'	=>	array( 'purple',	'/servers',		'server',	'Servers' ),
);

?>
    
    <div id="nav">
        <ul>
<?php

foreach( $nav_options as $option => $nav ) {
	$active = ( $page_current == $nav[2] || $page_current == $nav[2].'s' ) ? ' active' : '';
	echo '<li class="'.$nav[0].$active.'"><a href="'.$nav[1].'">'.$nav[3].'</a></li>';
	echo "\n"; // Just for clean source HTML view.
}

?>
        </ul>
    </div>
    
</div>

<div id="header-advrt">
    <div class="wrapper">
        <div class="advrt header">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="8745532678"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>
    </div>
</div>

<div id="body">
    <div class="wrapper">
<?php if ( $page_current != 'index' ) { ?>        <div id="main"<?php if ( !empty( $main_class ) ) echo ' class="'.$main_class.'"'; ?>><?php } ?>

<?php } ?>