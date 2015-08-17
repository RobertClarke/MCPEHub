<?php

/**
 * PostPage Class
 *
 * An object called on post pages in order to display posts in a
 * simple, one line command. Any changes to post pages are to be
 * made here, and post pages are templated by the Smarty engine.
**/

class PostPage {

	/**
	 * Constructor
	 *
	 * Grabs all post info, computes template and outputs page contents for posts.
	 * This function only needs a $type to function, as it grabs any other info
	 * from the $_GET values. Page header and footer are output here as well.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type The type of post (ex: map)
	**/
	function __construct( $type ) {
		global $db, $page;

		// Check if post slug exists in url
		if ( !$slug = clean_slug(input_GET('post')) )
		    redirect('/'.$type.'s');

		$type_id = post_type_code($type);

		// Grab all info necessary to display post
		$post = $db->query('
			SELECT
				post.*,
				(SELECT COUNT(*) FROM likes WHERE post_id = post.id AND post_type = '.$type_id.') AS likes,
				(SELECT COUNT(*) FROM comments WHERE post_id = post.id AND post_type = '.$type_id.' AND status = 1) AS comments,
				(SELECT COUNT(*) FROM content_featured WHERE post_id = post.id AND post_type = '.$type_id.') AS featured,
				GROUP_CONCAT(filename ORDER BY img.post_id) AS images,
				(SELECT filename FROM content_images WHERE post_id = post.id AND post_type = '.$type_id.' AND featured = 1 LIMIT 1) AS image_featured
			FROM content_'.$type.' post
			LEFT OUTER JOIN content_images img ON
				img.post_id = post.id AND
				img.post_type = '.$type_id.'
			WHERE post.slug = "'.$slug.'" AND post.status <> "-2"
			GROUP BY post.id
		')->fetch_first();

		// Redirect based on post status
		if ( !$post )
		    redirect('/404');

		else if ( $post['author_id'] != logged_in('id') && $post['status'] == '-1' || $post['status'] == '-2' ) // Deleted or rejected
		    redirect('/410');

		$owner = ( $post['author_id'] == logged_in('id') );

		// Get author information
		$author = new User($post['author_id']);
		$post['author'] = $author->username;

		// Get post category information
		$post['category'] = get_category_name( $post['category'], $type );

		$post['url'] = SITEURL . '/'.$type.'/'.$slug;

		// Explode the string of images from database
		$post['images'] = explode(',', $post['images']);

		if ( !isset($post['image_featured']) )
			$post['image_featured'] = $post['images'][0];

		/** Display Post HTML ***/

		$page->body_id	= 'post-page';

		$page->title_h1	= $post['title'];
		$page->title_h2	= 'Minecraft PE '.ucwords($type);

		$page->seo_desc	= $post['category'].' Minecraft PE '.$type.' posted by '.$post['author'].' on MCPE Hub, the #1 Minecraft PE community in the world.';
		$page->seo_tags	= strtolower($post['category']).' minecraft pe '.$type.', '.strtolower($post['category']).' mcpe '.$type.', minecraft pe map, minecraft pe, mcpe, mcpe map';

		$page->fb_title	= $post['title'] .' | Minecraft PE '. ucwords($type).' on MCPE Hub';
		$page->fb_url	= $post['url'];
		$page->fb_img	= SITEURL . '/uploads/1200x630/'.$type.'/'.$post['image_featured'];

		$page->fb_article = true;
		$page->share_apis = true;

		$page->canonical = $post['url'];

		$page->header($post['title'] .' | Minecraft PE '.ucwords($type).' on MCPE Hub', true);

		$smarty = new Smarty;
		$smarty->setTemplateDir(CORE.'templates/');

		$smarty->assign('type',			$type);
		$smarty->assign('title',		$post['title']);
		$smarty->assign('author',		$post['author']);
		$smarty->assign('url',			$post['url']);
		$smarty->assign('views',		$post['views']);
		$smarty->assign('likes',		$post['likes']);
		$smarty->assign('comments',		$post['comments']);
		$smarty->assign('downloads',	$post['downloads']);
		$smarty->assign('avatar',		$author->data['avatar']);
		$smarty->assign('images',		$post['images']);
		$smarty->assign('description',	$post['description']);

		// Generate post page
		$smarty->display('post.tpl');

		$page->footer();

	}

}

/**
 * Returns the numberic ID associated with a given post type
 *
 * Note: Leave $key empty to return all post types
 *
 * @since 3.0.0
 *
 * @param string $key The key to search for
 * @return int The numeric value for this post type (stored in DB), bool false on failiure
**/
function post_type_code( $key='' ) {
	global $db;

	// Check if already in cache
	if ( $codes = cache_get( 'post_type_codes', 'core' ) )
		return ( isset($codes[$key]) ) ? $codes[$key] : false;

	// Codes don't exist in cache, store them for future use
	else {

		$codes = [];
		$codes_db = $db->select(['key', 'value'])->from('content_types')->fetch();

		// Sort and store values in cache
		foreach ( $codes_db as $val ) {
			$codes[ $val['value'] ] = $val['key'];
		}

		cache_add( 'post_type_codes', $codes, 'core' );

		if ( !empty($key) )
			return ( isset($codes[$key]) ) ? $codes[$key] : false;

		else
			return $codes;

	}

}

/**
 * Returns the numberic ID associated with a given post type
 *
 * @since 3.0.0
 *
 * @param string $key (optional) The type of post to return categories for (ie: maps)
 * @return array Array of category keys and identifiers
**/
function get_categories( $post_type='' ) {
	global $db;

	$post_type = post_type_code($post_type);

	// Check if already in cache, fetch if needed
	if ( !$codes = cache_get( 'post_categories', 'core' ) ) {

		$codes = [];
		$codes_db = $db->from('content_categories')->fetch();

		// Sort and store values in cache
		foreach ( $codes_db as $val ) {
			$codes[ $val['post_type'] ][ $val['key'] ] = $val;
		}

		cache_add( 'post_categories', $codes, 'core' );

	}

	if ( !empty($post_type) )
		return $codes[$post_type];

	else
		return $codes;

}

/**
 * Returns the numberic ID associated with a given post category
 *
 * @since 3.0.0
 *
 * @param string $key The key to search for
 * @param string $post_type The post type
 * @return int The numeric value for this post category (stored in DB), int 0 on failiure
**/
function category_type_code( $key, $post_type ) {

	$post_type = post_type_code($post_type);
	$codes = get_categories();

	return ( isset($codes[$post_type][$key]) ) ? $codes[$post_type][$key]['id'] : 0;

}

/**
 * Returns the category object based on a given category num ID
 *
 * @since 3.0.0
 *
 * @param string $id The category ID to search for
 * @param string $post_type The post type
 * @return array Array from the content_categories table with info about the category
**/
function get_category_by_id( $id, $post_type ) {

	$post_type = post_type_code($post_type);
	$codes = get_categories();

	// Search for category ID in cache
	foreach ( $codes[$post_type] as $cat ) {
		if ( $cat['id'] == $id )
			return $cat;
	}

	return false;

}

/**
 * Returns the name of a category given a numeric ID and post type
 *
 * @since 3.0.0
 *
 * @param string $id The category ID to search for
 * @param string $post_type The post type
 * @return string Name of the category in the database
**/
function get_category_name( $id, $post_type ) {
	return get_category_by_id($id, $post_type)['name'];
}