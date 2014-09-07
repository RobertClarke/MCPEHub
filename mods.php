<?php

require_once( 'core.php' );
show_header( 'Minecraft PE Mods', FALSE, '', '', 'Complete Minecraft PE mods make it easy to change the look and feel of your game. Updated often with the best mods for MCPE.', 'minecraft pe mods, minecraft pe, mcpe' );

if ( isset( $_GET['disabled'] ) ) {
	$error->add( 'DISABLED', 'The post you requested is unavailable or disabled at this time.', 'error', 'times' );
	$error->set( 'DISABLED' );
}

$query_where = array( 'active' => 1 );
$query_like = array();

// If the user is searching for something.
if ( isset( $_GET['search'] ) && !empty( $_GET['search'] ) ) {
	
	$search = substr( $_GET['search'], 0, 100 );
	
	$c_url->add( 'search', urlencode( $search ) );
	$query_like['title'] = $db->escape( strip_tags( $search ) );
	
}

// Sorting options.
$sort_allowed = array( 'latest', 'views', 'downloads' );

if ( !empty( $_GET['sort'] ) && in_array( $_GET['sort'], $sort_allowed ) ) {
	
	if ( $_GET['sort'] == 'latest' ) $query_order = 'published DESC';
	else {
		$c_url->add( 'sort', $_GET['sort'] );
		$query_order = $db->escape( $_GET['sort'] ).' DESC';
	}
	
}
else {
	$_GET['sort'] = 'latest';
	$query_order = 'published DESC';
}

// Set up pagination.
$post_count = $db->select('id')->from('content_mods')->like($query_like)->where($query_where)->fetch();

// Set number of posts per page.
$posts_per_page = 10;

// ** Pagination Setup ** //
$total_pages = ceil( $db->affected_rows / $posts_per_page );

// Make sure page number requested is valid.
$page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) ) ? (int)$_GET['page'] : 1;
if ( $page > $total_pages ) $page = $total_pages;
if ( $page < 1 ) $page = 1;

// Set page number for URL.
if ( $page != 1 ) $c_url->add( 'page', $page );

// Offset for query.
$offset = ($page - 1) * $posts_per_page;

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

// Fetch posts from database based on page/offset.
$posts = $db->from( 'content_mods' )->limit( (int)$offset, (int)$posts_per_page )->like( $query_like )->order_by( $query_order )->where( $query_where )->fetch();

$posts_count = $db->affected_rows;

// HTML for sort links (loop for convenience).
$sort_html = '';
$sort = array( 'latest' => 'Latest Posts', 'views' => 'Most Viewed', 'downloads' => 'Most Downloaded' );
foreach( $sort as $key => $display ) {
	$l_active = ( isset( $_GET['sort'] ) && $_GET['sort'] == $key ) ? ' active' : '';
	$sort_html .= '<a href="'.$c_url->show('sort='.$key, TRUE).'" class="bttn'.$l_active.'">'.$display.'</a>';
}

// No posts found, show error messages.
if ( $posts_count == 0 ) {

	// If search query.
	if ( isset( $_GET['search'] ) && !empty( $_GET['search'] ) ) $error->add( 'NO_POSTS', 'No mods were found under "<b>'.htmlspecialchars($_GET['search']).'</b>".', 'warning', 'search' );
	
	// If there are just no posts.
	else $error->add( 'NO_POSTS', 'There are no posts found in this category.', 'warning', 'frown-o' );
	
	$error->set( 'NO_POSTS' );
	
}

// If posts are found, and a search, lets show a message informing of search results.
else if ( isset( $_GET['search'] ) && !empty( $_GET['search'] ) ) {
	
	$error->add( 'SEARCH_RESULT', 'Your search for "<b>'.htmlspecialchars($_GET['search']).'</b>" returned '.$posts_count.' mods.', 'info', 'search' );
	$error->set( 'SEARCH_RESULT' );
	
}

?>

<div id="page-title">
    <h1>Minecraft PE Mods</h1>
    <ul class="tabs">
        <a href="/how-to-install-mods" class="bttn"><i class="fa fa-question"></i> How To Install</a>
        <a href="#search" class="bttn search-toggle"><i class="fa fa-search"></i> Search</a>
        <a href="/submit?type=mod" class="bttn green"><i class="fa fa-plus"></i> Submit Mod</a>
    </ul>
</div>

