<?php

require_once( 'core.php' );

// For convenience around the file.
$type = 'server';

// If post slug missing, redirect back to post list.
if ( !isset( $_GET['post'] ) || empty( $_GET['post'] ) ) redirect( '/'.$type.'s' );

// Set post slug, if exists.
$slug = $db->escape( $_GET['post'] );

// Modify slug to remove "-" at end.
if ( substr( $slug, -1) == '-' ) $slug = rtrim( $slug, '-' );

// Modify slug to remove "-" at start.
if ( substr( $slug, 0, 1 ) == '-' ) $slug = ltrim( $slug, '-');

// Modify slug to lower case.
$slug = strtolower( $slug );


// Check if post exists + grab info.
$query = $db->from( 'content_'.$type.'s' )->where( array( 'slug' => $slug ) )->fetch();
$num = $db->affected_rows;

// If post not found, redirect to post list.
if ( $num == 0 ) redirect( '/'.$type.'s' );

$post = $query[0];

show_header( $post['title'], FALSE, '', '', 'MCPEHub is the #1 Minecraft PE community in the world, featuring seeds, maps, servers, mods, and more.', 'minecraft pe maps, survival, parkour, adventure, minecraft pe, mcpe' );

// Show post only if: activated (public), author (private), admin/mod.
if ( $post['active'] == 1 || $post['author'] == $user->info('id') || $user->is_admin() || $user->is_mod() ) {
	
	// Update view count on post.
	$post_tools->update_views( $post['id'], $type );
	
	$post_owned = ( $post['author'] == $user->info('id') ) ? TRUE : FALSE;
	
	$error->add( 'SUBMITTED', 'You\'ve submitted your '.$type.'! Once approved by a moderator, it\'ll be seen on the website.', 'success', 'check' );
	$error->add( 'PENDING', 'Your '.$type.' hasn\'t been approved by a moderator yet and cannot be seen by the public.', 'warning', 'eye' );
	$error->add( 'REJECTED', 'Your '.$type.' was rejected by a moderator and won\'t appear on the website.', 'error', 'times' );
	$error->add( 'EDITED', 'You\'ve edited your '.$type.'. Once your changes are approved, your '.$type.' will be visible again.', 'info', 'pencil' );
	
	// Show messages, as needed.
	if 		( $post['active'] == 0 ) 		$error->set( 'PENDING' );
	else if ( $post['active'] == '-1' ) 	$error->set( 'REJECTED' );
	
	if ( isset( $_GET['created'] ) ) 		$error->force( 'SUBMITTED' );
	if ( isset( $_GET['edited'] ) )			$error->force( 'EDITED' );
	
	if ( $user->is_admin() || $user->is_mod() ) {
		
		if ( isset( $_GET['featured'] ) ) {
			$error->add( 'FEATURED', 'The post has been marked as featured.', 'success', 'star' );
			$error->force( 'FEATURED' );
		}
		
		if ( isset( $_GET['unfeatured'] ) ) {
			$error->add( 'UNFEATURED', 'The post has been unmarked as featured.', 'success', 'check' );
			$error->force( 'UNFEATURED' );
		}
		
	}
	
	// Determine number of likes and comments on post.
	$q_where = 'post_id = "'.$post['id'].'" AND post_type = "'.$type.'"';
	$count_vars = $db->query('
		(SELECT "likes"		AS type, COUNT(*) FROM `likes`		WHERE '.$q_where.') UNION ALL
		(SELECT "comments"	AS type, COUNT(*) FROM `comments`	WHERE '.$q_where.')
	')->fetch();
	
	foreach( $count_vars as $var ) $post[ $var['type'] ] = $var['COUNT(*)'];
	
	// Check if user has liked/favorited the post already.
	if ( $user->logged_in() ) {
		
		$q_where = 'post_id = "'.$post['id'].'" AND post_type = "'.$type.'" AND user_id = "'.$user->info('id').'"';
		$count_stats = $db->query('
			(SELECT "liked"		AS type, COUNT(*) FROM `likes`		WHERE '.$q_where.') UNION ALL
			(SELECT "favorited"	AS type, COUNT(*) FROM `favorites`	WHERE '.$q_where.')
		')->fetch();
		
		foreach( $count_stats as $var ) $post[ $var['type'] ] = $var['COUNT(*)'];
		
		$post['liked'] = ( $post['liked'] != 0 ) ? TRUE : FALSE;
		$post['favorited'] = ( $post['favorited'] != 0 ) ? TRUE : FALSE;
		
	}
	
	// Format published date for display.
	$post['published'] = ( $post['published'] != 0 ) ? 'Published '.time_since( strtotime($post['published']) ) : 'Post Pending Approval';
	
	$post['author_id'] = $post['author'];
	
	// Grab author info.
	$post['author'] = $user->info('username', $post['author_id']);
	$post['author_avatar'] = $user->info('avatar_file', $post['author_id']);
	
	// Grab all post images + thumbnails.
	$post['db_images'] = explode( ',', $post['images'] );
	
	foreach( $post['db_images'] as $image ) {
		$post['images_full'][] = '/uploads/690x270/'.$type.'s/'.urlencode($image);
		$post['images_thumbs'][] = '/uploads/120x70/'.$type.'s/'.urlencode($image);
	}
	
	// Sanitize description for display.
    require( 'core/htmlpurifier/HTMLPurifier.standalone.php' );
	$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
	
	$post['description'] = $purifier->purify( $post['description'] );
	
	// Get online status + info from server.
	require_once( './core/classes/ping.php' );
	$connect = new mcQuery();
	
	// Connected!
	if ( $connect->connect( $post['ip'], $post['port'], 2 ) ) {
		
		$online = TRUE;
		
		$mc = new MinecraftQuery();
		$mc->Connect( $post['ip'], $post['port'], 2 );
		
		$server_info = $mc->GetInfo();
		
		//$statuses[$status]['players'] = ' - ' . $server_info['Players'] . '/' . $server_info['MaxPlayers'] . ' Players';
		
	}
	else $online = FALSE;
	
// Post not activated (public), author (private), admin/mod - redirect to post list.
} else redirect( '/'.$type.'s?disabled' );

?>
<div id="post">
    
    <div class="title clearfix">
        <div class="like">
            <a href="#" class="tip" data-tip="Like Post"><i class="fa fa-thumbs-up"></i></a>
            <div class="count"><?php echo $post['likes']; ?></div>
        </div>
        <div class="info">
            <h1><?php echo $post['title']; ?></h1>
            <h4><a href="/user/<?php echo $post['author']; ?>"><img src="/avatar/32x32/<?php echo $post['author_avatar']; ?>" /> <?php echo $post['author']; ?></a></h4>
        </div>
    </div>
    
<?php if ( $user->is_admin() || $user->is_mod() ) { ?>
    <div class="admin-actions">
        <h4>Admin &amp; Mod Tools</h4>
        
        <a href="/edit?post=<?php echo $post['id']; ?>&type=<?php echo $type; ?>" class="bttn"><i class="fa fa-pencil fa-fw"></i>Edit Post</a>
        <a href="/moderate?action=feature&post=<?php echo $post['id']; ?>&type=<?php echo $type; ?>" class="bttn<?php if ( $post['featured'] == 1 ) echo ' featured'; ?>"><i class="fa fa-star fa-fw"></i><?php echo ( $post['featured'] == 1 ) ? 'Unfeature' : 'Feature'; ?> Post</a>
        
<?php if ( $post['active'] == 1 ) { ?>
        <a href="/moderate?action=reject&post=<?php echo $post['id']; ?>&type=<?php echo $type; ?>" class="bttn red right"><i class="fa fa-times fa-fw"></i>Unapprove Post</a>
<?php } else { ?>
        <a href="/moderate?action=approve&post=<?php echo $post['id']; ?>&type=<?php echo $type; ?>" class="bttn green right"><i class="fa fa-check fa-fw"></i>Approve Post</a>
<?php } ?>
        
        <a href="/moderate?action=delete&post=<?php echo $post['id']; ?>&type=<?php echo $type; ?>" class="bttn right"><i class="fa fa-trash-o fa-fw"></i>Delete Post</a>
    </div>
<?php } ?>
    
    <?php $error->display(); ?>
    
    <div class="slideshow">
        <div id="post-slider" class="flexslider">
            <ul class="slides"><?php foreach( $post['images_full'] as $image ) { echo '<li><img src="'.$image.'" /></li>'; } ?></ul>
        </div>
        <div id="post-carousel" class="thumbs flexslider">
            <ul class="slides"><?php foreach( $post['images_thumbs'] as $image ) { echo '<li><img src="'.$image.'" /></li>'; } ?></ul>
        </div>
    </div>
    
    <div class="details clearfix">
        <div class="section author">
            <a href="/user/<?php echo $post['author']; ?>"><img src="/avatar/50x50/<?php echo $post['author_avatar']; ?>" class="avatar" /></a>
            <div class="info">
                <h3><a href="/user/<?php echo $post['author']; ?>"><?php echo $post['author']; ?></a></h3>
                <p><?php echo $post['published']; ?></p>
                <a href="#" class="sub view-bttn silver"><i class="fa fa-bullseye"></i> Subscribe</a>
            </div>
        </div>
        <div class="section last">
            <a class="view-bttn seed<?php if ( !$online ) echo ' silver'; ?>"><?php echo ( $online ) ? 'Online! &nbsp; '.$server_info['Players'].'/'. $server_info['MaxPlayers'].' Players' : 'Server Offline'; ?></a>
            <ul class="stats">
                <li>
                    <span><?php echo $post['views']; ?></span> Views
                    <span class="sep"></span>
                    <span><?php echo $post['comments']; ?></span> Comments
                </li>
            </ul>
            <div class="actions">
                <a href="#comment-form" class="tip" data-tip="Post Comment"><i class="fa fa-comments"></i></a>
                <a href="#" class="tip like" data-tip="Like Post"><i class="fa fa-thumbs-up"></i></a>
                <a href="#" class="tip fav" data-tip="Add to Favorites"><i class="fa fa-heart"></i></a>
                <a href="#"><i class="fa fa-flag fa-fw"></i> Report <?php echo ucwords($type); ?></a>
            </div>
        </div>
        <div class="clear"></div>
        <div class="details last clearfix">
            <div class="section long">
	            IP: <strong><?php echo $post['ip']; ?></strong> &nbsp; Port: <strong><?php echo $post['port']; ?></strong>
            </div>
        </div>
    </div>
    
    <div class="description sec">
        <?php echo $post['description']; ?>
    </div>
    
    <div class="actions more"><a href="http://mcpehub.com/<?php echo $type.'s'; ?>"><i class="fa fa-gamepad fa-fw"></i>Minecraft PE <?php echo ucwords( $type ).'s'; ?></a></div>
    
    <div class="sec">
        <center><div class="a-inline">
            <ins class="adsbygoogle" style="display:inline-block;width:336px;height:280px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="9036676673"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div></center>
    </div>
    
<?php $comment_tools->comment_form( $post['slug'], $type ); ?>
<?php $comment_tools->show_comments( $post['id'], $type ); ?>
  
</div>

<?php show_footer(); ?>