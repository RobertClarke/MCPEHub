<?php

class Comments {
	
	function __construct($db, $user) {
		$this->db = $db;
		$this->user = $user;
	}
	
	public function show($post, $type) {
		
		$comments = $this->db->from('comments')->where(['post' => $post, 'type' => $type])->fetch();
		$num = $this->db->affected_rows;
		
		// Grab author info on post.
		$owner = $this->db->select(['id', 'author'])->from('content_'.$type.'s')->where(['id' => $post])->fetch()[0];
		$owner = $owner['author'];
		
?>
<div id="comments">
    <div class="title">
        <h3><?php echo $num; ?> Comments</h3>
        <div class="links">
            <a href="/<?php echo $type; ?>s" class="bttn mini green toggle-comment"><i class="fa fa-plus"></i> Post New Comment</a>
        </div>
    </div>
    
    <script src="/assets/js/tinymce/tinymce.min.js"></script>
    <script>
tinymce.init({
	selector: "textarea.visual-comment",
	height: "120px",
	width: "100%",
	theme: "modern",
	skin: "light",
	plugins: ["link smileys paste"],
	toolbar: "bold underline italic strikethrough | smileys | bullist numlist | undo redo",
	statusbar: false,
	menubar: false,
	paste_as_text: true,
	object_resizing : false
});
    </script>
    
    <div id="new-comment">
        <h4>Post Comment</h4>
        <form action="/action/comment?post=<?php echo $post; ?>&type=<?php echo $type; ?>" method="POST">
            <div class="txt-cont">
                <textarea name="comment" id="comment" class="visual-comment"></textarea>
            </div>
            <div class="submit"><button type="submit" class="bttn big gold"><i class="fa fa-comments"></i> Post Comment</button></div>
        </form>
    </div>
<?php

if ( $num == 0 ) echo '<p>No one has commented on this '.$type.' yet.';
else {
	
	// HTMLPurifier initialize.
	require_once( './core/htmlpurifier/HTMLPurifier.standalone.php' );
	$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
	
	foreach ( $comments as $id => $c ) {
		
		$post_owner = ( $c['user'] == $owner ) ? TRUE : FALSE;
		
		$c['author'] = $this->user->info('username', $c['user']);
		$c['author_img'] = '/avatar/72x72/'.$this->user->info('avatar', $c['user']);
		
		$c['comment'] = $purifier->purify($c['comment']);
		
?>
<div class="comment<?php if ($post_owner) echo ' poster'; ?>">
    <div class="top">
        <a href="/user/<?php echo $c['author']; ?>"><img src="<?php echo $c['author_img']; ?>" alt="<?php echo $c['author']; ?>" width="36" height="36"></a>
        <p>
            <span class="poster"><a href="/user/<?php echo $c['author']; ?>"><?php echo $c['author']; ?></a><?php echo $this->user->badges($c['author']); ?></span>
            <span class="posted"><?php echo since(strtotime($c['posted'])); ?></span>
        </p>
    </div>
    <div class="content">
<?php echo $c['comment']; ?>
    </div>
</div>
<?php
		
	} // End: Comments foreach loop.
	
} // End: "No one has commented on this post yet." ?>
</div>
<?php
		
	}
	
}

?>