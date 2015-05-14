<?php

/**
  * Post Class
  *
  * Enables post actions around the website.
**/

class Post {
	
	private $db;
	public function __construct( $db, $user ) {
		
		$this->db = $db;
		$this->user = $user;
		
	}
	
	public function cleanSlug( $slug ) {
		
		// Modify slug to remove "-" at end.
		if ( substr( $slug, -1) == '-' ) $slug = rtrim( $slug, '-' );
		
		// Modify slug to remove "-" at start.
		if ( substr( $slug, 0, 1 ) == '-' ) $slug = ltrim( $slug, '-');
		
		// Modify slug to lower case.
		$slug = strtolower( $slug );
		
		return $slug;
		
	}
	
	public function update_views( $post_id, $post_type ) {
		
		$current_views = explode( ',', $_COOKIE['mcpe_v'] );
		
		// New identifier codes (for shorter cookies).
		$codes = array( 'map' => 'mp', 'seed' => 'se', 'texture' => 'tx', 'skin' => 'sk', 'mod' => 'md', 'server' => 'sr' );
		
		// Convert post type to new identifier code.
		$post_iden = $codes[$post_type];
		
		// Post not in views, add to count.
		if ( !in_array( $post_iden . $post_id, $current_views ) ) {
			
			// Grab current view count and append by 1 view.
			$query = $this->db->select( 'views' )->from( 'content_'.$post_type.'s' )->where( array('id' => $post_id) )->fetch();
			$views = ( $query[0]['views'] + 1 );

			// Update view count.
			$this->db->where( array( 'id' => $post_id ) )->limit(1)->update( 'content_'.$post_type.'s', array( 'views' => $views ) );
			
			// Set new views cookie.
			if ( !empty( $_COOKIE['mcpe_v'] ) ) $cookie_value = $_COOKIE['mcpe_v'] .','. $post_iden . $post_id;
			else $cookie_value = $post_iden . $post_id;
			
			$_COOKIE['mcpe_v'] = $cookie_value;
			set_cookie( 'mcpe_v', $cookie_value, time() + (365 * 24 * 60 * 60) );
			
			return TRUE;
			
		}
		
		// Post already in views, don't add.
		else return FALSE;
		
	}
	
	public function update_downloads( $post_id, $post_type ) {
		
		$current_dls = explode( ',', $_COOKIE['mcpe_d'] );
		
		// New identifier codes (for shorter cookies).
		$codes = array( 'map' => 'mp', 'seed' => 'se', 'texture' => 'tx', 'skin' => 'sk', 'mod' => 'md', 'server' => 'sr' );
		
		// Convert post type to new identifier code.
		$post_iden = $codes[$post_type];
		
		// Post not in users downloads, add to count.
		if ( !in_array( $post_iden . $post_id, $current_dls ) ) {
			
			// Grab current downloads count and append by 1 download.
			$query = $this->db->select( 'downloads' )->from( 'content_'.$post_type.'s' )->where( array('id' => $post_id) )->fetch();
			$dls = ( $query[0]['downloads'] + 1 );
			
			// Update download count.
			$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', array( 'downloads' => $dls ) );
			
			// Set new downloads cookie.
			if ( !empty( $_COOKIE['mcpe_d'] ) ) $cookie_value = $_COOKIE['mcpe_d'] .','. $post_iden . $post_id;
			else $cookie_value = $post_iden . $post_id;
			
			$_COOKIE['mcpe_d'] = $cookie_value;
			set_cookie( 'mcpe_d', $cookie_value, time() + (365 * 24 * 60 * 60) );
			
			return TRUE;
			
		}
		
		// User already downloaded file, dont' add to count.
		else return FALSE;
		
	}
	
	public function mod_toolkit($post, $type, $p, $author) {
		
		global $user;
		
		if ( $user->is_mod() || $user->is_admin() ) {
		
?>
<div id="mod-tools">
    <h5>Moderation Toolkit</h5>
    <a href="/moderate-edit?post=<?php echo $post; ?>&type=<?php echo $type; ?>" class="bttn mid"><i class="fa fa-pencil"></i> Edit Post</a>
    <a href="#feature" class="feature bttn mid<?php echo ( $p['featured'] == 1 ) ? ' gold' : NULL; ?>"><?php echo ( $p['featured'] == 0 ) ? '<i class="fa fa-star"></i> Feature Post' : '<i class="fa fa-check"></i> Featured'; ?></a>
    <div class="side">
<?php if ( $type == 'map' ) { ?><a href="#tested" class="tested bttn mid tip<?php echo ( $p['tested'] == 1 ) ? ' green' : NULL; ?>" data-tip="Toggle 'Tested' Status"><i class="fa fa-toggle-<?php echo ( $p['tested'] == 1 ) ? 'on' : 'off'; ?> solo"></i></a><?php } ?>
        <a href="/moderate-suspend?user=<?php echo $author; ?>" class="bttn mid tip" data-tip="Suspend User"><i class="fa fa-gavel solo"></i></a>
        <a href="/moderate-delete?post=<?php echo $post; ?>&type=<?php echo $type; ?>" class="bttn mid tip" data-tip="Delete Post"><i class="fa fa-trash-o solo"></i></a>
<?php if ( $p['active'] != '-1' ) { ?>
        <a href="/moderate-toggle?post=<?php echo $post; ?>&type=<?php echo $type; ?>" class="bttn mid tip" data-tip="Reject Post"><i class="fa fa-times solo"></i></a>
<?php } ?>
        <a href="#toggle" class="toggle-approve bttn mid <?php echo ( $p['active'] == 0 ) ? 'green' : 'red'; ?>"><?php echo ( $p['active'] == 0 ) ? '<i class="fa fa-check"></i> Approve Post' : '<i class="fa fa-times"></i> Unapprove Post'; ?></a>
    </div>
</div>
<?php

		} 
		
	}
		
}

?>