<div class="post-search<?php if ( isset($_GET['search']) && !empty($_GET['search']) ) echo ' show'; ?>"><form action="<?php echo $c_url->show('', TRUE); ?>" method="GET" class="form">
    <input type="text" name="search" id="search" class="text" value="<?php echo isset( $_GET['search'] ) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search Mods..." maxlength="100" />
    <button type="submit" id="submit">Search</button>
</form></div>

<?php $error->display(); ?>

<div id="posts">
    
<?php

// If posts exist in database according to given criteria.
if ( $posts_count != 0 ) {
	
	if ( !isset( $_GET['search'] ) || empty( $_GET['search'] ) ) {
	
?>
<div class="post-sort"><?php echo $sort_html; ?></div>
<?php
	
	}
	
	// Primary post loop.
	foreach( $posts as $id => $post ) {
		
		// Determine number of likes and comments on post.
		$q_where = 'post_id = "'.$post['id'].'" AND post_type = "mod"';
		$count_vars = $db->query('
			(SELECT "likes"		AS type, COUNT(*) FROM `likes`		WHERE '.$q_where.') UNION ALL
			(SELECT "comments"	AS type, COUNT(*) FROM `comments`	WHERE '.$q_where.')
		')->fetch();
		
		foreach( $count_vars as $var ) $post[ $var['type'] ] = $var['COUNT(*)'];
		
		$post['author_username'] = $user->info('username', $post['author']);
		$post['url'] = 'mod/' . $post['slug'];
		
		// Set up HTML for "version" tag in post list.
		$post['versions'] = explode( ',', $post['versions'] );
		$post['versions_html'] = '';
		$post['versions_list'] = '';
		
		// If only 0.8.0, set yellow class on versions div.
		if ( count( $post['versions'] ) == 1 && in_array( '0.8.0', $post['versions'] ) ) $post['versions_html'] .= '<div class="version old">';
		else $post['versions_html'] .= '<div class="version">';
		
		foreach( $post['versions'] as $version ) $post['versions_list'] .= $version.' / ';
		$post['versions_list'] = trim( $post['versions_list'], ' / ' );
		
		$post['versions_html'] .= $post['versions_list'] . "</div>\n";
		
		// Grab first image from images for display.
		$post['images'] = explode( ',', $post['images'] );
		$post['image'] = urlencode( $post['images'][0] );
		
		$post['image_url'] = './uploads/500x200/mods/'.$post['image'];
		$post['avatar_url'] = './avatar/32x32/'.$user->info('avatar_file', $post['author']);
		
		// Set up HTML for "devices" display in post list.
		$post['devices'] = explode( ',', $post['devices'] );
		$post['devices_list'] = '';
		
		foreach( $post['devices'] as $device ) $post['devices_list'] .= $device.' + ';
		$post['devices_list'] = trim( $post['devices_list'], ' + ' );
		
		$post['devices_html'] = $post['devices_list'] . "\n";
		
?>
    <div class="post">
        
        <div class="img">
            <div class="overlay">
                <a href="user/<?php echo $post['author_username']; ?>"><img src="<?php echo $post['avatar_url']; ?>" alt="" class="avatar" /></a>
                <h2><a href="<?php echo $post['url']; ?>"><?php echo $post['title']; ?></a></h2>
            </div>
            <?php echo $post['versions_html']; ?>
            <?php if ( $post['featured'] == 1 ) { ?><div class="featured"><i class="fa fa-star"></i> Featured Mod</div><?php } ?>
            <a href="<?php echo $post['url']; ?>"><img src="<?php echo $post['image_url']; ?>" /></a>
        </div>
        
        <div class="side">
            <ul>
                <li><i class="fa fa-mobile fa-fw"></i> <?php echo $post['devices_html']; ?></li>
                <li><i class="fa fa-thumbs-up fa-fw"></i> <span><?php echo $post['likes']; ?></span> likes</li>
                <li><i class="fa fa-eye fa-fw"></i> <span><?php echo $post['views']; ?></span> views</li>
                <li><i class="fa fa-comments fa-fw"></i> <span><?php echo $post['comments']; ?></span> comments</li>
            </ul>
            <a href="<?php echo $post['url']; ?>#download" class="view-bttn dl"><i class="fa fa-download"></i> Download <span><?php echo $post['downloads']; ?></span></a>
        </div>
        
    </div>
    
<?php
	
	} // END: Primary post loop.
	
} // END: If posts exist in database using given criteria.

?>
    
    <?php if ( isset( $pagination_html ) ) echo $pagination_html; ?>

</div>

<?php show_footer(); ?>