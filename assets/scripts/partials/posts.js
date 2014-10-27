/** POSTS LISTS **/

// Changing title in post list based on length.
$('#posts .over h2').each(function() {
	
	// Check if post more than 2 lines.
	if ( $(this).height() > 25 ) {
		
		$(this).addClass('multi');
		
		// Check if post has smaller font but 1 line.
		if ( $(this).height() < 17 ) { $(this).addClass('solo'); }
	}
});

$('a.search').click(function() {
	$('#search').slideToggle();
	$('#search input#search').focus();
	event.preventDefault();
});

/** INDIVIDUAL POSTS **/

// Changing title in post based on length.
$('#p-title.solo h1').each(function() {
	
	// Check if post more than 2 lines.
	if ( $(this).height() > 30 ) {
		
		$(this).addClass('multi');
		
		// Check if post has smaller font but 1 line.
		if ( $(this).height() < 40 ) { $(this).addClass('solo'); }
	}
});

$('#comments a.toggle-comment').click(function() {
	
	$('#new-comment').slideToggle(400);
	
	event.preventDefault();
	
});

// Liking posts.
$('#post a.like').click(function() {
	
	var p_cont = $(this).closest('#post');
	
	var p_id = p_cont.data('id');
	var p_type = p_cont.data('type');
	
	$.ajax({
		
		type:	'GET',
		cache:	false,
		url:	'/ajax/like',
		data:	'post='+p_id+'&type='+p_type,
		
		success: function(data) {
			
			if ( data === 'liked' ) {
				$('#details a.like', p_cont).html('<i class="fa fa-thumbs-up"></i> Liked').addClass('green');
				$('#p-title a.like', p_cont).html('<i class="fa fa-thumbs-up"></i> Liked');
				$('#p-title .likes span').html(parseInt($('#p-title .likes span').html(), 10)+1);
			} else if ( data === 'unliked' ) {
				$('#details a.like', p_cont).html('<i class="fa fa-thumbs-up"></i> Like').removeClass('green');
				$('#p-title a.like', p_cont).html('<i class="fa fa-thumbs-up"></i> Like');
				$('#p-title .likes span').html(parseInt($('#p-title .likes span').html(), 10)-1);
			} else if ( data === 'auth' ) {
				$('#modal-auth').modal('show');
			} else { // Error
				$('#modal-error').modal('show');
			}
			
		}
		
	});
	
	event.preventDefault();
	
});

// Favoriting posts.
$('#post a.fav').click(function() {
	
	var p_cont = $(this).closest('#post');
	
	var p_id = p_cont.data('id');
	var p_type = p_cont.data('type');
	
	$.ajax({
		
		type:	'GET',
		cache:	false,
		url:	'/ajax/favorite',
		data:	'post='+p_id+'&type='+p_type,
		
		success: function(data) {
			
			if ( data === 'favorited' ) {
				$('#details a.fav', p_cont).html('<i class="fa fa-heart"></i> Favorited').addClass('red');
			} else if ( data === 'unfavorited' ) {
				$('#details a.fav', p_cont).html('<i class="fa fa-heart"></i> Favorite').removeClass('red');
			} else if ( data === 'auth' ) {
				$('#modal-auth').modal('show');
			} else { // Error
				$('#modal-error').modal('show');
			}
			
		}
		
	});
	
	event.preventDefault();
	
});

// Following users.
$('a.follow').click(function() {
	
	var following = $(this).data('following');
	var button = $(this);
	
	$.ajax({
		
		type:	'GET',
		cache:	false,
		url:	'/ajax/follow',
		data:	'follow='+following,
		
		success: function(data) {
			
			if ( data === 'followed' ) {
				button.html('<i class="fa fa-check"></i> Following').addClass('green');
			} else if ( data === 'unfollowed' ) {
				button.html('<i class="fa fa-rss"></i> Follow').removeClass('green');
			} else if ( data === 'auth' ) {
				$('#modal-auth').modal('show');
			} else { // Error
				$('#modal-error').modal('show');
			}
			
		}
		
	});
	
	event.preventDefault();
	
});