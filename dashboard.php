<?php

/**
 * Dashboard
 *
 * The user dashboard is used to give users an overview of their
 * posts and account.
**/

require_once('loader.php');

$page->auth = true;
$page->body_id = 'dashboard';
$page->alt_body = true;
$page->title_h1 = 'Dashboard';
$page->title_h2 = 'An overview of your account';

$page->header('Dashboard');

// Post types for counters
$types = [
	'map'		=> 1,
	'seed'		=> 2,
	'texture'	=> 3,
	'skin'		=> 4,
	'mod'		=> 5,
	'server'	=> 6
];

// Post status codes
$status = [
	-1		=> 'rejected',
	0		=> 'pending',
	1		=> 'approved'
];

// URL parameters for filtering
$url_type = filter_input(INPUT_GET, 'type');
$url_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);

// Showing one specific post type on $_GET request
if ( !empty($url_type) && array_key_exists($url_type, $types) )
	$types = [$url_type => $types[$url_type]];

$query_posts = $query_count = 'SELECT * FROM (';

// Build query statement depending on types being fetched
foreach( $types as $name => $id ) {

	// Tag for querying the database for download counts
	$dl_tag = ( $name == 'server' || $name == 'seed' ) ? 'NULL' : 'post.downloads';

	$query_count .= '
	(
		SELECT "'.$name.'" AS type, COUNT(*) AS count, GROUP_CONCAT(id) AS ids
		FROM content_'.$name.' post
		WHERE post.author_id = 1 AND post.status <> "-2"
	) UNION ALL';

	$query_posts .= '
	(
		SELECT
			"'.$name.'" AS type,
			post.id, post.title, post.slug, post.author_id, post.status, post.views, post.submitted,
			'.$dl_tag.' AS downloads,
			(SELECT COUNT(*) FROM likes WHERE post_id = post.id AND post_type = '.$id.') AS likes,
			(SELECT COUNT(*) FROM comments WHERE post_id = post.id AND post_type = '.$id.' AND status = 1) AS comments,
			(SELECT COUNT(*) FROM content_featured WHERE post_id = post.id AND post_type = '.$id.') AS featured,
			GROUP_CONCAT(filename ORDER BY img.post_id) AS images
		FROM content_'.$name.' post
		LEFT OUTER JOIN content_images img ON
			img.post_id = post.id AND
			img.post_type = '.$id.'
		WHERE post.author_id = 1 AND post.status <> "-2"
		GROUP BY post.id
	) UNION ALL';

}

// Trim off last UNION ALL from statements
$query_count = rtrim($query_count, ' UNION ALL');
$query_posts = rtrim($query_posts, ' UNION ALL');

// Determine # of each type of post
$query_count = $query_count . ') AS posts';
$posts_count_db = $db->query($query_count)->fetch();

$posts_total = 0;
$posts_count = [];
$posts_id = [];

foreach ( $posts_count_db as $c ) {
	$posts_count[$c['type']] = $c['count'];
	$posts_id[ $types[$c['type']] ] = $c['ids'];
	$posts_total += $c['count'];
}

// Grab posts with pagination offset
$offset = pagination_offset($posts_total, 10, $url_page);

$query_posts = $query_posts . ') AS posts ORDER BY submitted DESC LIMIT '.$offset.', 10';
$posts = $db->query($query_posts)->fetch();

