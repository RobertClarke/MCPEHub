<?php

require_once( 'core.php' );

// Store all profile information in one variable.
$profile = array();

$profile['found'] = FALSE;
$profile['owned'] = FALSE;

$profile['username'] = isset( $_GET['user'] ) ? $db->escape( strip_tags( substr( $_GET['user'], 0, 50 ) ) ) : '';

if ( $user->logged_in() && empty( $profile['username'] ) ) $profile['username'] = $user->info( 'username' );

// If user requested in URL and username is found in database (+ check if suspended).
if ( !empty( $profile['username'] ) && !$user->suspended( $profile['username'] ) && $user_db = $user->info( '', $profile['username'] ) ) {
	
	$profile['found'] = TRUE;
	
	// Add username to links.
	$c_url->add( 'user', $profile['username'] );
	
	// Merge in database info with profile array.
	$profile = array_merge( $profile, $user_db );
	
	// Check if user is owner of profile.
	if ( $user->logged_in() && $profile['username'] == $user->info('username') ) {
		$profile['owned'] = TRUE;
		$page_title = 'My Profile';
	}
	else $page_title = $profile['username'].'\'s Profile';
	
	// Setup avatar image URL.
	$profile['avatar'] = './avatar/110x110/'.$profile['avatar_file'];
	
	// Clean up bio HTML using HTMLPurifier.
	require( 'core/htmlpurifier/HTMLPurifier.standalone.php' );
	$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
	
	$profile['bio'] = $purifier->purify( $profile['bio'] );
	
	// Devices list.
	$profile['device_list'] = explode( ',', $profile['devices'] );
	$profile['devices_list'] = '';
	
	foreach( $profile['device_list'] as $device ) $profile['devices_list'] .= $device.' &amp; ';
	$profile['devices_list'] = trim( $profile['devices_list'], ' &amp; ' );
	
	// Show a message if user forgot to create a bio.
	if ( $profile['owned'] && empty( $profile['bio'] ) ) {
		$error->add( 'NO_BIO', 'You haven\'t posted a bio yet! Post one to let other members know more about you: <a href="/edit_profile">Update Profile</a>', 'warning', 'pencil' );
		$error->set( 'NO_BIO' );
	}
	
	// Set default values for database query.
	$q_cols = 'id, title, slug, author, images, views, submitted, featured';
	$q_where = 'author = "'. $profile['id'] .'" AND active = "1"';
	
	// Grab all posts created by user.
	$posts = $db->query('
		(SELECT "map"	 	AS type, '.$q_cols.', downloads	 		FROM `content_maps` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "seed" 		AS type, '.$q_cols.', null AS downloads	FROM `content_seeds` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "texture" 	AS type, '.$q_cols.', downloads	 		FROM `content_textures`  WHERE '.$q_where.') UNION ALL
		(SELECT "skin"	 	AS type, '.$q_cols.', downloads	 		FROM `content_skins`  	 WHERE '.$q_where.') UNION ALL
		(SELECT "mod" 		AS type, '.$q_cols.', downloads	 		FROM `content_mods` 	 WHERE '.$q_where.') UNION ALL
		(SELECT "server" 	AS type, '.$q_cols.', null AS downloads	FROM `content_servers`   WHERE '.$q_where.')
	')->fetch();
	
	$posts_num = $db->affected_rows;
	
}
else $page_title = 'Profile Not Found';

show_header( $page_title, FALSE );

// Profile exists, show profile.
if ( $profile['found'] ) {
	
	// Show tools for profile owner.
	if ( $profile['owned'] ) {
	
?>

<div id="page-title">
    <h2>My Profile</h2>
    <ul class="tabs">
        <a href="/account?tab=avatar" class="bttn"><i class="fa fa-camera"></i> Change Avatar</a>
        <a href="/edit_profile" class="bttn purple"><i class="fa fa-pencil"></i> Edit Profile</a>
    </ul>
</div>

<?php } // END: Show tools for profile owner. ?>

<div id="profile">
    
    <?php $error->display(); ?>
    
    <div class="head section clearfix">
        <div class="avatar">
            <img src="<?php echo $profile['avatar']; ?>" alt="<?php echo $profile['username']; ?>'s Avatar" />
        </div>
        <div class="info">
            <h1><?php echo (!empty($profile['name'])) ? $profile['name'] : $profile['username']; ?>'s Profile</h1>
            <h5><?php echo $user->badges( $profile['username'] ); ?> Last active <?php echo time_since( strtotime( $profile['last_active'] ) ); ?></h5>
            <div class="actions">
                <a href="#" class="bttn"><i class="fa fa-bullseye"></i> Subscribe</a>
                <a href="#" class="bttn"><i class="fa fa-child"></i> Add Friend</a>
                <a href="#" class="bttn"><i class="fa fa-envelope"></i> Send Message</a>
                <a href="#" class="bttn"><i class="fa fa-flag"></i> Report Member</a>
            </div>
        </div>
    </div>
    <div class="about section">
        <h2>About Me</h2>
<?php if ( !empty( $profile['bio'] ) || !empty( $profile['youtube'] ) || !empty( $profile['twitter'] ) || !empty( $profile['devices'] ) ) { // Profile info exists. ?>
        
        <?php echo ( !empty( $profile['bio'] ) ) ? $profile['bio'] : '<p>I haven\'t updated my bio yet, check back soon!</p>'; ?>
        
<?php if ( !empty( $profile['youtube'] ) || !empty( $profile['twitter'] ) || !empty( $profile['devices'] ) ) { ?>
        <div class="links">
            <?php if ( !empty( $profile['devices'] ) ) { ?><span class="bttn"><i class="fa fa-mobile-phone"></i> <?php echo $profile['devices_list']; ?></span><?php } ?>
            <?php if ( !empty( $profile['youtube'] ) ) { ?><a href="http://youtube.com/<?php echo $profile['youtube']; ?>" class="bttn youtube" target="_blank"><i class="fa fa-youtube-play"></i> <?php echo $profile['youtube']; ?></a><?php } ?>
            <?php if ( !empty( $profile['twitter'] ) ) { ?><a href="http://twitter.com/<?php echo $profile['twitter']; ?>" class="bttn twitter" target="_blank"><i class="fa fa-twitter"></i> <?php echo $profile['twitter']; ?></a><?php } ?>
        </div>
<?php } ?>
        
<?php } else echo 'I haven\'t updated my profile yet, check back soon!'; ?>
    </div>
    <div class="posts">
        <h2>My Posts<?php if ( $posts_num != 0 ) echo ' <span>'.$posts_num.'</span>'; ?></h2>
<?php if ( $posts_num != 0 ) { // User has posts. ?>
	    <div id="posts" class="profile">
<?php
	
	// Set number of posts per page.
	$posts_per_page = 4;
	
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

	foreach( $posts as $post ) { // START: Post foreach.
		
		// Determine number of likes and comments on post.
		$q_where = 'post_id = "'.$post['id'].'" AND post_type = "'.$post['type'].'"';
		$count_vars = $db->query('
			(SELECT "likes"		AS type, COUNT(*) FROM `likes`		WHERE '.$q_where.') UNION ALL
			(SELECT "comments"	AS type, COUNT(*) FROM `comments`	WHERE '.$q_where.')
		')->fetch();
		
		foreach( $count_vars as $var ) $post[ $var['type'] ] = $var['COUNT(*)'];
		
		$post['url'] = $post['type'].'/' . $post['slug'];
		
		// Grab first image from images for display.
		$post['images'] = explode( ',', $post['images'] );
		$post['image'] = urlencode( $post['images'][0] );
		
		$post['image_url'] = './uploads/290x140/'.$post['type'].'s/'.$post['image'];
		$post['avatar_url'] = './avatar/32x32/'.$user->info('avatar_file', $post['author']);
		
		$dot = ( strlen( $post['title'] ) > 40 ) ? '...' : ''; // '...' After post title if too long.
		
	?>

    <div class="post">
        
        <div class="img">
            <?php if ( $post['featured'] == 1 ) { ?><div class="featured"><i class="fa fa-star"></i> Featured <?php echo ucwords( $post['type'] ); ?></div><?php } ?>
            <a href="<?php echo $post['url']; ?>"><img src="<?php echo $post['image_url']; ?>" /></a>
        </div>
        
        <div class="side">
            <h3><a href="<?php echo $post['url']; ?>"><?php echo ucwords($post['type']); ?>: <?php echo substr($post['title'], 0, 40).$dot; ?></a></h3>
            <ul>
                <li><i class="fa fa-thumbs-up fa-fw"></i> <span><?php echo $post['likes']; ?></span> likes</li>
                <li><i class="fa fa-eye fa-fw"></i> <span><?php echo $post['views']; ?></span> views</li>
                <li><i class="fa fa-comments fa-fw"></i> <span><?php echo $post['comments']; ?></span> comments</li>
<?php if ( isset( $post['downloads'] ) ) { ?>                <li><i class="fa fa-download fa-fw"></i> <span><?php echo $post['downloads']; ?></span> downloads</li><?php } ?>
            </ul>
            <a href="<?php echo $post['url']; ?>" class="view-bttn silver">View <?php echo ucwords($post['type']); ?> &raquo;</a>
        </div>
        
    </div>

<?php } // END: Post foreach. ?>
<?php if ( isset( $pagination_html ) ) echo $pagination_html; ?>
	    </div>
<?php } else echo 'I haven\'t made any posts yet, check back soon!'; ?>
    </div>
    
</div>

<?php
	
} // END: Profile exists, show profile.

// If profile doesn't exist, show an error message.
else {
	
	// Missing user variable in URL.
	if ( empty( $profile['username'] ) )
		$invalid = 'The profile you requested couldn\'t be found.';
	
	// Requested user is suspended.
	else if ( $user->suspended( $profile['username'] ) )
		$invalid = 'The profile you requested couldn\'t be found because the user "<strong>'.htmlspecialchars( $profile['username'] ).'</strong>" is suspended.';
	
	// Requested user doesn't exist in database.
	else
		$invalid = 'The profile you requested couldn\'t be found because the user "<strong>'.htmlspecialchars( $profile['username'] ).'</strong>" doesn\'t exist.';
	
	$error->add( 'INVALID', $invalid, 'error', 'times' );
	$error->set( 'INVALID' );
	
	$error->display();
	
} // END: Profile doesn't exist, show an error message.

show_footer();

?>