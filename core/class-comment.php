<?php

class Comment {
	
	private $db;
	public function __construct( $db, $user ) {
		
		$this->db = $db;
		$this->user = $user;
		
		$this->comment_missing = isset($_GET['comment_missing']) ? $_GET['comment_missing'] : null;
		$this->comment_posted = isset($_GET['comment_posted']) ? $_GET['comment_posted'] : null;
		$this->comment_denied = isset($_GET['comment_denied']) ? $_GET['comment_denied'] : null;
		
	}
	
	// Function to output HTML that displays content comment form.
	function commentForm( $post_id, $post_type, $content='' ) {
		
?>
    <div id="post-comment">
        
        <h2>Post Comment</h2>
<?php
		
		// Show comment form for logged in users only.
		if ( $this->user->loggedIn() ) {
			
			if ( $this->comment_missing ) echo '<div class="message error"><i class="fa fa-times fa-fw"></i> You must enter a comment!</div>';
			else if ( $this->comment_posted ) echo '<div class="message success"><i class="fa fa-check fa-fw"></i> Your comment has been posted!</div>';
			else if ( $this->comment_denied ) echo '<div class="message error"><i class="fa fa-cross fa-fw"></i> You\'ve already commented too much on this post!</div>';
			
?>
    <div class="post-comment">
	        
	        <div class="avatar"><img src="./core/timthumb.php?src=../uploads/avatars/<?php echo $this->user->info()['avatar_file']; ?>&h=50&w=50&zc=1" /></div>
	        
	        <div class="input">
	            <form action="comment.php?post=<?php echo $post_id; ?>&type=<?php echo $post_type; ?>" method="POST" class="form">
	                <input type="hidden" name="post_id" value="" />
	                <input type="hidden" name="post_type" value="map" />
	                <textarea name="comment" id="comment" class="visual-comment"><?php echo $content; ?></textarea>
	                <input type="submit" name="submit" id="submit" class="submit left button" value="Post Comment" />
	            </form>
	            
	        </div>
	        
        </div>
<?php
		} else { // User not logged in, show message asking to join.
			
?>
    <div class="message info"><i class="fa fa-comments fa-fw"></i> <a href="register.php">Sign up</a> or <a href="login.php">sign in</a> to post a comment.</div>
<?php
			
		}
		
?>
        <div class="clear"></div>
        
    </div>
<?php
		
	}
	
	function showComments( $post_id, $post_type ) {
		
		$post = $this->db->select( array( 'id', 'author_id' ) )->from( 'content_' . $post_type . 's' )->where( array( 'id' => $post_id ) )->fetch()[0];
		$post_owner = $post['author_id'];
		
		// Getting number of comments for the post.
		$comments = $this->db->from( 'comments' )->where( array( 'post_id' => $post_id, 'post_type' => $post_type ) )->fetch();
		$post_comments = $this->db->affected_rows;
		
?>
<div id="post-comments">
        
        <h2><?php echo ( $post_comments != 0 ) ? $post_comments : 'No'; ?> Comment<?php if ( $post_comments != 1 ) echo 's'; ?></h2>
        
        <?php if ( $post_comments == 0 ) { // No comments on post. ?>
	    
	    There are no comments on this post.<br /><br />
	    
        <?php
        
        } else { // Comments on post.
	        
	        foreach ( $comments as $id => $comment ) { // Comments foreach.
	        	$comment['author'] = $this->user->info( $comment['user_id'] )['username'];
				
				// Check if user owns post.
				if ( $post_owner == $comment['user_id'] ) $post_owned = TRUE;
				else $post_owned = FALSE;
				
        ?>
        
        <div class="comment">
            
            <div class="user-info">
                
                <div class="avatar"><a href="user/<?php echo $comment['author']; ?>"><img src="core/timthumb.php?src=../uploads/avatars/<?php echo $this->user->info( $comment['user_id'] )['avatar_file']; ?>&h=32&w=32&zc=1" /></a></div>
                
                <span class="user">
                    <a href="user/<?php echo $comment['author']; ?>"><?php echo $comment['author']; ?></a>
                    
                    <?php if ( $this->user->isAdmin( $this->user->info( $comment['user_id'] )['id'] ) ) { ?>
                    <span class="rank admin"><i class="fa fa-star fa-fw"></i> Admin</span>
                    <?php } else if ( $this->user->isMod( $this->user->info( $comment['user_id'] )['id'] ) ) { ?>
                    <span class="rank mod"><i class="fa fa-star fa-fw"></i> Mod</span>
                    <?php } ?>
                    
                    <?php // Verified badge code coming soon. ?>
                    
                    <?php if ( $post_owned ) { ?>
                    <span class="rank author"><i class="fa fa-pencil fa-fw"></i> Post Author</span>
                    <?php } ?>
                    
                    <span class="date"><?php echo time_since( strtotime( $comment['posted'] ) ); ?></span>
                    <span class="reply"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>#post-comment" onClick="comment_reply('<?php echo $comment['author']; ?>');">Reply</a></span>
                </span>
                
            </div>
            
            <div class="comment-content">
                
	        <?php
	        
	        $comment_content = html_entity_decode( $comment['comment'] );
	        
	        // Adding links to @usernames.
	        $comment_content = preg_replace('/(?<=^|\s)@([a-z0-9_]+)/i', '<a href="/user/$1">@$1</a>', $comment_content );
	        
	        // Changing YouTube URLs into embeds.
	        $comment_content = preg_replace('~
	        # Match non-linked youtube URL in the wild. (Rev:20130823)
	        https?://         # Required scheme. Either http or https.
	        (?:[0-9A-Z-]+\.)? # Optional subdomain.
	        (?:               # Group host alternatives.
	          youtu\.be/      # Either youtu.be,
	        | youtube         # or youtube.com or
	          (?:-nocookie)?  # youtube-nocookie.com
	          \.com           # followed by
	          \S*             # Allow anything up to VIDEO_ID,
	          [^\w\s-]       # but char before ID is non-ID char.
	        )                 # End host alternatives.
	        ([\w-]{11})      # $1: VIDEO_ID is exactly 11 chars.
	        (?=[^\w-]|$)     # Assert next char is non-ID or EOS.
	        (?!               # Assert URL is not pre-linked.
	          [?=&+%\w.-]*    # Allow URL (query) remainder.
	          (?:             # Group pre-linked alternatives.
	            [\'"][^<>]*>  # Either inside a start tag,
	          | </a>          # or inside <a> element text contents.
	          )               # End recognized pre-linked alts.
	        )                 # End negative lookahead assertion.
	        [?=&+%\w.-]*        # Consume any URL (query) remainder.
	        ~ix', 
	        '<iframe width="560" height="315" style="margin: 10px 0;" src="//www.youtube-nocookie.com/embed/$1?rel=0" frameborder="0" allowfullscreen></iframe><div class="clear"></div>',
	        $comment_content);
	        
	        echo $comment_content;
	        
	        ?>
                
            </div>
            
        </div>
        
        <?php } /* END: Comments foreach. */ } // END: Comments on post. ?>
        
    </div>
<?php
		
	}
	
}

?>