<?php

require_once( 'core.php' );
show_header( 'Dashboard', TRUE, '', 'dash' );

$action = isset( $_GET['action'] ) ? $_GET['action'] : '';

$error->add( 'LOGGED_IN', 'Welcome back to MCPE Hub, <b>'. $user->info('username') .'</b>!', 'info', 'smile-o' );
$error->add( 'DELETED_POST', 'Your post has been deleted.', 'success', 'trash-o' );
$error->add( 'NO_POSTS', 'You haven\'t made any submissions yet. Contribute to our community by sharing your MCPE content!', 'warning', 'frown-o' );
$error->add( 'NO_POSTS_SPECIFIC', 'You haven\'t made any submissions in this category yet.', 'warning', 'frown-o' );

if ( isset( $_GET['logged_in'] ) ) $error->force( 'LOGGED_IN' );
else if ( isset( $_GET['deleted'] ) ) $error->force( 'DELETED_POST' );

$allowed_types = array( 'map', 'seed', 'texture', 'skin', 'mod', 'server' );

// Set default values for database query.
$q_cols = 'id, title, slug, author, images, active, edited, submitted';
$q_where = 'author = "'. $user->info('id') .'" AND active <> "-2"';

// Queries depend on the type of request (all/single type).
if ( isset( $_GET['show'] ) && in_array( $_GET['show'], $allowed_types ) ) {
	
	$type = $_GET['show'];
	
	// Count each kind of post, since we aren't running the other query.
	$posts_count = $db->query('
		(SELECT "map"	 	AS type, COUNT(*) FROM `content_maps` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "seed" 		AS type, COUNT(*) FROM `content_seeds` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "texture" 	AS type, COUNT(*) FROM `content_textures` WHERE '.$q_where.') UNION ALL
		(SELECT "skin" 		AS type, COUNT(*) FROM `content_skins` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "mod" 		AS type, COUNT(*) FROM `content_mods` 	  WHERE '.$q_where.') UNION ALL
		(SELECT "server" 	AS type, COUNT(*) FROM `content_servers`  WHERE '.$q_where.')
	')->fetch();
	
	// Grab specific post created by user.
	$posts = $db->query( 'SELECT "'.$type.'" AS type, '.$q_cols.' FROM `content_'.$type.'s` WHERE '.$q_where.'' )->fetch();
	
	// Set post type for URL.
	$c_url->add( 'show', $type );
	
} else { // Show all posts.
	
	// Grab all posts created by user.
	$posts = $db->query('
		(SELECT "map"	 	AS type, '.$q_cols.' FROM `content_maps` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "seed" 		AS type, '.$q_cols.' FROM `content_seeds` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "texture" 	AS type, '.$q_cols.' FROM `content_textures` WHERE '.$q_where.') UNION ALL
		(SELECT "skin" 		AS type, '.$q_cols.' FROM `content_skins` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "mod" 		AS type, '.$q_cols.' FROM `content_mods` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "server" 	AS type, '.$q_cols.' FROM `content_servers`  WHERE '.$q_where.')
	')->fetch();

} // END: Show all posts.

// Number of posts retrieved from database.
$posts_num = $db->affected_rows;

// If posts are found for the given user.
if ( isset( $type ) && $posts_num == 0 ) $error->set( 'NO_POSTS_SPECIFIC' ); 
else if ( $posts_num == 0 ) $error->set( 'NO_POSTS' );


// Count numbers of each post.
$num = array( 'all' => 0, 'map' => 0, 'seed' => 0, 'texture' => 0, 'skin' => 0, 'mod' => 0, 'server' => 0 );

// This counting is done if a specific post is selected and we want
// to count all of the post types for nav.
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


if ( $posts_num != 0 ) {
	
	// Set number of posts per page.
	$posts_per_page = 5;
	
	// ** Pagination Setup ** //
	$total_pages = ceil( $posts_num / $posts_per_page );
	
	// Make sure page number requested is valid.
	$page = ( !empty( $_GET['page'] ) && is_numeric( $_GET['page'] ) ) ? (int)$_GET['page'] : 1;
	if ( $page > $total_pages ) $page = $total_pages;
	if ( $page < 1 ) $page = 1;
	
	// Set page number for URL.
	if ( $page != 1 ) $c_url->add( 'page', $page );
	
	// Offset for query.
	$offset = ($page - 1) * $posts_per_page;
	
	// Slice posts into array to be sorted by submission date.
	$sliced = array();
	foreach ( $posts as $key => $col ) $sliced[$key] = $col['submitted'];
	array_multisort( $sliced, SORT_DESC, $posts );
	
	$posts = array_slice( $posts, $offset, $posts_per_page );
	
	// ** Pagination HTML Generation ** //
	if ( $total_pages > 1 ) {
		
		$pagination_html = '';
		
		// Set range of pagination links.
		$range = 2;
		
		// Back link.
		if ( $page > 1 ) $pagination_html .= '<li><a href="'. $c_url->show('page='.($page - 1)) .'"><i class="fa fa-chevron-circle-left"></i></a></li>';
		
		// Page links.
		for ( $x = ($page - $range); $x < ( ($page + $range) + 1 ); $x++ ) {
			if ( ($x > 0) && ($x <= $total_pages) ) {
				$pagination_html .= ( $x == $page ) ? '<li class="active">' : '<li>';
				$pagination_html .= '<a href="'. $c_url->show('page='.$x) .'">'.$x.'</a></li>';
			} 
		}
		
		// Forward link.
		if ( $page != $total_pages ) $pagination_html .= '<li><a href="'. $c_url->show('page='.($page + 1)) .'"><i class="fa fa-chevron-circle-right"></i></a></li>';
		
		// Adding final <div> and <ul> tags to HTML markup.
		$pagination_html = '<div class="pagination"><ul>'. $pagination_html .'</ul></div>';
		
	}
	
	// Set up icons for post types.
	$icons = array( 'map' => 'map-marker', 'seed' => 'leaf', 'texture' => 'magic', 'skin' => 'smile-o', 'mod' => 'codepen', 'server' => 'gamepad' );
	
	// Set up badges for status.
	$status_badge = array(
		'-1' => '<div class="status red"><i class="fa fa-times fa-fw"></i> Rejected</div>',
		'0' => '<div class="status yellow"><i class="fa fa-clock-o fa-fw"></i> Under Review</div>',
		'1' => '<div class="status green"><i class="fa fa-check fa-fw"></i> Published</div>',
	);
	
} // END: Posts exist for the given user.

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
					
					redirect( $c_url->show('deleted') );
					
				} // END: Post not already deleted.
				
			} // END: User owns the post.
			
		} // END: Post exists in database.
		
	} // END: $_GET values present and valid.
	
