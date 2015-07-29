<?php

/**
 * Homepage
 *
 * The website homepage, where all featured posts are displayed
 * for users who first visit the site.
**/

require_once('loader.php');

$page->body_id = 'homepage';
$page->no_wrap = true;
$page->title_h1 = 'MCPE Hub';
$page->title_h2 = 'The #1 Minecraft PE Community';

$page->header();

?>
<div id="featured">
	<div class="wrapper">
		<a href="#"><article class="map big">
			<header>
				<p class="type">Featured Map</p>
				<h1>8 villages with stronghold</h1>
			</header>
			<div class="info">
				<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
				<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
			</div>
			<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="500" height="280" class="screen">
		</article></a>
		<a href="#"><article class="seed">
			<header>
				<p class="type">Featured Seed</p>
				<h1>HydroLandMCPE</h1>
			</header>
			<div class="info">
				<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
				<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
			</div>
			<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="250" height="140" class="screen">
		</article></a>
		<a href="#"><article class="texture">
			<header>
				<p class="type">Featured Texture</p>
				<h1>Adventure Time Craft BETA 5</h1>
			</header>
			<div class="info">
				<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
				<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
			</div>
			<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="250" height="140" class="screen">
		</article></a>
		<a href="#"><article class="mod">
			<header>
				<p class="type">Featured Mod</p>
				<h1>Power Pills Addon!</h1>
			</header>
			<div class="info">
				<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
				<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
			</div>
			<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="250" height="140" class="screen">
		</article></a>
		<a href="#"><article class="server">
			<header>
				<p class="type">Featured Server</p>
				<h1>FuzionCraft PVP Server!</h1>
			</header>
			<div class="info">
				<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
				<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
			</div>
			<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="250" height="140" class="screen">
		</article></a>
	</div>
</div>
<div id="ad-homepage">
	<div class="wrapper">
		<div class="g-ad"></div>
	</div>
</div>
<div id="maps" class="featured-posts maps">
	<div class="wrapper">
		<h2>Featured Community Maps</h2>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
	</div>
	<div class="more"><a href="/maps">Browse Maps (5243)</a></div>
</div>
<div id="seeds" class="featured-posts seeds alt">
	<div class="wrapper">
		<h2>Featured Community Seeds</h2>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
	</div>
	<div class="more"><a href="/seeds">Browse Seeds (5243)</a></div>
</div>
<div id="textures" class="featured-posts textures">
	<div class="wrapper">
		<h2>Featured Community Textures</h2>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
	</div>
	<div class="more"><a href="/textures">Browse Textures (5243)</a></div>
</div>
<div id="skins" class="featured-posts skins alt">
	<div class="wrapper">
		<h2>Featured Community Skins</h2>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
	</div>
	<div class="more"><a href="/skins">Browse Skins (5243)</a></div>
</div>
<div id="mods" class="featured-posts mods">
	<div class="wrapper">
		<h2>Featured Community Mods</h2>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
	</div>
	<div class="more"><a href="/mods">Browse Mods (5243)</a></div>
</div>
<div id="servers" class="featured-posts servers alt">
	<div class="wrapper">
		<h2>Featured Community Servers</h2>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
	</div>
	<div class="more"><a href="/servers">Browse Servers (5243)</a></div>
</div>
<div id="news" class="featured-posts news">
	<div class="wrapper">
		<h2>Minecraft PE News</h2>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
		<article>
			<div class="image">
				<div class="info">
					<p><img src="/assets/img/DEMO_AVATAR.jpg" alt="" width="20" height="20"> anatolie</p>
					<p class="likes"><i class="icon-thumbs-up"></i> 35</p>
				</div>
				<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="234" height="135" class="screen">
			</div>
			<header>
				<h1>Plot MC - Pocket Edition</h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</article>
	</div>
	<div class="more"><a href="/blog">Browse News (5243)</a></div>
</div>
<div id="stats">
	<div class="wrapper">
		<h2>Our Community</h2>
		<p>Our community statistics, updated every hour</p>
		<div class="stat"><b class="countUp" data-count="83647">0</b> Members</div>
		<div class="stat"><b class="countUp" data-count="12732">0</b> Posts</div>
		<div class="stat"><b class="countUp" data-count="1738294">0</b> Post Views</div>
		<div class="stat"><b class="countUp" data-count="1203020">0</b> Downloads</div>
	</div>
</div>
<?php $page->footer(); ?>