<?php

ob_start();

global $pg, $db, $num_unapproved;

// Default SEO values.
$seo_default_desc = 'MCPE Hub is the #1 Minecraft PE community in the world, featuring seeds, maps, servers, mods, and more.';
$seo_default_tags = 'minecraft pe, mcpehub, mcpe';

// Main page values array + defaults.
$pg = [
	'title'			=> (!empty($page_title)) ? $page_title : NULL,
	'sidebar'		=> (isset($pg_set['sidebar']) && !$pg_set['sidebar']) ? FALSE : TRUE,
	
	'current'		=> basename($_SERVER['PHP_SELF'], '.php'),
	'html_title'	=> NULL,
	'nav'			=> NULL,
	
	'seo_desc'		=> (!isset($pg_set['seo_desc'])) ? $seo_default_desc : $pg_set['seo_desc'],
	'seo_keywords'	=> (!isset($pg_set['seo_keywords'])) ? $seo_default_tags : $pg_set['seo_keywords'],
	
	'body_id'		=> (!empty($pg_set['body_id'])) ? ' id="'.$pg_set['body_id'].'"' : NULL,
	'body_class'	=> (!empty($pg_set['body_class'])) ? ' class="'.$pg_set['body_class'].'"' : NULL,
	
	'title_main'	=> (!empty($pg_set['title_main'])) ? $pg_set['title_main'] : 'MCPE Hub',
	'title_sub'		=> (!empty($pg_set['title_sub'])) ? $pg_set['title_sub'] : 'The #1 Minecraft PE Community',
];

// Setting <title> value in <head>.
if ( !empty($pg['title']) ) $pg['html_title'] .= $pg['title'].' | ';
$pg['html_title'] .= 'MCPE Hub';
if ( $pg['current'] == 'index' ) $pg['html_title'] .= ' | Minecraft PE Maps, Seeds, Textures, Mods, Servers &amp; More!';

// Build main category navigation.
$pg['nav_links'] = [
	//'home'		=>	['home',		'/',			'index',	'<i class="fa fa-home"></i>'],
	'blog'		=>	['blog',		'/blog',		'blog',		'<i class="fa fa-newspaper-o"></i>'],
	'maps'		=>	['red',			'/maps',		'map',		'Maps'],
	'seeds'		=>	['yellow',		'/seeds',		'seed',		'Seeds'],
	'textures'	=>	['green',		'/textures',	'texture',	'Textures'],
	'skins'		=>	['pink',		'/skins',		'skin',		'Skins'],
	'mods'		=>	['blue',		'/mods',		'mod',		'Mods'],
	'servers'	=>	['purple',		'/servers',		'server',	'Servers']
];

foreach( $pg['nav_links'] as $id => $n ) {
	$active = ($pg['current'] == $n[2] || $pg['current'] == $n[2].'s') ? ' active' : NULL;
	$pg['nav'] .= "<li><a href=\"{$n[1]}\" class=\"{$n[0]}{$active}\">{$n[3]}</a></li>";
}

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
<html lang="en">
<head>
    
    <title><?php echo $pg['html_title']; ?></title>
    
    <meta charset="UTF-8">
    <meta name="description" content="<?php echo $pg['seo_desc']; ?>">
    <meta name="keywords" content="<?php echo $pg['seo_keywords']; ?>">
    
    <link rel="stylesheet" type="text/css" href="/assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Changa+One%7COpen+Sans:300,400,600,700,800">
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
    
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="format-detection" content="telephone=no">
    
<script type="text/javascript">
//<![CDATA[
try{if (!window.CloudFlare) {var CloudFlare=[{verbose:0,p:1401845039,byc:0,owlid:"cf",bag2:1,mirage2:0,oracle:0,paths:{cloudflare:"/cdn-cgi/nexp/dokv=abba2f56bd/"},atok:"ffdd644c0cf852107efb78e494a19174",petok:"9bc09865fdc38abc269eca1c8af3c8d17950e012-1402185323-1800",zone:"mcpehub.com",rocket:"0",apps:{}}];CloudFlare.push({"apps":{"ape":"195a63d680ea0b81a0820582ea994b85"}});!function(a,b){a=document.createElement("script"),b=document.getElementsByTagName("script")[0],a.async=!0,a.src="//ajax.cloudflare.com/cdn-cgi/nexp/dokv=97fb4d042e/cloudflare.min.js",b.parentNode.insertBefore(a,b)}()}}catch(e){};
//]]>
</script>

<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-8521263-42', 'mcpehub.com');
ga('send', 'pageview');

</script>

<script type="text/javascript">
_atrk_opts = { atrk_acct:"urwtl1agWBr1N8", domain:"mcpehub.com",dynamic: true};
(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=urwtl1agWBr1N8" style="display:none" height="1" width="1" alt="" /></noscript>
    
</head>
<body<?php echo $pg['body_id'] . $pg['body_class']; ?>>

<?php

/** BOXED TEMPLATE **/
if ( strpos($pg['body_class'], 'boxed') ) {

?>

<div id="header"><div class="wrapper"><a href="/" class="logo">MCPE Hub</a></div></div>

<div id="main">
    <div class="wrapper">

<?php

/** END BOXED TEMPLATE **/

} else { /** NORMAL PAGE TEMPLATE **/

?>

<div id="header">
    <div id="top"><div class="wrapper">
        <a href="/" class="logo">MCPE Hub</a>
        <div class="nav"><ul><?php echo $pg['nav']; ?></ul></div>
        <a href="#" class="menu-responsive"><i class="fa fa-bars"></i></a>
        <div class="sub-nav">
<?php if ( !$user->logged_in() ) { ?>
            <div class="bttn-group">
                <a href="/register" class="bttn gold">Create Account</a>
                <a href="/login" class="bttn">Sign In</a>
            </div>
<?php } else { ?>
            <a href="/dashboard" data-tip="Dashboard"><i class="fa fa-tachometer"></i></a>
<?php if ( $user->is_admin() || $user->is_mod() ) { ?><a href="/moderate" data-tip="Moderate"><i class="fa fa-gavel"></i><?php echo ($num_unapproved != 0) ? '<span class="badge">'.$num_unapproved.'</span>' : NULL; ?></a><?php } ?>
            <a href="/alerts" data-tip="Notifications" data-dropdown="#drop-notif" data-toggle="modal" data-target="#modal-soon"><i class="fa fa-bullhorn"></i></a>
            <a href="/inbox" data-tip="Messages" data-dropdown="#drop-msgs" data-toggle="modal" data-target="#modal-soon"><i class="fa fa-send"></i></a>
            <div class="user">
                <a href="/profile" class="toggle" data-dropdown="#nav-user">
                    <img src="/avatar/60x60/<?php echo $user->info('avatar'); ?>" alt="<?php echo $user->info('username'); ?>" width="30" height="30">
                    <i class="fa fa-caret-down"></i>
                </a>
            </div>
<?php } ?>
        </div>
    </div></div>
    <div id="title" class="wrapper">
        <h1><span><?php echo $pg['title_sub']; ?></span> <?php echo $pg['title_main']; ?></h1>
    </div>
</div>

<div id="avrt-wide">
    <div class="wrapper">
        <div class="avrt">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- MCPEHub Responsive 1 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-3736311321196703"
     data-ad-slot="3687043070"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
            
            
        </div>
    </div>
</div>

<div id="main"><div class="wrapper">
<div id="content"<?php if ( !$pg['sidebar'] ) echo ' class="full"'; ?>>

<?php } /** END NORMAL PAGE TEMPLATE **/ ?>
