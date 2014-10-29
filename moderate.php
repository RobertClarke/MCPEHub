<?php

require_once( 'core.php' );

// Only allow admins/mods on this page!
if ( !$user->is_admin() && !$user->is_mod() ) {
	if ( $user->logged_in() ) redirect('/');
	else redirect('/login?auth_req');
}

show_header( 'Moderate Posts', TRUE );

$action = isset( $_GET['action'] ) ? $_GET['action'] : '';

$error->add( 'NO_POSTS', 'There are currently no pending posts awaiting moderation right now.', 'success', 'smile-o' );
$error->add( 'NO_POSTS_SPECIFIC', 'There are currently no pending posts in this category.', 'info', 'gavel' );

$error->add( 'APPROVED_POST', 'The post has been approved.', 'success', 'check' );
$error->add( 'REJECTED_POST', 'The post has been rejected.', 'success', 'times' );
$error->add( 'DELETED_POST', 'The post has been deleted.', 'success', 'trash-o' );

$error->add( 'MISSING_INPUTS', 'Missing or invalid input values to modify this post.', 'error', 'times' );
$error->add( 'INVALID_POST', 'The post you\'re attempting to modify doesn\'t exist.', 'error', 'times' );

if ( isset( $_GET['approved'] ) ) $error->force( 'APPROVED_POST' );
else if ( isset( $_GET['rejected'] ) ) $error->force( 'REJECTED_POST' );
else if ( isset( $_GET['deleted'] ) ) $error->force( 'DELETED_POST' );

$allowed_types = array( 'map', 'seed', 'texture', 'skin', 'mod', 'server' );

// Set default values for database query.
$q_cols = 'id, title, slug, author, images, active, edited, submitted';
$q_where = 'active = "0"';

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
	if ( $type == 'server' ) {
		$posts = $db->query( 'SELECT "'.$type.'" AS type, '.$q_cols.', ip, port FROM `content_'.$type.'s` WHERE '.$q_where.'' )->fetch();
	}
	else $posts = $db->query( 'SELECT "'.$type.'" AS type, '.$q_cols.' FROM `content_'.$type.'s` WHERE '.$q_where.'' )->fetch();
	
	// Set post type for URL.
	$url->add( 'show', $type );
	
} else { // Show all posts.
	
	// Grab all posts created by user.
	$posts = $db->query('
		(SELECT "map"	 	AS type, '.$q_cols.', tags, versions 	FROM `content_maps` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "seed" 		AS type, '.$q_cols.', tags, seed	 	FROM `content_seeds` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "texture" 	AS type, '.$q_cols.', devices, versions FROM `content_textures`  WHERE '.$q_where.') UNION ALL
		(SELECT "skin" 		AS type, '.$q_cols.', tags, dl_link 	FROM `content_skins`  	 WHERE '.$q_where.') UNION ALL
		(SELECT "mod" 		AS type, '.$q_cols.', devices, versions FROM `content_mods` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "server" 	AS type, '.$q_cols.', ip, port	 	 	FROM `content_servers`   WHERE '.$q_where.')
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
	
	// Set post submission as edit date (if needed) for correct ordering.
	foreach( $posts as $post => $post_info ) {
		
		if ( $posts[$post]['edited'] != 0 ) {
			
			$posts[$post]['submitted'] = $posts[$post]['edited'];
			$posts[$post]['was_edited'] = TRUE;
		}
		
	}
	
	// Set number of posts per page.
	$posts_per_page = 5;
	
	// ** Pagination Setup ** //
	$total_pages = ceil( $posts_num / $posts_per_page );
	
	// Make sure page number requested is valid.
	$page = ( !empty( $_GET['page'] ) && is_numeric( $_GET['page'] ) ) ? (int)$_GET['page'] : 1;
	if ( $page > $total_pages ) $page = $total_pages;
	if ( $page < 1 ) $page = 1;
	
	// Set page number for URL.
	if ( $page != 1 ) $url->add( 'page', $page );
	
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
		if ( $page > 1 ) $pagination_html .= '<li><a href="'. $url->show('page='.($page - 1)) .'"><i class="fa fa-chevron-circle-left"></i></a></li>';
		
		// Page links.
		for ( $x = ($page - $range); $x < ( ($page + $range) + 1 ); $x++ ) {
			if ( ($x > 0) && ($x <= $total_pages) ) {
				$pagination_html .= ( $x == $page ) ? '<li class="active">' : '<li>';
				$pagination_html .= '<a href="'. $url->show('page='.$x) .'">'.$x.'</a></li>';
			} 
		}
		
		// Forward link.
		if ( $page != $total_pages ) $pagination_html .= '<li><a href="'. $url->show('page='.($page + 1)) .'"><i class="fa fa-chevron-circle-right"></i></a></li>';
		
		// Adding final <div> and <ul> tags to HTML markup.
		$pagination_html = '<div class="pagination"><ul>'. $pagination_html .'</ul></div>';
		
	}
	
	// Set up icons for post types.
	$icons = array( 'map' => 'map-marker', 'seed' => 'leaf', 'texture' => 'magic', 'skin' => 'smile-o', 'mod' => 'codepen', 'server' => 'gamepad' );
	
} // END: Posts exist for the given user.

