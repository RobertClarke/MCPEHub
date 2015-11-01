<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>MCPE Hub | The #1 Minecraft PE Community</title>
		<link rel="stylesheet" href="./assets/css/main.css" type="text/css">


		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700|Varela+Round' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="https://i.icomoon.io/public/temp/9ab9b06e51/UntitledProject/style.css">


		<link rel="shortcut icon" href="/favicon.png">
		<meta name="description" content="MCPE Hub is the #1 Minecraft PE community in the world, featuring seeds, maps, servers, skins, mods, and more.">
		<meta name="keywords" content="minecraft pe, mcpe, minecraft, mcpehub">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
		<meta name="format-detection" content="telephone=no">
		<meta property="og:site_name" content="MCPE Hub">
		<meta property="fb:app_id" content="873336029407458">
		<meta property="og:locale" content="en_US">
		<meta property="og:title" content="MCPE Hub | The #1 Minecraft PE Community">
		<meta property="og:description" content="MCPE Hub is the #1 Minecraft PE community in the world, featuring seeds, maps, servers, skins, mods, and more.">
		<meta property="og:url" content="http://mcpe.dev/">
		<meta property="og:image" content="http://mcpehub.com/assets/img/fb_banner.jpg">
		<meta property="og:type" content="website">
	</head>
	<body id="homepage">
		<section id="top" class="extended">
			<div class="wrapper">
				<div id="logo"><a href="/">MCPE Hub</a></div>
				<nav id="nav-top">
					<ul>
						<li class="dropdown">
							<a href="#">MCPE Content <i class="icon-caret-down"></i></a>
							<!--<ul>
								<li><a href="#"><i class="icon-map"></i>Maps <span>3,138</span></a></li>
								<li><a href="#"><i class="icon-seed"></i>Seeds <span>1,701</span></a></li>
								<li><a href="#"><i class="icon-skin"></i>Skins <span>3,409</span></a></li>
								<li><a href="#"><i class="icon-texture"></i>Texture Packs <span>549</span></a></li>
								<li><a href="#"><i class="icon-mod"></i>Mods <span>889</span></a></li>
								<li><a href="#"><i class="icon-server"></i>Servers <b class="badge updated">Updated</b> <span>2174</span></a></li>
								<li><a href="#"><i class="icon-youtuber"></i>YouTubers <b class="badge new">New</b> <span>43</span></a></li>
								<li><a href="#"><i class="icon-tutorial"></i>Tutorials <span>16</span></a></li>
								<li><a href="#"><i class="icon-blog"></i>News <span>24</span></a></li>
								<li><a href="#"><i class="icon-update"></i>Game Updates <span>4</span></a></li>
							</ul>-->
						</li>
						<li class="dropdown">
							<a href="#">Community <i class="icon-caret-down"></i></a>
							<!--<ul>
								<li><a href="#"><i class="icon-trophy"></i>Leaderboards</a></li>
								<li><a href="#"><i class="icon-question"></i>About The Hub</a></li>
								<li><a href="#"><i class="icon-bullhorn"></i>Announcements</a></li>
								<li><a href="#"><i class="icon-line-chart"></i>Stats</a></li>
								<li><a href="#"><i class="icon-user-admin"></i>The Team </a></li>
								<li><a href="#"><i class="icon-twitter"></i>Follow Us</a></li>
							</ul>-->
						</li>
						<li><a href="#">Forums</a></li>
					</ul>
				</nav>
			</div>
			<div class="wrapper extended">
				<header id="header">
					<div class="centered">
						<h1>The #1 Minecraft PE Community</h1>
						<h2>Featuring <span>114,384 members</span>, <span>23,058 submissions</span> and more!</h2>
					</div>
					<a href="#" class="join-bttn">Join The Community</a>
				</header>
			</div>
		</section>
		<section id="content">
			<div class="wrapper">

				<div id="featured-posts">

<?php $counter = 1; while ( $counter <= 4 ) { ?>
					<article>
						<div class="screenshot">
							<a href="#like" class="likes"><i class="icon-heart"></i> 43</a>
							<a href="#"><img src="./assets/img/DEMO_POST<?php echo $counter; ?>.png" alt="" width="240" height="180"></a>
						</div>
						<div class="info">
							<a href="#"><h2>Minecraft Pocket Edition Floor After Floor</h2></a>
							<span class="author"><a href="#"><img src="./assets/img/DEMO_AVATAR.png" alt="" width="16" height="16"> Geoman</a></span>
						</div>
					</article>
<?php $counter++; } ?>

				</div>

				<!--<div class="avrt banner"></div>-->

				<div id="posts">
					<section class="posts maps">
						<div class="title">
							<h2><i class="icon-map"></i>Minecraft PE Maps</h2>
							<h4>Have a blast on maps created by our community members!</h4>
						</div>
<?php $counter = 1; while ( $counter <= 3 ) { ?>
						<article>
							<div class="screenshot">
								<a href="#like" class="likes"><i class="icon-heart"></i> 43</a>
								<a href="#"><img src="./assets/img/DEMO_POST<?php echo $counter; ?>.png" alt="" width="240" height="180"></a>
							</div>
							<div class="info">
								<span class="details"><ul>
									<li><a href="/maps/adventure">Adventure Map</a></li>
									<li>5,891 views</li>
								</ul></span>
								<a href="#"><h2>Minecraft Pocket Edition Floor After Floor</h2></a>
								<span class="author"><a href="#"><img src="./assets/img/DEMO_AVATAR.png" alt="" width="16" height="16"> Geoman</a></span>
							</div>
						</article>
<?php $counter++; } ?>
					</section>
					<section class="posts seeds">
						<div class="title">
							<h2><i class="icon-seed"></i>Minecraft PE Seeds</h2>
							<h4>Start in a fresh world surrounded by cool terrains, villages and more!</h4>
						</div>