break; // END: Deleting posts.

} // End action switch.


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
$sort_urls .= '<a href="'.$c_url->show('', TRUE).'" class="'.$active.'bttn"><i class="fa fa-book"></i> All <span>'.$num['all'].'</span></a>';

foreach( $sort_url as $id => $p ) {
	$active = ( isset( $type ) && $id == $type ) ? 'active ' : '';
	$sort_urls .= '<a href="'.$c_url->show('show='.$id, TRUE).'" class="'.$active.'bttn"><i class="fa fa-'.$p[1].'"></i> '.$p[0].' <span>'.$num[$id].'</span></a>';
}

// Showing a message if no other errors.
if ( empty( $error->selected ) && $posts_num != 0 ) {
	
	$error->add( 'NEWS', '<strong>NEWS: We\'ve enabled editing posts!</strong> Feel free to make any changes to any of your posts now. Enjoy! :)', 'info', 'bullhorn' );
	$error->set( 'NEWS' );
	
}

?>

<div id="page-title">
    <h2>Dashboard</h2>
    <ul class="tabs">
        <a href="/account" class="bttn"><i class="fa fa-gears"></i> Account Settings</a>
        <a href="/submit" class="bttn green"><i class="fa fa-plus"></i> Submit Content</a>
    </ul>
</div>

<div class="sort"><?php echo $sort_urls; ?></div>

<?php $error->display(); ?>
<?php if ( $posts_num != 0 ) { // START: If posts exist. ?>

<div class="posts">
<?php

	foreach( $posts as $post ) { // START: Post foreach.
		
		// Grab first image from images for display.
		$post['images'] = explode( ',', $post['images'] );
		$post['image'] = urlencode( $post['images'][0] );
		
		// Pre-built image URL.
		$post['image_url'] = './uploads/140x100/'.$post['type'].'s/'.$post['image'];
		
	?>

    <div class="post clearfix">
        <div class="preview">
            <div class="badge-type <?php echo $post['type']; ?>"><i class="fa fa-<?php echo $icons[ $post['type'] ]; ?>"></i> <?php echo $post['type']; ?></div>
            <?php echo $status_badge[ $post['active'] ]; ?>
            <img src="<?php echo $post['image_url']; ?>" />
        </div>
        <div class="details">
            <h2><a href="/<?php echo $post['type']; ?>/<?php echo $post['slug']; ?>"><?php echo $post['title']; ?></a></h2>
            <div class="actions">
                <a href="/edit?post=<?php echo $post['id']; ?>&type=<?php echo $post['type']; ?>" class="bttn"><i class="fa fa-pencil"></i> Edit Post</a>
                <a href="/dashboard" class="bttn right del tip" data-tip="Delete"><i class="fa fa-trash-o no-space"></i></a>
                <a href="/<?php echo $post['type']; ?>/<?php echo $post['slug']; ?>" class="bttn right tip" data-tip="Preview" target="_blank"><i class="fa fa-eye no-space"></i></a>
            </div>
        </div><div class="clear"></div>
        <div class="delconf">
            <span>Are you sure you want to delete this <?php echo $post['type']; ?>?</span>
            <a href="<?php echo $c_url->show('action=delete&post='.$post['id'].'&type='.$post['type']); ?>" class="bttn red">Confirm Delete</a>
        </div>
    </div>

<?php } // END: Post foreach. ?>
<?php if ( isset( $pagination_html ) ) echo $pagination_html; ?>
</div>

<?php } // END: If posts exist. ?>

<?php show_footer(); ?>