// Start action switch.
switch( $action ) {

// Case: Approving posts.
case 'approve':
	
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
			
			// Mark post as approved.
			$db_where = array( 'id' => $post['id'] );
			$db_update = array( 'active' => '1', 'published' => date( 'Y-m-d H:i:s' ), 'reviewer_id' => $user->info('id') );
			
			$db->where( $db_where )->update( 'content_'.$post_type.'s', $db_update );
			
			redirect( '/moderate?approved' );
			
		} // END: Post exists in database.
		
	} // END: $_GET values present and valid.
	
break; // END: Approving posts.

// Case: Rejecting posts.
case 'reject':
	
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
			
			// Mark post as approved.
			$db_where = array( 'id' => $post['id'] );
			$db_update = array( 'active' => '-1', 'reviewer_id' => $user->info('id') );
			
			$db->where( $db_where )->update( 'content_'.$post_type.'s', $db_update );
			
			redirect( '/moderate?rejected' );
			
		} // END: Post exists in database.
		
	} // END: $_GET values present and valid.
	
break; // END: Rejecting posts.

// Case: Deleting posts.
case 'delete':
	
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
			
			// Check if post not already deleted.
			if ( $post['active'] == '-2' ) $error->set( 'ALREADY_DELETED' );
			else {
				
				// Mark post as deleted.
				$db_where = array( 'id' => $post['id'] );
				$db_update = array( 'active' => '-2', 'editor_id' => $user->info('id') );
				
				$db->where( $db_where )->update( 'content_'.$post_type.'s', $db_update );
				
				redirect( '/moderate?deleted' );
				
			} // END: Post not already deleted.
			
		} // END: Post exists in database.
		
	} // END: $_GET values present and valid.
	
break; // END: Deleting posts.

// Case: Toggle featured status.
case 'feature':
	
	$error->reset();
	
	// Check if $_GET values present and valid.
	if ( empty( $_GET['post'] ) || empty( $_GET['type'] ) || !in_array( $_GET['type'], $allowed_types ) ) $error->set( 'MISSING_INPUTS' );
	else {
		
		$post_id = $db->escape( $_GET['post'] );
		$post_type = $_GET['type'];
		
		// Check if post exists in database.
		$post = $db->select( 'id,slug,featured' )->from( 'content_'. $post_type .'s' )->where( array( 'id' => $post_id ) )->fetch();
		
		if ( !$db->affected_rows ) $error->set( 'INVALID_POST' );
		else {
			
			$post = $post[0];
			
			// If post is already featured, unfeature.
			if ( $post['featured'] == 1 ) {
				$update = array( 'featured' => 0 );
				$status = 'unfeatured';
			}
			
			// Otherwise, feature it (not already featured).
			else {
				$update = array( 'featured' => 1 );
				$status = 'featured';
			}
			
			$update['featured_time'] = date( 'Y-m-d H:i:s' );
			
			$db->where( array( 'id' => $post['id'] ) )->update( 'content_'.$post_type.'s', $update );
			
			// Redirect back to post.
			redirect( '/'.$post_type.'/'.$post['slug'].'?'.$status );
			
		} // END: Post exists in database.
		
	} // END: $_GET values present and valid.
	
break; // END: Toggle featured status.

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
$sort_urls .= '<a href="'.$url->show('', TRUE).'" class="'.$active.'bttn"><i class="fa fa-book"></i> All <span>'.$num['all'].'</span></a>';

foreach( $sort_url as $id => $p ) {
	$active = ( isset( $type ) && $id == $type ) ? 'active ' : '';
	$sort_urls .= '<a href="'.$url->show('show='.$id, TRUE).'" class="'.$active.'bttn"><i class="fa fa-'.$p[1].'"></i> '.$p[0].' <span>'.$num[$id].'</span></a>';
}

