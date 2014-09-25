<?php

/**
  
  * Homepage
  *
  * Displays featured slider + featured posts on website.
  
**/

require_once('core.php');

show_header(NULL, FALSE, ['no_sidebar' => TRUE]);

$q_where = 'active = "1"';

// Count each kind of post.
$posts_count = $db->query('
	(SELECT "maps"	 	AS type, COUNT(*) FROM `content_maps` 	  WHERE '.$q_where.') UNION ALL
	(SELECT "seeds" 	AS type, COUNT(*) FROM `content_seeds` 	  WHERE '.$q_where.') UNION ALL
	(SELECT "textures" 	AS type, COUNT(*) FROM `content_textures` WHERE '.$q_where.') UNION ALL
	(SELECT "skins" 	AS type, COUNT(*) FROM `content_skins` 	  WHERE '.$q_where.') UNION ALL
	(SELECT "mods" 		AS type, COUNT(*) FROM `content_mods` 	  WHERE '.$q_where.') UNION ALL
	(SELECT "servers" 	AS type, COUNT(*) FROM `content_servers`  WHERE '.$q_where.')
')->fetch();

// Throw counts into an array.
$post_count = array();
foreach ( $posts_count as $post ) $post_count[$post['type']] = $post['COUNT(*)'];

// Set default values for database query.
$q_cols = 'id, title, slug, author, images, views, submitted, featured_time';
$q_where = 'active = "1" AND featured = "1"';

$q_end = ' ORDER BY featured_time DESC LIMIT 3';

// Grab all featured posts in each category.
$db_posts = $db->query('
	(SELECT "map"	 	AS type, '.$q_cols.' FROM `content_maps` 	 WHERE '.$q_where.$q_end.') UNION ALL
	(SELECT "seed" 		AS type, '.$q_cols.' FROM `content_seeds` 	 WHERE '.$q_where.$q_end.') UNION ALL
	(SELECT "texture" 	AS type, '.$q_cols.' FROM `content_textures` WHERE '.$q_where.$q_end.') UNION ALL
	(SELECT "skin" 		AS type, '.$q_cols.' FROM `content_skins` 	 WHERE '.$q_where.$q_end.') UNION ALL
	(SELECT "mod" 		AS type, '.$q_cols.' FROM `content_mods` 	 WHERE '.$q_where.$q_end.') UNION ALL
	(SELECT "server" 	AS type, '.$q_cols.' FROM `content_servers`  WHERE '.$q_where.$q_end.')
	
	ORDER BY `featured_time` DESC
	
')->fetch();

// Grab additional info, organize posts array for use.
foreach( $db_posts as $id => $post ) {
	
	// Determine number of likes and comments on post.
	$q_where = 'post = "'.$post['id'].'" AND type = "map"';
	$count_vars = $db->query('
		(SELECT "likes"		AS type, COUNT(*) FROM `likes`		WHERE '.$q_where.') UNION ALL
		(SELECT "comments"	AS type, COUNT(*) FROM `comments`	WHERE '.$q_where.')
	')->fetch();
	
	foreach( $count_vars as $var ) $post[ $var['type'] ] = $var['COUNT(*)'];
	
	$post['author_username'] = $user->info('username', $post['author']);
	$post['url'] = $post['type'].'/' . $post['slug'];
	
	// Grab first image from images for display.
	$post['images'] = explode( ',', $post['images'] );
	$post['image'] = urlencode( $post['images'][0] );
	
	$post['image_slide_url'] = './uploads/700x280/'.$post['type'].'s/'.$post['image'];
	$post['image_url'] = './uploads/130x80/'.$post['type'].'s/'.$post['image'];
	
	$db_posts[$id] = $post;

}

// Putting each post into its category in the main array.
$posts = array();
foreach( $db_posts as $post ) $posts[ $post['type'].'s' ][] = $post;


//print_r($posts);

// Picking out random featured posts out of those types for slideshow.
$slideshow = array();
foreach( $posts as $type => $the_posts ) {
	$rand = $posts[$type][array_rand($the_posts)];
	$slideshow[ $rand['type'] ] = $rand;
}

// Array for showing icons in slideshow.
$s_label = array(
	'map' => '<i class="fa fa-map-marker fa-fw"></i> Featured Map',
	'seed' => '<i class="fa fa-leaf fa-fw"></i> Featured Seed',
	'texture' => '<i class="fa fa-magic fa-fw"></i> Featured Texture',
	'skin' => '<i class="fa fa-smile-o fa-fw"></i> Featured Skin',
	'mod' => '<i class="fa fa-codepen fa-fw"></i> Featured Mod',
	'server' => '<i class="fa fa-gamepad fa-fw"></i> Featured Server'
);

function show_featured( $type ) {
	
	global $posts;
	
	$allowed = array( 'maps', 'seeds', 'textures', 'skins', 'mods', 'servers' );
	if ( !in_array( $type, $allowed ) ) $type = 'maps';
	
	if ( isset( $posts[ $type ] ) && count( $posts[ $type ] ) != 0 ) {
	
	foreach( $posts[ $type ] as $post ) {
		$dot = ( strlen( $post['title'] ) > 35 ) ? '...' : ''; // '...' After post title if too long.
		
?>
            	<div class="post clearfix">
            	    <div class="img">
            	       <a href="<?php echo $post['url']; ?>">
            	           <img src="<?php echo $post['image_url']; ?>" alt="<?php echo $post['title']; ?>" />
            	       </a>
            	   </div>
            	   <div class="info">
            	       <h3><a href="<?php echo $post['url']; ?>"><?php echo substr($post['title'], 0, 35).$dot; ?></a></h3>
            	       <p>by <a href="user/<?php echo $post['author_username']; ?>"><?php echo $post['author_username']; ?></a></p>
            	       <ul>
	            	       <li><i class="fa fa-thumbs-up"></i> <strong><?php echo $post['likes']; ?></strong> likes</li>
	            	       <li><i class="fa fa-eye"></i> <strong><?php echo $post['views']; ?></strong> views</li>
	            	       <li><i class="fa fa-comments"></i> <strong><?php echo $post['comments']; ?></strong> comments</li>
            	       </ul>
            	   </div>
            	</div>
<?php
	} // End foreach. 
	
	}
}

?>

<div id="home-slider">
    <div class="flexslider">
      <ul class="slides">
<?php foreach( $slideshow as $type => $slide ) { ?>
        <li>
    	    <a href="<?php echo $slide['url']; ?>"><img src="<?php echo $slide['image_slide_url']; ?>" /></a>
    	    <p class="slide-caption">
    	        <a href="<?php echo $slide['url']; ?>"><strong><?php echo $s_label[$slide['type']]; ?>:</strong> <?php echo $slide['title']; ?></a>
    	        <span>
    	            <a href="user/<?php echo $slide['author_username']; ?>">Posted by: <?php echo $slide['author_username']; ?></a>
    	            <a href="<?php echo $slide['url']; ?>"><i class="fa fa-eye fa-fw"></i> <?php echo $slide['views']; ?></a>
    	            <a href="<?php echo $slide['url']; ?>#comments"><i class="fa fa-comments fa-fw"></i> <?php echo $slide['comments']; ?></a>
    	            <a href="<?php echo $slide['url']; ?>" class="likes"><i class="fa fa-thumbs-up fa-fw"></i> <?php echo $slide['likes']; ?></a>
    	        </span>
    	    </p>
		</li>
<?php } // END: Slideshow foreach. ?>
      </ul>
    </div>
</div>

<div class="advrt home-slide">
    <ins class="adsbygoogle" style="display:inline-block;width:300px;height:250px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="3678350279"></ins>
    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>

<div class="clearfix"></div>

<div id="home-posts">
    <div class="half">
        <div class="box">
            <h2><i class="fa fa-map-marker fa-fw"></i> Featured Maps</h2>
<?php show_featured('maps'); ?>
            <div class="bttn-more"><a href="/maps" class="view-bttn dl">Browse Maps (<?php echo $post_count['maps']; ?>)</a></div>
        </div>
        <div class="box">
            <h2><i class="fa fa-gamepad fa-fw"></i> Featured Servers</h2>
<?php show_featured('servers'); ?>
            <div class="bttn-more"><a href="/servers" class="view-bttn dl">Browse Servers (<?php echo $post_count['servers']; ?>)</a></div>
        </div>
        <div class="box">
            <h2><i class="fa fa-codepen fa-fw"></i> Featured Mods</h2>
<?php show_featured('mods'); ?>
            <div class="bttn-more"><a href="/mods" class="view-bttn dl">Browse Mods (<?php echo $post_count['mods']; ?>)</a></div>
        </div>
    </div>
    <div class="half last">
        <div class="box">
            <h2><i class="fa fa-leaf fa-fw"></i> Featured Seeds</h2>
<?php show_featured('seeds'); ?>
            <div class="bttn-more"><a href="/seeds" class="view-bttn dl">Browse Seeds (<?php echo $post_count['seeds']; ?>)</a></div>
        </div>
        <div class="box">
            <h2><i class="fa fa-magic fa-fw"></i> Featured Textures</h2>
<?php show_featured('textures'); ?>
            <div class="bttn-more"><a href="/textures" class="view-bttn dl">Browse Textures (<?php echo $post_count['textures']; ?>)</a></div>
        </div>
        <div class="box">
            <h2><i class="fa fa-smile-o fa-fw"></i> Featured Skins</h2>
<?php show_featured('skins'); ?>
            <div class="bttn-more"><a href="/skins" class="view-bttn dl">Browse Skins (<?php echo $post_count['skins']; ?>)</a></div>
        </div>
    </div>
</div>

<?php show_footer(); ?>