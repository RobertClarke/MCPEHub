<?php

/**
 * Mod Post Page
 *
 * Any changes to the output of this content page are to be made
 * in /core/classes/post.php in the PostPage class.
**/

require_once('loader.php');

// Create and output a post page via PostPage object
$post = new PostPage('mod');