?>

<div id="p-title">
    <h1>Moderate Posts</h1>
    <div class="tabs">
        <a href="/" class="bttn"><i class="fa fa-arrow-left"></i> Back to Website</a>
    </div>
</div>

<?php $error->display(); ?>
<?php if ( $posts_num != 0 ) { // START: If posts exist. ?>

<div class="posts compact">
<?php

	foreach( $posts as $post ) { // START: Post foreach.
		
		// Grab first image from images for display.
		$post['images'] = explode( ',', $post['images'] );
		$post['image'] = urlencode( $post['images'][0] );
		
		// Pre-built image URL.
		$post['image_url'] = './uploads/140x100/'.$post['type'].'s/'.$post['image'];
		
		$post['author'] = $user->info( 'username', $post['author'] );
		
echo '<div class="post">
    <div class="img">
        <div class="overlay">
            <h2 style="font-weight:400; margin-bottom: 10px;"><a href="/'.$post['type'].'/'.$post['slug'].'">'.$post['title'].'</a></h2>
        </div>
        <a href="/'.$post['type'].'/'.$post['slug'].'"><img src="'.$post['image_url'].'" alt="'.$post['title'].'" width="140" height="80"></a>
    </div>
    <div class="info">
        <ul>
            <i class="fa fa-male"></i> <a href="/user/'.$post['author'].'">'.$post['author'].'</a><br><br>
        </ul>
        <div class="link">
            <a href="'.$url->show('action=approve&post='.$post['id'].'&type='.$post['type']).'" class="bttn dl silver right tip" data-tip="Approve" style="float: left;"><i class="fa fa-check no-space"></i></a>
            <a href="'.$url->show('action=delete&post='.$post['id'].'&type='.$post['type']).'" class="bttn">Delete</a>
            <a href="'.$url->show('action=reject&post='.$post['id'].'&type='.$post['type']).'" class="bttn red">Reject</a>
        </div>
    </div>
    
</div><br><br>';
		
	?>

    <!--<div class="post clearfix">
        <div class="preview">
            <div class="badge-type <?php echo $post['type']; ?>"><i class="fa fa-<?php echo $icons[ $post['type'] ]; ?>"></i> <?php echo $post['type']; ?></div>
            <?php if ( isset( $post['was_edited'] ) ) { ?><div class="status red"><i class="fa fa-pencil fa-fw"></i> Edited Post</div><?php } ?>
            <img src="<?php echo $post['image_url']; ?>" />
        </div>
        <div class="details">
            <h2><a href="/<?php echo $post['type']; ?>/<?php echo $post['slug']; ?>" target="_blank"><?php echo $post['title']; ?></a></h2>
            <div class="actions">
                <a href="/edit?post=<?php echo $post['id']; ?>&type=<?php echo $post['type']; ?>" class="bttn tip" data-tip="Edit"><i class="fa fa-pencil no-space"></i></a>
                <a href="/<?php echo $post['type']; ?>/<?php echo $post['slug']; ?>" class="bttn tip" data-tip="Preview" target="_blank"><i class="fa fa-eye no-space"></i></a>
                
                <span class="info"><span><i class="fa fa-male"></i> <a href="/user/<?php echo $post['author']; ?>"><?php echo $post['author']; ?></a></span><span><i class="fa fa-clock-o"></i> <?php echo since( strtotime( $post['submitted'] ) ); ?></span></span>
                
                <a href="<?php echo $url->show('action=approve&post='.$post['id'].'&type='.$post['type']); ?>" class="bttn green right tip" data-tip="Approve"><i class="fa fa-check no-space"></i></a>
                <a href="/moderate" class="bttn red right reject tip" data-tip="Reject"><i class="fa fa-times no-space"></i></a>
                <a href="/moderate" class="bttn right del tip" data-tip="Delete"><i class="fa fa-trash-o no-space"></i></a>
            </div>
<?php if ( $post['type'] == 'server' ) { ?>
            <div class="extra-info">
                IP: <strong><?php echo $post['tags']; // tags = ip & versions = port (SQL) ?></strong> &nbsp; Port: <strong><?php echo $post['versions']; ?></strong>
            </div>
<?php } ?>
        </div><div class="clear"></div>
        
    </div>-->

<?php } // END: Post foreach. ?>
<?php if ( isset( $pagination_html ) ) echo $pagination_html; ?>
</div>

<?php } // END: If posts exist. ?>

<?php show_footer(); ?>