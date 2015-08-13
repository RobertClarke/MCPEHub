<?php

/**
 * Page Structure Class
 *
 * Includes all functions required to include the header, footer and
 * any other required page elements. Allows for any public variables
 * to be passed on to the header and footer.
**/

class Page {

	// HTML title
	public $title		= '';

	// Whether or not to run authentication checks
	public $auth		= false;

	// HTML elements
	public $title_h1	= ''; // Leave blank to use the same value as $title
	public $title_h2	= '';

	// SEO elements
	public $seo_desc	= '';
	public $seo_tags	= '';

	// Basic page URL information
	public $current		= '';
	public $base		= '';
	public $url			= '';

	// Canonical URL tag for post pages
	public $canonical	= '';

	// <body> element tags
	public $body_id		= '';
	public $body_class	= '';

	public $no_wrap		= false;
	public $alt_body	= false;

	// Set to enable Facebook, Twitter and Google+ APIs in one call
	public $share_apis	= false;

	// Facebook Javascript API
	public $api_fb		= false;
	public $fb_title	= '';
	public $fb_desc		= '';
	public $fb_url		= '';
	public $fb_img		= '';
	public $fb_article	= false;

	// Twitter Javascript API
	public $api_twitter	= false;

	// Google+ Platform API
	public $api_google	= false;

	// Enqueued scripts
	public $scripts = [];

	/**
	 * Constructor
	 *
	 * Determines and stores the various variations of the current page value.
	 *
	 * @since 3.0.0
	**/
	public function __construct() {
		$this->current = trim( strtok( filter_input(INPUT_SERVER, 'REQUEST_URI'), '?' ), '/' );
		$this->base = 'http://' . filter_input(INPUT_SERVER, 'SERVER_NAME') . '/';
		$this->url = $this->base . $this->current;

		$this->cur = basename(filter_input(INPUT_SERVER, 'PHP_SELF'), '.php');
	}

	/**
	 * Display page header.
	 *
	 * Provides authentication before any page content is sent to the users
	 * browser. Includes the header template file afterwards.
	 *
	 * @since 3.0.0
	 *
	 * @param string $title Optional, sets the title of the page within the function
	 * @param boolean $override Optional, allows to fully overwrite the <title> tag
	**/
	public function header( $title='', $override=false ) {

		if ( $this->auth )
			login_redirect();

		// Default title, if value missing
		if ( empty($title) )
			$this->title = 'MCPE Hub | The #1 Minecraft PE Community';

		else {
			if ( !$override ) $this->title = $title.' | MCPE Hub';
			else $this->title = $title;
		}

		if ( empty($this->seo_desc) )
			$this->seo_desc = 'MCPE Hub is the #1 Minecraft PE community in the world, featuring seeds, maps, servers, skins, mods, and more.';

		if ( empty($this->seo_tags) )
			$this->seo_tags = 'minecraft pe, mcpe, minecraft, mcpehub';

		// Enables all sharing APIs in one call
		if ( $this->share_apis )
			$this->api_fb = $this->api_twitter = $this->api_google = true;

		include_once('core/structure/header.php');
	}

	/**
	 * Display page footer.
	 *
	 * Includes the footer template file.
	 *
	 * @since 3.0.0
	**/
	public function footer() {
		include_once('core/structure/footer.php');
	}

	/**
	 * Enqueue a given script to the footer of a page
	 *
	 * $script must be the filename, EXCLUDING the .js ending of the
	 * enqueued script. Accepts an array for multiple scripts.
	 *
	 * @since 3.0.0
	 *
	 * @param string|array $script String or array of scripts to include (without .js extensions)
	**/
	public function enqueue( $script ) {

		if ( !is_array($script) )
			$this->scripts[] = $script;

		else
			$this->scripts = array_merge( $this->scripts, $script );

		return;

	}

}