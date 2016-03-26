<?php

/**
  * Moderator Tracker (Ghetto rigged for the time being)
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');

show_header('Moderation Tracker', TRUE, ['body_id' => 'dashboard', 'title_main' => 'Moderation Tracker', 'title_sub' => 'Moderator Panel']);

$users = $db->from('users')->order_by('username DESC')->where(['level' => 1])->fetch();

$q_where = 'submitted > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND editor_id != 0';

$posts = $db->query('
	(SELECT "maps"	 	AS type, id, editor_id, submitted FROM `content_maps` 	  	WHERE '.$q_where.') UNION ALL
	(SELECT "seeds" 	AS type, id, editor_id, submitted FROM `content_seeds`     WHERE '.$q_where.') UNION ALL
	(SELECT "textures" 	AS type, id, editor_id, submitted FROM `content_textures` 	WHERE '.$q_where.') UNION ALL
	(SELECT "skins" 	AS type, id, editor_id, submitted FROM `content_skins` 	WHERE '.$q_where.') UNION ALL
	(SELECT "mods" 		AS type, id, editor_id, submitted FROM `content_mods` 	    WHERE '.$q_where.') UNION ALL
	(SELECT "servers" 	AS type, id, editor_id, submitted FROM `content_servers`   WHERE '.$q_where.')
')->fetch();

$track = [];

foreach ( $users as $u ) {
	$track[$u['id']] = 0;
}

foreach ( $posts as $p ) {
	$track[ $p['editor_id'] ] += 1;
}

?>
<div id="p-title">
    <h1>Moderation Tracker</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>

<div class="user-list">
	<?php $error->display(); ?>
	<ul>
<?php

foreach( $users as $i => $p ) {
	echo '<li><a href="/user/'.$p['username'].'"><div class="img"><img src="/avatar/96x96/'.$p['avatar'].'" /></div><p>'.$p['username'].'<span style="float:right">'.$track[$p['id']].'</span></p></a></li>';
}

?>
	</ul>
</div>

<?php show_footer(); ?>
