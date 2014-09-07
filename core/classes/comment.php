<?php

class Comment {
	
	private $db;
	function __construct( $db, $user ) {
		
		$this->db = $db;
		$this->user = $user;
		
		$this->missing = isset($_GET['comment_missing']) ? TRUE : FALSE;
		$this->posted = isset($_GET['comment_posted']) ? TRUE : FALSE;
		$this->denied = isset($_GET['comment_denied']) ? TRUE : FALSE;
		
	}
	
	protected function message( $error, $type, $icon ) {
		echo '<div class="message '.$type.'"><i class="fa fa-'.$icon.' fa-fw"></i> '.$error.'</div>';
	}
	
	// Output HTML that displays comment form.
	public function comment_form( $post_id, $post_type, $content = '' ) {
		
?>
    <div id="comment-form">
        <h2>Post Comment</h2>
        <?php
		
		// Show comment form for logged in users only.
		if ( !$this->user->logged_in() )
			$this->message( '<a href="/register">Join Us</a> or <a href="/login">Sign In</a> to post comments on posts.', 'info', 'comments' );
		
		// User logged in, show form.
		else {
			
			// Show messages, as needed.
			if 		( $this->missing ) $this->message( 'You must enter a comment to post.', 'error', 'times' );
			else if ( $this->posted ) $this->message( 'Your comment has been successfully posted.', 'success', 'check' );
			else if ( $this->denied ) $this->message( 'You\'ve already commented too many times on this post.', 'error', 'times');
			
		?>
        <div class="input">
            <form action="/comment?post=<?php echo $post_id; ?>&type=<?php echo $post_type; ?>" method="POST" class="form">
                <div class="input-cont">
                    <textarea name="comment" id="comment" class="visual-comment"><?php echo $content; ?></textarea>
                    <p><img src="/avatar/32x32/<?php echo $this->user->info('avatar_file'); ?>" class="avatar" /> Posting this comment as <?php echo $this->user->info('username'); ?>.</p>
                </div>
                <button type="submit" name="submit" id="submit" class="bttn submit">Post Comment</button>
            </form>
        </div>
		<?php } // END: User logged in, show form. ?>
    </div>
<?php
		
	} // END: HTML comment form output.
	
	public function show_comments( $post_id, $post_type ) {
		
		// Grab owner's user id.
		$post = $this->db->select( array( 'id', 'author' ) )->from( 'content_' . $post_type . 's' )->where( array( 'id' => $post_id ) )->fetch()[0];
		$post_owner = $post['author'];
		
		// Getting comments from database + counting.
		$comments = $this->db->from( 'comments' )->where( array( 'post_id' => $post_id, 'post_type' => $post_type ) )->fetch();
		$post_comments = $this->db->affected_rows;
		
?>
    <div id="comments">
        <h2><?php echo ( $post_comments != 0 ) ? $post_comments : 'No'; ?> Comment<?php if ( $post_comments != 1 ) echo 's'; ?></h2>
        <?php
        
        // Show message if there are no comments on post.
        if ( $post_comments == 0 )
        	echo 'No one has commented on this post yet.';
        
        // Show list of comments.
        else {
	        
	        // HTMLPurifier initialize.
			$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
	        
	        foreach ( $comments as $id => $comment ) { // Comments foreach.
	        	
	        	// Check if the comment author is the owner of the given post.
	        	$post_owned = ( $comment['user_id'] == $post_owner ) ? TRUE : FALSE;
	        	
	        	// Convert comment author id to username for display.
	        	$comment['author'] = $this->user->info( 'username', $comment['user_id'] );
	        	$comment['author_img'] = $this->user->info( 'avatar_file', $comment['user_id'] );
				
				// Run comment through HTMLPurifier for security.
				$the_comment = $purifier->purify( $comment['comment'] );
				
				// Add @username links to comment.
				//$the_comment = preg_replace('/(?<=^|\s)@([a-z0-9_]+)/i', '<a href="/user/$1">@$1</a>', $the_comment);
				$the_comment = preg_replace('/@([a-z0-9_]+)/i', '<a href="/user/$1">@$1</a>', $the_comment);
				
        ?>
        
        <div class="comment">
            <div class="top clearfix">
                <a href="/user/<?php echo $comment['author']; ?>"><img src="/avatar/64x64/<?php echo $comment['author_img']; ?>" class="avatar" /></a>
                    <div class="info">
                        <a href="/user/<?php echo $comment['author']; ?>"><span class="comment-author"><?php echo $comment['author']; ?></span></a>
                        <?php echo $this->user->badges( $comment['author'] ); ?>
                        <?php if ( $post_owned ) echo '<span class="rank author"><i class="fa fa-pencil"></i>Author</span> '; ?>
                        <span class="ago"><?php echo time_since( strtotime( $comment['posted'] ) ); ?></span>
                    </div>
                </a>
            </div>
            <div class="the_comment">
                <?php echo $the_comment; ?>
            </div>
        </div>
        <?php } /* End: Comments foreach. */ } // End: Comments on post. ?>
    </div>
<?php	
	}
}
?>