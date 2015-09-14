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

$types = post_type_code();

$query_posts = $query_count = 'SELECT * FROM (';

// Build query statement depending on types being fetched
foreach( $types as $name => $id ) {

	$query_count .= '
	(
		SELECT "'.$name.'" AS type, COUNT(*) AS count
		FROM content_'.$name.' post
		WHERE post.status = "1"
	) UNION ALL';

	$query_posts .= '
	(
		SELECT "'.$name.'" AS type,
			post.id, post.title, post.slug, post.author_id, post.status, post.submitted,
			featured.post_id, featured.post_type, featured.date AS featured,
			(SELECT COUNT(*) FROM likes WHERE post_id = post.id AND post_type = '.$id.') AS likes,
			GROUP_CONCAT(filename) AS images,
			(SELECT filename FROM content_images WHERE post_id = post.id AND post_type = '.$id.' AND featured = 1 LIMIT 1) AS image_featured
		FROM content_'.$name.' post
		INNER JOIN content_featured featured
			ON post.id = featured.post_id AND featured.post_type = '.$id.'
	 	LEFT OUTER JOIN content_images images
			ON post.id = images.post_id AND images.post_type = '.$id.'
		WHERE post.status = 1
		GROUP BY post.id
		LIMIT 4
	) UNION ALL';

}

// Trim off last UNION ALL from statements
$query_count = rtrim($query_count, ' UNION ALL');
$query_posts = rtrim($query_posts, ' UNION ALL');

// Determine # of each type of post
$query_count = $query_count . ') AS posts';
$db_count = $db->query($query_count)->fetch();

// Storing post counts
$posts_count = [];
foreach ( $db_count as $c ) {
	$posts_count[ $c['type'] ] = $c['count'];
}

$query_posts = $query_posts . ') AS posts ORDER BY featured DESC';
$db_posts = $db->query($query_posts)->fetch();

// Organize posts by post type
$posts = [];

foreach ( $db_posts as $post ) {

	$post['images'] = explode(',', $post['images']);

	// Add featured image, if missing or not specified in database
	if ( !isset($post['image_featured']) )
		$post['image_featured'] = $post['images'][0];

	// Get author info
	$post['auth'] = new User($post['author_id']);
	$post['author'] = $post['auth']->username;
	$post['author_avatar'] = $post['auth']->data['avatar'];

	$posts[ $post['type'] ][] = $post;

}

?>
<div id="featured">
	<div class="wrapper">
<?php

$posts_top = '';

foreach ( $types as $type => $id ) {
	if ( in_array($type, ['map', 'seed', 'texture', 'mod', 'server']) && isset($posts[$type]) ) {

		// Pick out a random post from each category
		$post = ( count($posts[$type]) > 1 ) ? $posts[$type][ rand(1, count($posts[$type]))-1 ] : $posts[$type][0];

?>
		<a href="/<?php echo $type.'/'.$post['slug']; ?>">
			<article class="<?php echo $type; echo ( $type == 'map' ) ? ' big' : ''; ?>">
				<header>
					<p class="type">Featured <?php echo ucwords($type); ?></p>
					<h1><?php echo $post['title']; ?></h1>
				</header>
				<div class="info">
					<p><img src="/avatar/20x20/<?php echo $post['author_avatar']; ?>" alt="<?php echo $post['author']; ?>" width="20" height="20"> <?php echo $post['author']; ?></p>
					<p class="likes"><i class="icon-thumbs-up"></i> <?php echo $post['likes']; ?></p>
				</div>
				<img src="/uploads/<?php echo ( $type == 'map' ) ? '500x280' : '240x140'; ?>/<?php echo $type; ?>/<?php echo $post['image_featured']; ?>" alt="<?php echo $post['title']; ?>" width="<?php echo ( $type == 'map' ) ? '500' : '240'; ?>" height="<?php echo ( $type == 'map' ) ? '280' : '140'; ?>" class="screen">
			</article>
		</a>
<?php } } ?>
	</div>
</div>
<div id="ad-homepage">
	<div class="wrapper">
		<div class="g-ad"></div>
	</div>
</div>
<?php

$i = 0; // Counter for applying "alt" classes

foreach ( $types as $type => $id ) {

	$type_s = $type.'s';

	if ( $type == 'blog' )
		$type_s = 'news';

?>
<div id="<?php echo $type_s; ?>" class="featured-posts <?php echo $type_s; echo ( $i % 2 !== 0 ) ? ' alt' : ''; ?>">
	<div class="wrapper">
		<h2>Featured Community <?php echo ucwords($type_s); ?></h2>
<?php
	if ( array_key_exists($type, $posts) ) {
		foreach ( $posts[$type] as $post ) {
?>
		<article><a href="/<?php echo $type; ?>/<?php echo $post['slug']; ?>">
			<div class="image">
				<div class="info">
					<p><img src="/avatar/20x20/<?php echo $post['author_avatar']; ?>" alt="<?php echo $post['author']; ?>" width="20" height="20"> <?php echo $post['author']; ?></p>
					<p class="likes"><i class="icon-thumbs-up"></i> <?php echo $post['likes']; ?></p>
				</div>
				<img src="/uploads/240x140/<?php echo $type; ?>/<?php echo $post['image_featured']; ?>" alt="<?php echo $post['title']; ?>" width="234" height="135" class="screen">
			</div>
			<header>
				<h1><?php echo $post['title']; ?></h1>
				<h3>Challenging, yet fun map with two dungeons, spawners &amp; more!</h3>
			</header>
		</a></article>
<?php } } ?>
	</div>
	<div class="more"><a href="/<?php echo $type_s; ?>">Browse <?php echo ucwords($type_s); ?> (<?php echo $posts_count[$type]; ?>)</a></div>
</div>
<?php $i++; } ?>
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