<?php $counter = 1; while ( $counter <= 3 ) { ?>
						<article>
							<div class="screenshot">
								<a href="#like" class="likes"><i class="icon-heart"></i> 43</a>
								<a href="#"><img src="./assets/img/DEMO_POST<?php echo $counter; ?>.png" alt="" width="240" height="180"></a>
							</div>
							<div class="info">
								<span class="details"><ul>
									<li><a href="/maps/adventure">Mountain Seed</a></li>
									<li>5,891 views</li>
								</ul></span>
								<a href="#"><h2>Quintuple Village! Exposed Mesa Dungeon!</h2></a>
								<span class="author"><a href="#"><img src="./assets/img/DEMO_AVATAR.png" alt="" width="16" height="16"> Geoman</a></span>
							</div>
						</article>
<?php $counter++; } ?>
					</section>
				</div>

				<div id="posts">
					<section class="posts servers">
						<div class="title">
							<h2><i class="icon-server"></i>Minecraft PE Servers</h2>
							<h4>Join other members and play online on community servers!</h4>
						</div>
<?php $counter = 1; while ( $counter <= 3 ) { ?>
						<article>
							<div class="screenshot">
								<a href="#like" class="likes"><i class="icon-heart"></i> 43</a>
								<a href="#"><img src="./assets/img/DEMO_POST<?php echo $counter; ?>.png" alt="" width="240" height="180"></a>
							</div>
							<div class="info">
								<span class="details"><ul>
									<li><a href="/maps/adventure">PvP Server</a></li>
									<li>5,891 views</li>
								</ul></span>
								<a href="#"><h2>MythcraftPE: Home Of PVP! Factions, Creative, and 1v1!</h2></a>
								<span class="author"><a href="#"><img src="./assets/img/DEMO_AVATAR.png" alt="" width="16" height="16"> Geoman</a></span>
							</div>
						</article>
<?php $counter++; } ?>
					</section>
					<section class="posts skins">
						<div class="title">
							<h2><i class="icon-skin"></i>Minecraft PE Skins</h2>
							<h4>Customize your Minecraft PE character with an awesome skin!</h4>
						</div>
<?php $counter = 1; while ( $counter <= 3 ) { ?>
						<article>
							<div class="screenshot">
								<a href="#like" class="likes"><i class="icon-heart"></i> 43</a>
								<a href="#"><img src="./assets/img/DEMO_POST<?php echo $counter; ?>.png" alt="" width="240" height="180"></a>
							</div>
							<div class="info">
								<span class="details"><ul>
									<li><a href="/maps/adventure">Mob Skin</a></li>
									<li>5,891 views</li>
								</ul></span>
								<a href="#"><h2>Minecraft Story Mode Jesse Skin</h2></a>
								<span class="author"><a href="#"><img src="./assets/img/DEMO_AVATAR.png" alt="" width="16" height="16"> Geoman</a></span>
							</div>
						</article>
<?php $counter++; } ?>
					</section>
				</div>

				<div id="posts">
					<section class="posts textures">
						<div class="title">
							<h2><i class="icon-texture"></i>Minecraft PE Texture Packs</h2>
							<h4>Add a custom texture pack to make your game beautiful!</h4>
						</div>
<?php $counter = 1; while ( $counter <= 3 ) { ?>
						<article>
							<div class="screenshot">
								<a href="#like" class="likes"><i class="icon-heart"></i> 43</a>
								<a href="#"><img src="./assets/img/DEMO_POST<?php echo $counter; ?>.png" alt="" width="240" height="180"></a>
							</div>
							<div class="info">
								<span class="details"><ul>
									<li><a href="/maps/adventure">Realistic Texture Pack</a></li>
									<li>5,891 views</li>
								</ul></span>
								<a href="#"><h2>Ender Shader</h2></a>
								<span class="author"><a href="#"><img src="./assets/img/DEMO_AVATAR.png" alt="" width="16" height="16"> Geoman</a></span>
							</div>
						</article>
<?php $counter++; } ?>
					</section>
					<section class="posts mods">
						<div class="title">
							<h2><i class="icon-mod"></i>Minecraft PE Mods</h2>
							<h4>Add functionality to your game by adding some mods!</h4>
						</div>
<?php $counter = 1; while ( $counter <= 3 ) { ?>
						<article>
							<div class="screenshot">
								<a href="#like" class="likes"><i class="icon-heart"></i> 43</a>
								<a href="#"><img src="./assets/img/DEMO_POST<?php echo $counter; ?>.png" alt="" width="240" height="180"></a>
							</div>
							<div class="info">
								<span class="details"><ul>
									<li><a href="/maps/adventure">World Mod</a></li>
									<li>5,891 views</li>
								</ul></span>
								<a href="#"><h2>0.12.3!!!! Dungeon Portal Mod!!! With Gravestone!!!!</h2></a>
								<span class="author"><a href="#"><img src="./assets/img/DEMO_AVATAR.png" alt="" width="16" height="16"> Geoman</a></span>
							</div>
						</article>
<?php $counter++; } ?>
					</section>
				</div>

			</div>
		</section>

		<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
	</body>
</html>