<?php

require_once( 'core.php' );
show_header( 'Admin Dashboard' );

if ( !isset( $_GET['action'] ) ) $_GET['action'] = '';

// Grabbing every post that's not approved yet.
// DEV NOTE: (SELECT "map" AS type, content_maps.* FROM `content_maps` WHERE active=0) UNION
$query_columns = 'id, title, author_id, submitted, image_file, slug';
$unapproved = $db->query( '
	(SELECT "map" AS type, '. $query_columns .', category, views FROM `content_maps` WHERE active=0) UNION
	(SELECT "seed" AS type, '. $query_columns .', category, views FROM `content_seeds` WHERE active=0) UNION
	(SELECT "texture" AS type, '. $query_columns .', version, views FROM `content_textures` WHERE active=0) UNION
	(SELECT "mod" AS type, '. $query_columns .', version, views FROM `content_mods` WHERE active=0) UNION
	(SELECT "server" AS type, '. $query_columns .', ip, port FROM `content_servers` WHERE active=0)
' )->fetch();

// Counting how many posts haven't been approved yet.
$unapproved_num = $db->affected_rows;

// Showing a message if there are some unapproved posts.
if ( $unapproved_num != 0 ) $error = 1;
else $error = 2;

// Showing messages based on url.
if ( isset( $_GET['message'] ) ) {
	if ( $_GET['message'] == 'activated' ) $error = 4;
	else if ( $_GET['message'] == 'rejected' ) $error = 5;
}

// Setting up pagination.
$posts_per_page = 5;
$total_pages = ceil( $unapproved_num / $posts_per_page );

// Only set up if there are posts...
if ( $unapproved_num != 0 ) {
	
	if ( !empty( $_GET['page'] ) && is_numeric( $_GET['page'] ) ) $page = (int)$_GET['page']; else $page = 1;
	if ( $page > $total_pages ) $page = $total_pages;
	if ( $page < 1 ) $page = 1;
	
	$offset = ( $page - 1 ) * $posts_per_page;
	
	// Slicing posts to be sorted by date submitted.
	$mid = array();
	foreach ($unapproved as $key => $row) $mid[$key] = $row['submitted'];
	array_multisort($mid, SORT_DESC, $unapproved);
	
	$unapproved = array_slice( $unapproved, $offset, $posts_per_page );
	
	// Setup pagination HTML.
	$pagination = '';
	$range = 3;
	
	// Back link.
	if ($page > 1) {
	   $prevpage = $page - 1;
	   $pagination .= "<li><a href='{$_SERVER['PHP_SELF']}?page=".$prevpage."'><i class='fa fa-chevron-circle-left'></i></a></li>\n";
	}
	
	// Show "number" links.
	for ($x = ($page - $range); $x < (($page + $range) + 1); $x++) {
		if (($x > 0) && ($x <= $total_pages)) {
			
			// Normal page link.
			$pagination .= "<li";
			if ($x == $page) $pagination .= " class='active'";
			$pagination .= ">";
			
			if ($x == $page) $pagination .= "$x";
			else $pagination .="<a href='{$_SERVER['PHP_SELF']}?page=$x'>$x</a>";
			
			$pagination .= "</li>\n";
			
		} 
	}
	
	// Forward link.
	if ($page != $total_pages && $total_pages != 0) {
	   $nextpage = $page + 1;
	   $pagination .= "<li><a href='{$_SERVER['PHP_SELF']}?page=$nextpage'><i class='fa fa-chevron-circle-right'></i></a></li>\n";
	}

}

$errors = array(
	1 => array( 'There are some posts that are awaiting moderation.', 'warning', 'exclamation-triangle' ),
	2 => array( 'There are no posts that are awaiting moderation.', 'success', 'check' ),
	3 => array( 'Invalid post ID &amp; type combination.', 'error', 'times' ),
	4 => array( 'The post has been approved and is now live on the website.', 'success', 'check' ),
	5 => array( 'The post has been rejected.', 'success', 'check' ),
);

// Allowed post types.
$post_types = array( 'map', 'seed', 'texture', 'mod', 'server' );

switch ( $_GET['action'] ) { // START SWITCH

case 'approve': // CASE: Accepting posts.
	
	// Check for existence of post ID and type.
	if ( !empty( $_GET['post'] ) && !empty( $_GET['type'] ) && is_numeric( $_GET['post'] ) && in_array( $_GET['type'], $post_types ) ) {
		
		$post_id = strip_tags( $_GET['post'] );
		$post_type = strip_tags( $_GET['type'] );
		
		// Check if the post exists.
		$db->from( 'content_' . $post_type . 's' )->where( array( 'id' => $post_id ) )->fetch();
		
		if ( $db->affected_rows ) {
			
			// Update the value in the database.
			$db->where( array( 'id' => $post_id ) )->update( 'content_' . $post_type . 's', array( 'active' => 1, 'published' => date( 'Y-m-d H:i:s' ), 'reviewer_id' => $user->info()['id'] ) );
			
			// Redirect to success message.
			redirect( $_SERVER['PHP_SELF'] . '?page=' . $page . '&message=activated' );
			
		} else $error = 3; // Post doesn't exist.
		
	} else $error = 3; // Post missing input variables or invalid input formatting.
	
break; // END: Accepting posts.

case 'reject': // CASE: Rejecting posts.
	
	// Check for existence of post ID and type.
	if ( !empty( $_GET['post'] ) && !empty( $_GET['type'] ) && is_numeric( $_GET['post'] ) && in_array( $_GET['type'], $post_types ) ) {
		
		$post_id = strip_tags( $_GET['post'] );
		$post_type = strip_tags( $_GET['type'] );
		
		// Check if the post exists.
		$db->from( 'content_' . $post_type . 's' )->where( array( 'id' => $post_id ) )->fetch();
		
		if ( $db->affected_rows ) {
			
			// Update the value in the database.
			$db->where( array( 'id' => $post_id ) )->update( 'content_' . $post_type . 's', array( 'active' => '-1', 'reviewer_id' => $user->info()['id'] ) );
			
			// Redirect to success message.
			redirect( $_SERVER['PHP_SELF'] . '?page=' . $page . '&message=rejected' );
			
		} else $error = 3; // Post doesn't exist.
		
	} else $error = 3; // Post missing input variables or invalid input formatting.
	
break; // END: Rejecting posts.

} // END SWITCH

?>

<div id="page-head">
    <h2>Admin Dashboard</h2>
    <div class="buttons">
        <a href="../"><i class="fa fa-arrow-left fa-fw"></i> Back to Website</a>
    </div>
    <div class="clear"></div>
</div>

<?php if ( !empty( $error ) ) { $m = TRUE; echo '<div class="message '.$errors[$error][1].'"><i class="fa fa-'.$errors[$error][2].' fa-fw"></i> '.$errors[$error][0].'</div>'; } ?>

<?php if ( $total_pages > 1 ) { ?>
<div class="pagination bottom bottom-spacer">
    <ul>
        <?php echo $pagination; ?>
    </ul>
</div>
<?php } ?>

<?php if ( $unapproved_num != 0 ) { // START: If unapproved posts found. ?>
<div class="post-list">
    
<?php foreach( $unapproved as $post ) { // START: Unapproved foreach. ?>
    <div class="post">
        <div class="avatar-main">
            <a href="../uploads/posts/<?php echo $post['type']; ?>s/<?php echo $post['image_file']; ?>" target="_blank"><img src="../core/timthumb.php?src=../uploads/posts/<?php echo $post['type']; ?>s/<?php echo urlencode( $post['image_file'] ); ?>&h=150&w=150&zc=1" alt="<?php echo $post['title']; ?>" /></a>
        </div>
        <div class="details">
            <h2><?php echo $post['title']; ?></h2>
            <p>
                Author: <strong><a href="../profile.php?user=<?php echo $user->info( $post['author_id'] )['username']; ?>"><?php echo $user->info( $post['author_id'] )['username']; ?></a></strong><br />
                Submitted: <strong><?php echo time_since( strtotime( $post['submitted'] ) ); ?></strong><br />
                Post Type: <strong><?php echo $post['category']; ?> <?php echo $post['type']; ?></strong>
            </p>
            <ul class="tabs"><li><a href="../<?php echo $post['type']; ?>.php?<?php echo $post['type']; ?>=<?php echo $post['slug']; ?>"><i class="fa fa-eye fa-fw"></i> Preview</a></li><li><a href="?action=edit&post=<?php echo $post['id']; ?>&type=<?php echo $post['type']; ?>"><i class="fa fa-pencil fa-fw"></i> Edit Post</a></li><li class="right"><a href="?action=approve&post=<?php echo $post['id']; ?>&type=<?php echo $post['type']; ?>&page=<?php echo $page; ?>" class="green"><i class="fa fa-check fa-fw"></i> Approve</a></li><li class="right"><a href="?action=reject&post=<?php echo $post['id']; ?>&type=<?php echo $post['type']; ?>&page=<?php echo $page; ?>" class="red"><i class="fa fa-times fa-fw"></i></a></li></ul>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } // END: Unapproved foreach. ?>
    
</div>

<?php if ( $total_pages > 1 ) { ?>
<div class="pagination bottom">
    <ul>
        <?php echo $pagination; ?>
    </ul>
</div>
<?php } ?>

<?php } // END: If unapproved posts found. ?>

<?php show_footer(); ?>