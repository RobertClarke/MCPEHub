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
    
    <base href="http://mcpehub.com/">
    <link rel="shortcut icon" href="./favicon.ico" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-precomposed.png" />
    <meta name="apple-mobile-web-app-title" content="MCPE Hub">
    <link rel="stylesheet" type="text/css" href="./assets/css/main.css" media="all" />
    
<?php if ( $page_id == 'boxed' ) { ?>    <link rel="stylesheet" type="text/css" href="./assets/css/boxed.css" media="all" /><?php } ?>
    
    <!-- Extra CSS -->
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css"  />
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:600,700,400,300" media="all" />
    <!-- Extra Fonts -->
    
    <meta charset="UTF-8">
    
    <script type="text/javascript">
//<![CDATA[
try{if (!window.CloudFlare) {var CloudFlare=[{verbose:0,p:1401845039,byc:0,owlid:"cf",bag2:1,mirage2:0,oracle:0,paths:{cloudflare:"/cdn-cgi/nexp/dokv=abba2f56bd/"},atok:"ffdd644c0cf852107efb78e494a19174",petok:"9bc09865fdc38abc269eca1c8af3c8d17950e012-1402185323-1800",zone:"mcpehub.com",rocket:"0",apps:{}}];CloudFlare.push({"apps":{"ape":"195a63d680ea0b81a0820582ea994b85"}});!function(a,b){a=document.createElement("script"),b=document.getElementsByTagName("script")[0],a.async=!0,a.src="//ajax.cloudflare.com/cdn-cgi/nexp/dokv=97fb4d042e/cloudflare.min.js",b.parentNode.insertBefore(a,b)}()}}catch(e){};
//]]>
</script>

<script>
  !function(g,s,q,r,d){r=g[r]=g[r]||function(){(r.q=r.q||[]).push(
  arguments)};d=s.createElement(q);q=s.getElementsByTagName(q)[0];
  d.src='//d1l6p2sc9645hc.cloudfront.net/tracker.js';q.parentNode.
  insertBefore(d,q)}(window,document,'script','_gs');
  
  _gs('GSN-032559-K');
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-8521263-42', 'mcpehub.com');
  ga('send', 'pageview');

</script>
    
</head>

<body>

<?php if ( $page_id == 'boxed' ) { // Boxed template. ?>

<div class="wrapper logo">
    <div class="logo-text"><a href="index.php">MCPE Hub</a></div>
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
                <li><a href="dashboard.php" class="first tip-header" data-tip="Dashboard"><i class="fa fa-tachometer solo"></i></a></li>
                <!--<li><a href="#" class="tip-header" data-tip="Notifications"><i class="fa fa-globe solo"></i></a></li>
                <li><a href="#" class="tip-header" data-tip="Messages"><i class="fa fa-envelope solo"></i></a></li>-->
                <li><a href="profile.php" class="tip-header" data-tip="My Profile"><i class="fa fa-male solo"></i></a></li>
                <li><a href="account.php" class="tip-header" data-tip="Account Settings"><i class="fa fa-gears solo"></i></a></li>
                <li><a href="login.php?logout" class="last tip-header" data-tip="Sign Out"><i class="fa fa-lock solo"></i></a></li>
<?php } else { ?>
                <li><a href="login.php" class="first"><i class="fa fa-sign-in"></i> Sign In</a></li>
                <li><a href="register.php" class="last reg"><i class="fa fa-star"></i> Create Account</a></li>
<?php } ?>
            </ul>
        </div>
        
        <div class="tagline">
            <div class="logo-main"><a href="/">MCPE Hub</a></div>
            <h2>The #1 Minecraft PE Community</h2>
        </div>
        
    </div>
    
<?php

// Foreach loop to display nav options because copy pasting code is annoying.
$nav_options = array(
	'home'		=>	array( 'home',		'/',			'index',	'<i class="fa fa-home"></i>', ),
	'maps'		=>	array( 'red',		'/maps',		'map',		'Maps' ),
	'seeds'		=>	array( 'yellow',	'/seeds',		'seed',		'Seeds' ),
	'textures'	=>	array( 'green',		'/textures',	'texture',	'Textures' ),
	'skins'	    =>	array( 'pink',		'/skins',	    'skin',	    'Skins' ),
	'mods'		=>	array( 'blue',		'/mods',		'mod',		'Mods' ),
	'servers'	=>	array( 'purple',	'/servers',	    'server',	'Servers' ),
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