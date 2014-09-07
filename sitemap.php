<?php

require_once( 'core.php' );

// Generate XML file.
$xml = '<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.8">
	<url>
		<loc>http://mcpehub.com/</loc>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/links</loc>
		<changefreq>always</changefreq>
		<priority>1</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/maps</loc>
		<changefreq>always</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/seeds</loc>
		<changefreq>always</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/textures</loc>
		<changefreq>always</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/skins</loc>
		<changefreq>always</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/mods</loc>
		<changefreq>always</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/servers</loc>
		<changefreq>always</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/how-to-install-maps</loc>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/how-to-install-texture-packs</loc>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/how-to-install-mods</loc>
		<changefreq>weekly</changefreq>
		<priority>0.6</priority>
	</url>
	<url>
		<loc>http://mcpehub.com/submit</loc>
	</url>
';

// Generate XML data for all posts.
$q_cols = 'slug,submitted,published,edited';
$q_where = 'active = "1"';

$posts = $db->query('
	(SELECT "map"	 	AS type, '.$q_cols.' FROM `content_maps` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "seed" 		AS type, '.$q_cols.' FROM `content_seeds` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "texture" 	AS type, '.$q_cols.' FROM `content_textures` WHERE '.$q_where.') UNION ALL
	(SELECT "skin" 		AS type, '.$q_cols.' FROM `content_skins` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "mod" 		AS type, '.$q_cols.' FROM `content_mods` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "server" 	AS type, '.$q_cols.' FROM `content_servers`  WHERE '.$q_where.')
')->fetch();

foreach( $posts as $id => $post ) {
	$xml .= '<url>';
	$xml .= '<loc>http://mcpehub.com/'.$post['type'].'/'.$post['slug'].'</loc>';
	
	// Determine which time to use as lastedited.
	if ( $post['edited'] != 0 ) $last_mod = $post['edited'];
	else if ( $post['published'] != 0 ) $last_mod = $post['published'];
	else $last_mod = $post['submitted'];
	
	$last_mod = date( 'Y-m-d', strtotime( $last_mod ) );
	
	$xml .= '<lastmod>'.$last_mod.'</lastmod>';
	
	$xml .= '</url>';
}

/*$users = $db->select('username')->from('users')->where('level > "-1"')->fetch();

foreach( $users as $id => $user ) {
	$xml .= '<url>';
	$xml .= '<loc>http://mcpehub.com/user/'.$user['username'].'</loc>';
	$xml .= '</url>';
}*/

$xml .= '</urlset>';

echo $xml;

?>