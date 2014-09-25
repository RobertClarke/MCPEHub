<?php

/**
  * User Dashboard
**/

require_once('core.php');

$pi = [
	'title_main'		=> 'Dashboard',
	'title_sub'			=> 'Account'
];

show_header('Dashboard', TRUE, $pi);

$error->add('WELCOME',	'Welcome back to MCPE Hub, <b>'.$user->info('username').'</b>!', 'info');
$error->add('DELETED_POST', 'Your post has been deleted.', 'success');

if ( isset( $_GET['login'] ) ) $error->set('WELCOME');
else if ( isset( $_GET['deleted'] ) ) $error->force('DELETED_POST');

$error->add('MISSING',		'You haven\'t made any submissions yet. Contribute to our community by sharing your MCPE content!', 'warning');
$error->add('MISSING_TYPE',	'You haven\'t made any submissions in this category yet.', 'warning');

$current_page = ( isset($_GET['page']) ) ? $_GET['page'] : 1;

$allowed_types	= ['map', 'seed', 'texture', 'skin', 'mod', 'server'];
$q_cols			= 'id, title, slug, author, images, active, edited, submitted, featured';
$q_where		= 'author = "'.$user->info('id').'" AND active <> "-2"';

// Show specific type of post.
if ( isset($_GET['show']) && in_array($_GET['show'], $allowed_types) ) {
	
	// No need to escape since verified above.
	$type = $_GET['show'];
	
	$posts_count = $db->query('
		(SELECT "map"	 	AS type, COUNT(*) FROM `content_maps` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "seed" 		AS type, COUNT(*) FROM `content_seeds` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "texture" 	AS type, COUNT(*) FROM `content_textures` WHERE '.$q_where.') UNION ALL
		(SELECT "skin" 		AS type, COUNT(*) FROM `content_skins` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "mod" 		AS type, COUNT(*) FROM `content_mods` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "server" 	AS type, COUNT(*) FROM `content_servers`  WHERE '.$q_where.')
	')->fetch();
	
	// Grab specific post created by user.
	$posts = $db->query('SELECT "'.$type.'" AS type, '.$q_cols.' FROM `content_'.$type.'s` WHERE '.$q_where.'')->fetch();
	$url->add('show', $type);
	
} else { // Show all posts, no specific post requested.
	
	$posts = $db->query('
		(SELECT "map"	 	AS type, '.$q_cols.' FROM `content_maps` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "seed" 		AS type, '.$q_cols.' FROM `content_seeds` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "texture" 	AS type, '.$q_cols.' FROM `content_textures` WHERE '.$q_where.') UNION ALL
		(SELECT "skin" 		AS type, '.$q_cols.' FROM `content_skins` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "mod" 		AS type, '.$q_cols.' FROM `content_mods` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "server" 	AS type, '.$q_cols.' FROM `content_servers`  WHERE '.$q_where.')
	')->fetch();

}

// Count total number of database returns.
$posts_num = $db->affected_rows;

// Check if posts are found.
if ( isset( $type ) && $posts_num == 0 ) $error->set('MISSING_TYPE'); 
else if ( $posts_num == 0 ) $error->set('MISSING');

// Count each post type.
$num = ['all' => 0, 'map' => 0, 'seed' => 0, 'texture' => 0, 'skin' => 0, 'mod' => 0, 'server' => 0];

// This counting is done if a specific post is selected and we want to count all of the post types for nav.
if ( isset( $posts_count ) ) {
	foreach ( $posts_count as $post ) {
		$num[ $post['type'] ] = $post['COUNT(*)'];
		$num['all'] = $num['all'] + $post['COUNT(*)'];
	}
	
} else { // Default counting.
	foreach ( $posts as $post ) {
		$num[ $post['type'] ]++;
		$num['all']++;
	}
}

$action = isset($_GET['action'])?$_GET['action']:NULL;

// Start action switch.
switch( $action ) {

// Case: deleting posts.
case 'delete':
	
	$error->add( 'MISSING_INPUTS', 'Missing or invalid input values to modify this post.', 'error', 'times' );
	$error->add( 'INVALID_POST', 'The post you\'re attempting to modify doesn\'t exist.', 'error', 'times' );
	$error->add( 'NOT_OWNED', 'You don\'t have permission to modify this post.', 'error', 'lock' );
	$error->add( 'ALREADY_DELETED', 'That post has already been marked as deleted.', 'warning', 'exclamation-triangle' );
	
	$error->reset();
	
	// Check if $_GET values present and valid.
	if ( empty( $_GET['post'] ) || empty( $_GET['type'] ) || !in_array( $_GET['type'], $allowed_types ) ) $error->set( 'MISSING_INPUTS' );
	else {
		
		$post_id = $db->escape( $_GET['post'] );
		$post_type = $_GET['type'];
		
		// Check if post exists in database.
		$post = $db->from( 'content_'. $post_type .'s' )->where( array( 'id' => $post_id ) )->fetch();
		
		if ( !$db->affected_rows ) $error->set( 'INVALID_POST' );
		else {
			
			$post = $post[0];
			
			// Check if user owns the post.
			if ( $post['author'] != $user->info('id') ) $error->set( 'NOT_OWNED' );
			else {
				
				// Check if post not already deleted.
				if ( $post['active'] == '-2' ) $error->set( 'ALREADY_DELETED' );
				else {
					
					// Mark post as deleted.
					$db_where = array( 'id' => $post['id'] );
					$db_update = array( 'active' => '-2', 'editor_id' => $user->info('id') );
					
					$db->where( $db_where )->update( 'content_'.$post_type.'s', $db_update );
					
					redirect( $url->show('deleted') );
					
				} // END: Post not already deleted.
				
			} // END: User owns the post.
			
		} // END: Post exists in database.
		
	} // END: $_GET values present and valid.
	
break; // END: Deleting posts.

} // End action switch.

// If posts are found.
if ( $posts_num != 0 ) {
	
	$offset	= $pagination->build($posts_num, 10, $current_page);
	
	// Slice posts into array to be sorted by submission date.
	$sliced = array();
	foreach ( $posts as $key => $col ) $sliced[$key] = $col['submitted'];
	array_multisort( $sliced, SORT_DESC, $posts );
	
	$posts = array_slice($posts, $offset, 10);
	
	// Set up icons for post types.
	$icons = array( 'map' => 'map-marker', 'seed' => 'leaf', 'texture' => 'magic', 'skin' => 'smile-o', 'mod' => 'codepen', 'server' => 'gamepad' );
	
	// Set up badges for status.
	$status_badge = array(
		'-1' => '<div class="status red"><i class="fa fa-times fa-fw"></i> Rejected</div>',
		'0' => '<div class="status yellow"><i class="fa fa-clock-o fa-fw"></i> Under Review</div>',
		'1' => '<div class="status green"><i class="fa fa-check fa-fw"></i> Published</div>',
	);
	
} // End: If posts are found.

// Set "sort" URLs in array for easy editing.
$sort_urls = '';
$sort_url = array(
	'map' => array( 'Maps', 'map-marker' ),
	'seed' => array( 'Seeds', 'leaf' ),
	'texture' => array( 'Textures', 'magic' ),
	'skin' => array( 'Skins', 'smile-o' ),
	'mod' => array( 'Mods', 'codepen' ),
	'server' => array( 'Servers', 'gamepad' ),
);

$active = ( !isset( $type ) ) ? 'active ' : '';
$sort_urls .= '<a href="'.$url->show('', TRUE).'" class="'.$active.'bttn"><i class="fa fa-align-justify"></i> All <span>'.$num['all'].'</span></a>';

foreach( $sort_url as $id => $p ) {
	$active = ( isset( $type ) && $id == $type ) ? 'active ' : '';
	$sort_urls .= '<a href="'.$url->show('show='.$id, TRUE).'" class="'.$active.'bttn"><i class="fa fa-'.$p[1].'"></i> '.$p[0].' <span>'.$num[$id].'</span></a>';
}



//$count	= $db->select('COUNT(*) AS count')->from('content_maps')->where(['active' => 1])->fetch()[0]['count'];
//$offset	= $pagination->build($count, 10, $current_page);

?>

<div id="page-title">
    <h1>Dashboard</h1>
    <div class="links">
        <a href="/account" class="bttn"><i class="fa fa-gears"></i> Account Settings</a>
        <a href="/submit" class="bttn green"><i class="fa fa-plus"></i> Submit Content</a>
    </div>
</div>

<?php $error->display(); ?>

<div class="sort"><?php echo $sort_urls; ?></div>

<?php

if ( $posts_num != 0 ) { // If posts exist, show them. ?>

<div class="posts compact">

<?php

foreach( $posts as $post ) {
	
	$post['auth']	= $user->info('username', $post['author']);
	$post['url']	= '/map/'.$post['slug'];
	$post['url_a']	= '/user/'.$post['auth'];
	
	$post['images']		= explode(',', $post['images']);
	$post['thumb']		= '/uploads/700x200/maps/'.urlencode($post['images'][0]);
	
	$post['featured_html'] = ( $post['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star"></i> Featured</div>' : NULL;
	
echo '
<div class="post">
    <div class="img">
        <div class="overlay">
            <h2><a href="'.$post['url'].'">'.$post['title'].'</a></h2>
            '.$post['featured_html'].'
        </div>
        <a href="'.$post['url'].'"><img src="'.$post['thumb'].'" alt="'.$post['title'].'" width="700" height="50"></a>
    </div>
    <div class="info">
        <ul>
            <li class="solo"><i class="fa fa-pencil"></i> <a href="/edit?post='.$post['id'].'&type='.$post['type'].'">Edit Post</a></li>
            <li class="solo"><i class="fa fa-trash"></i> <a href="/" class="del">Delete Post</a></li>
            <li class="solo"><i class="fa fa-upload"></i> '.since(strtotime($post['submitted'])).'</li>
        </ul>
        <div class="link"><a href="'.$post['url'].'" class="button dl silver"><i class="fa fa-eye"></i> View Post</a></div>
    </div>
    <div class="delconf">
        <span>Are you sure you want to delete this '.$post['type'].'?</span><br><br>
        <a href="'.$url->show('action=delete&post='.$post['id'].'&type='.$post['type']).'" class="bttn red">Confirm Delete</a>
    </div>
</div>';
	
}

?>

</div>

<?php $pagination->html(); ?>

<?php } // End: If posts exist, show them. ?>

<?php show_footer(); ?>