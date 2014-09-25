<?php

ob_start();

$page_current = basename($_SERVER['PHP_SELF'], '.php');

// Default SEO values.
if ( empty($pi['seo_description']) ) $pi['seo_description'] = 'MCPEHub is the #1 Minecraft PE community in the world, featuring seeds, maps, servers, mods, and more.';
if ( empty($pi['seo_keywords']) ) $pi['seo_keywords'] = 'minecraft pe, mcpehub, mcpe';

// Default title values.
if ( empty($pi['title_main']) ) $pi['title_main'] = 'MCPE Hub';
if ( empty($pi['title_sub']) ) $pi['title_sub'] = 'The #1 Minecraft PE Community';

// Loop to display category navigation.
$nav_main = '';
$nav_links = array(
	'maps'		=>	array( 'red',		'/maps',		'map',		'Maps' ),
	'seeds'		=>	array( 'yellow',	'/seeds',		'seed',		'Seeds' ),
	'textures'	=>	array( 'green',		'/textures',	'texture',	'Textures' ),
	'skins'		=>	array( 'pink',		'/skins',		'skin',		'Skins' ),
	'mods'		=>	array( 'blue',		'/mods',		'mod',		'Mods' ),
	'servers'	=>	array( 'purple',	'/servers',		'server',	'Servers' )
);

foreach( $nav_links as $option => $nav ) {
	$active = ( $page_current == $nav[2] || $page_current == $nav[2].'s' ) ? ' active' : '';
	$nav_main .= '<a href="'.$nav[1].'" class="'.$nav[0].$active.'">'.$nav[3].'</a>';
	$nav_main .= "\n";
}

if ( empty($pi['body_id']) ) $pi['body_id'] = '';
if ( empty($pi['body_class']) ) $pi['body_class'] = '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <title><?php if ( !empty($page_title) ) echo $page_title . ' | '; ?>MCPE Hub<?php if ( $page_current=='index' ) echo ' | Minecraft PE Maps, Seeds, Textures, Mods, Servers &amp; More!'; ?></title>
    
    <meta charset="UTF-8">
    <meta name="description" content="<?php echo $pi['seo_description']; ?>">
    <meta name="keywords" content="<?php echo $pi['seo_keywords']; ?>">
    
    <link rel="stylesheet" type="text/css" href="/assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Changa+One%7COpen+Sans:300,400,600,700,800">
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    
    <link rel="shortcut icon" href="/favicon.png?v=2" />
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
    <meta name="apple-mobile-web-app-title" content="MCPE Hub">
    
</head>
<body<?php if(!empty($pi['body_id'])) echo ' id="'.$pi['body_id'].'"'; ?><?php if(!empty($pi['body_class'])) echo ' class="'.$pi['body_class'].'"'; ?>>
    
<?php if ( $pi['body_id'] == 'boxed' ) { // Boxed template. ?>
    
    <div class="wrapper logo-wrap"><a href="/" class="logo">MCPE Hub</a></div>
    
    <div id="modal"<?php if ( isset($pi['modal_class']) ) echo ' class="'.$pi['modal_class'].'"'; ?>>
        <div class="wrapper">
    
<?php } else { // End: Show boxed template content. ?>
    
    <div id="header">
        <div id="top">
            <div class="wrapper">
                <a href="/" class="logo">MCPE Hub</a>
                <div id="nav-main">
<?php echo $nav_main; ?>
                </div>
<?php if ( !$user->logged_in() ) { ?>
                <div id="nav-user">
                    <a href="/login" data-toggle="modal" data-target="#modalLogin"><i class="fa fa-lock"></i> Sign In</a>
                    <a href="/register" class="create" data-toggle="modal" data-target="#modalRegister"><i class="fa fa-child"></i> Create Account</a>
<?php } else { ?>
                <div id="nav-user" class="tabs">
                    <a href="/dashboard" class="tip-b" data-tip="Dashboard"><i class="fa fa-tachometer fa-fw"></i></a>
                    <a href="/messages" class="tip-b" data-tip="Messages"><i class="fa fa-envelope fa-fw"></i></a>
                    <a href="/messages" class="tip-b" data-tip="My Profile"><i class="fa fa-male fa-fw"></i></a>
                    <a href="/account" class="tip-b" data-tip="Settings"><i class="fa fa-gears fa-fw"></i></a>
                    <a href="/login?logout" class="tip-b" data-tip="Logout"><i class="fa fa-lock fa-fw"></i></a>
<?php } ?>
                </div>
            </div>
        </div>
        <div id="main-title" class="wrapper">
            <h1><span><?php echo $pi['title_sub']; ?></span> <?php echo $pi['title_main']; ?></h1>
        </div>
    </div>
    
    <div class="avrt-wide">
        <div class="wrapper">
            <div class="avrt">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="8745532678"></ins>
                <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
            </div>
        </div>
    </div>
    
    <div id="main">
        <div class="wrapper">
            <div id="content"<?php if ( isset($pi['no_sidebar']) ) echo ' class="full"'; ?>>

<?php } // End: Show normal template content. ?>