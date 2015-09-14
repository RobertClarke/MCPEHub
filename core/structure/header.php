<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $this->title; ?></title>
		<link rel="stylesheet" href="/assets/css/main.css" type="text/css">
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300" type="text/css">
		<link rel="shortcut icon" href="/favicon.png">
		<meta name="description" content="<?php echo $this->seo_desc; ?>">
		<meta name="keywords" content="<?php echo $this->seo_tags; ?>">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
		<meta name="format-detection" content="telephone=no">
		<meta property="og:site_name" content="MCPE Hub">
		<meta property="fb:app_id" content="873336029407458">
		<meta property="og:locale" content="en_US">
		<meta property="og:title" content="<?php echo ( !empty($this->fb_title) ) ? $this->fb_title : $this->title; ?>">
		<meta property="og:description" content="<?php echo ( !empty($this->fb_desc) ) ? $this->fb_desc : $this->seo_desc; ?>">
		<meta property="og:url" content="<?php echo ( !empty($this->fb_url) ) ? $this->fb_url : $this->url; ?>">
		<meta property="og:image" content="<?php echo ( !empty($this->fb_img) ) ? $this->fb_img : 'http://mcpehub.com/assets/img/fb_banner.jpg'; ?>">
		<meta property="og:type" content="<?php echo ( $this->fb_article ) ? 'article' : 'website'; ?>">
		<?php if ( !empty($this->canonical) ) echo '<link rel="canonical" href="'.$this->canonical.'">'; ?>
	</head>
	<body<?php
		if ( !empty($this->body_id) ) echo ' id="'.$this->body_id.'"';
		if ( !empty($this->body_class) ) echo ' class="'.$this->body_class.'"'; ?>>
<?php if ( $this->body_class != 'boxed' ) { // Non-boxed layout, display normal header ?>
<?php if ( $this->api_fb ) { ?>
	<div id="fb-root"></div>
	<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=873336029407458";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	</script>
<?php } if ( $this->api_twitter ) { ?>
	<script>
	window.twttr = (function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0],
		t = window.twttr || {};
		if (d.getElementById(id)) return t;
		js = d.createElement(s);
		js.id = id;
		js.src = "https://platform.twitter.com/widgets.js";
		fjs.parentNode.insertBefore(js, fjs);
		t._e = [];
		t.ready = function(f) { t._e.push(f); };
		return t;
	}(document, "script", "twitter-wjs"));
	</script>
<?php } if ( $this->api_google ) { ?>
	<script src="https://apis.google.com/js/platform.js" async defer></script>
<?php } ?>
	<div id="contents">
	<div id="top">
		<div class="wrapper">
			<div id="logo"><a href="/">MCPE Hub</a></div>
			<nav class="main">
				<ul>
<?php
	foreach ( ['map','seed','texture','skin','mod','server','new'] as $p )
		echo '<li><a href="/'.$p.'s" class="'.$p.'s'. ((strpos($this->cur, $p) !== false) ? ' active' : '') .'"><i class="icon-'.$p.'"></i> '.ucwords($p).'s</a></li>';
?>
				</ul>
			</nav>
			<nav class="account">
				<ul>
					<li class="user-toggle"><a href="#user"><i class="icon-user"></i> <i class="icon-caret-down spacer-left"></i></a></li>
					<li class="menu-toggle"><a href="#menu"><i class="icon-menu"></i></a></li>
				</ul>
			</nav>
<?php global $u; if ( logged_in() ) { ?>
			<div class="login">
				<div class="user">
					<a href="/profile"><img src="/avatar/112x112/<?php echo $u->data['avatar']; ?>" alt="<?php echo $u->username; ?>" width="56" height="56"></a>
					<p><span>Hola,</span> <a href="/profile"><?php echo $u->username; ?></a></p>
				</div>
				<div class="links">
					<ul>
						<li><a href="/dashboard"><i class="icon-meter"></i> Dashboard</a></li>
						<li><a href="/moderate"><i class="icon-hammer"></i> Moderate</a></li>
						<li><a href="/profile"><i class="icon-cool"></i> Profile</a></li>
						<li><a href="/messages"><i class="icon-mail"></i> Messages</a></li>
						<li><a href="/account"><i class="icon-settings"></i> Settings</a></li>
						<li><a href="/logout"><i class="icon-key"></i> Logout</a></li>
					</ul>
				</div>
			</div>
<?php } ?>
		</div>
	</div>
	<header id="header">
		<div class="wrapper">
			<div class="title">
				<?php

					if ( $this->title_h1 == 'MCPE Hub' )
						echo '<h1 class="logo">MCPE Hub</h1>';
					else
						echo '<h1>'. $this->title_h1 .'</h1>';

					if ( !empty($this->title_h2) ) echo '<h2>'.$this->title_h2.'</h2>';
					if ( !empty($this->title_bttn) ) echo '<div class="bttn-container">'.$this->title_bttn.'</div>';

				?>
			</div>
		</div>
	</header>
	<section id="content"<?php if ( $this->alt_body ) echo ' class="alt"'; ?>>
		<?php if ( !$this->no_wrap ) echo '<div class="wrapper">'; ?>
<?php } // END: Non-boxed layout, display normal header ?>