if ( $posts_total != 0 ) {

	// Get user stats from the database
	$query_stats = '
	(
		SELECT SUM(views) AS views, SUM(downloads) AS downloads FROM (
			SELECT views, downloads FROM content_map WHERE author_id = 1 AND status <> "-2" UNION ALL
			SELECT views, downloads FROM content_mod WHERE author_id = 1 AND status <> "-2" UNION ALL
			SELECT views, 0 AS downloads FROM content_seed WHERE author_id = 1 AND status <> "-2" UNION ALL
			SELECT views, 0 AS downloads FROM content_server WHERE author_id = 1 AND status <> "-2" UNION ALL
			SELECT views, downloads FROM content_skin WHERE author_id = 1 AND status <> "-2" UNION ALL
			SELECT views, downloads FROM content_texture WHERE author_id = 1 AND status <> "-2"
		) AS stats
	)';

	// Build query for fetching total likes count
	$query_likes = '';
	foreach ( $posts_id as $type => $ids ) {
		if ( !empty($ids) )
			$query_likes .= 'SELECT COUNT(*) AS likes FROM likes WHERE post_id IN ('.$ids.') AND post_type = '.$type.' UNION ALL ';
	}
	$query_likes = rtrim($query_likes, ' UNION ALL ');
	$query_likes = 'SELECT SUM(likes) AS count FROM ('.$query_likes.') AS likes';

	// Get stat counters from database
	$stats = $db->query($query_stats)->fetch_first();
	$likes = $db->query($query_likes)->fetch_first();

} else {

	$stats['views'] = $stats['downloads'] = $likes['count'] = 0;

}

// Get follower count
$query_followers = 'SELECT COUNT(*) AS count FROM following WHERE user_following = 1';
$followers = $db->query($query_followers)->fetch_first();

?>
<div id="stats">
	<p><b><?php echo number_format($stats['views']); ?></b> Post Views</p>
	<p><b><?php echo number_format($likes['count']); ?></b> Post Likes</p>
	<p><b><?php echo number_format($stats['downloads']); ?></b> Downloads</p>
	<p><b><?php echo number_format($followers['count']); ?></b> Followers</p>
</div>

</div></section>

<section id="content">
	<div class="wrapper">
<?php if ( $posts_total != 0 ) { // If posts exist ?>
		<header class="main_title">
			<h1>My Posts</h1>
			<nav>
				<ul>
					<li><a href="/submit" class="bttn green"><i class="icon-upload"></i> Submit Content</a></li>
				</ul>
			</nav>
		</header>
		<div id="posts">
<?php foreach ( $posts as $post ) { echo '
	<article>
		<header>
			<div class="title">
				<p class="type '.$post['type'].'">'.ucwords($post['type']).'</p>
				<a href="#"><h1>'.$post['title'].'</h1></a>
				<div class="status">
					'.( ($post['featured'] == 1) ? '<span class="featured"><i class="icon-trophy"></i> Featured</span>' : '' ).'
					<span class="'.$status[$post['status']].'"><i class="icon-'.$status[$post['status']].'"></i> '.ucwords( $status[$post['status']] ).'</span>
				</div>
			</div>
			<img src="/assets/img/DEMO_IMAGE.jpg" alt="" width="700" height="100" class="screen">
		</header>
		<div class="info">
			<div class="stats'.( (!isset($post['downloads'])) ? ' triple' : '' ).'">
				<span><b>'.number_format($post['views']).'</b> Views</span>
				'.( (isset($post['downloads'])) ? '<span><b>'.number_format($post['downloads']).'</b> Downloads</span>' : '' ).'
				<span><b>'.number_format($post['likes']).'</b> Likes</span>
				<span><b>'.number_format($post['comments']).'</b> Comments</span>
			</div>
			<div class="actions">
				<a href="/'.$post['type'].'/'.$post['slug'].'"><i class="icon-link"></i> Share</a>
				<a href="/dashboard-edit?post='.$post['id'].'&type='.$post['type'].'"><i class="icon-pencil"></i> Edit Post</a>
				<a href="/dashboard-delete?post='.$post['id'].'&type='.$post['type'].'" class="right delete"><i class="icon-trash"></i> Delete</a>
			</div>
		</div>
	</article>
'; } // End post foreach ?>
		</div>
<?php } else { // If no posts ?>
	<div class="fullmessage">
		<h2>You haven't posted</h2>
		<p>Contribute to the community by sharing your MCPE content!</p>
		<a href="/submit" class="bttn green xl">Submit Content Now</a>
	</div>
<?php } ?>
	</div>
</section>
<?php $page->footer(); ?>