$(function() {
	
	// Delete confirmation in dash.
	$('.dash a.del').click(function(){
		
		// Set var for the div which we're working with.
		var thisconf = $(this).closest('.post').find('.delconf');
		
		$(this).closest('.post').find('.rejconf').slideUp(500);
		
		// Slide up any confirmations already down.
		$('.delconf').not(thisconf).slideUp(500);
		
		// Slide toggle the confirmation area.
		$(thisconf).slideToggle(500);
		
		event.preventDefault();
		
	});
	
	// Reject confirmation in dash.
	$('.dash a.reject').click(function(){
		
		// Set var for the div which we're working with.
		var thisconf = $(this).closest('.post').find('.rejconf');
		
		$(this).closest('.post').find('.delconf').slideUp(500);
		
		// Slide up any confirmations already down.
		$('.rejconf').not(thisconf).slideUp(500);
		
		// Slide toggle the confirmation area.
		$(thisconf).slideToggle(500);
		
		event.preventDefault();
		
	});
	
});



function toggleLike( $post_id, $post_type ) {
	
	

	
	
	$.ajax( {
		
		type: 		'POST',
		url: 		'action.php',
		dataType:	'json',
		data: 		'action=like&post_id=' + $post_id + '&post_type=' + $post_type,
		
		// What to do when AJAX is returned.
		success: function( data ) {
			
			$('#post-title a.like, #post-title .thumb').each(function() { $(this).toggleClass('active') } );
			
			var count = $('#post-title .likes .count');
			var num = parseInt( count.text() );
			
			if ( data.status == 'liked' ) {
				
				//toggleError( '<i class="fa fa-thumbs-up"></i> You\'ve liked this post!', 'success' );
				
				$('#post-title a.like').attr('data-tip', 'Unlike Post');
				$('#post-title a.like .tipr_content').text('Unlike Post');
				
				$('#post-title a.like i').addClass('fa-thumbs-down');
				$('#post-title a.like i').removeClass('fa-thumbs-up');
				
				count.text( num + 1 );
				
				
			} else if ( data.status == 'unliked' ) {
				
				//toggleError( '<i class="fa fa-thumbs-down"></i> You\'ve unliked this post.', 'success' );
				
				$('#post-title a.like').attr('data-tip', 'Like Post');
				$('#post-title a.like .tipr_content').text('Like Post');
				
				$('#post-title a.like i').removeClass('fa-thumbs-down');
				$('#post-title a.like i').addClass('fa-thumbs-up');
				
				count.text( num - 1 );
				
			}
			
		}
		
	});
	
	event.preventDefault();
	
}


function toggleFav( $post_id, $post_type ) {
	
	$.ajax( {
		
		type: 		'POST',
		url: 		'action.php',
		dataType:	'json',
		data: 		'action=fav&post_id=' + $post_id + '&post_type=' + $post_type,
		
		// What to do when AJAX is returned.
		success: function( data ) {
			
			$('#post-title a.fav').toggleClass('active');
			
			if ( data.status == 'favorited' ) {
				
				//toggleError( '<i class="fa fa-heart"></i> You\'ve favorited this post!', 'success' );
				
				$('#post-title a.fav').attr('data-tip', 'Unfavorite Post');
				$('#post-title a.fav .tipr_content').text('Unfavorite Post');
				
				
			} else if ( data.status == 'unfavorited' ) {
				
				//toggleError( '<i class="fa fa-heart-o"></i> You\'ve unfavorited this post.', 'success' );
				
				$('#post-title a.fav').attr('data-tip', 'Favorite Post');
				$('#post-title a.fav .tipr_content').text('Favorite Post');
				
			}
			
		}
		
	});
	
	event.preventDefault();
	
}




function toggleSub( $author_id ) {
	
	$.ajax( {
		
		type: 		'POST',
		url: 		'action.php',
		dataType:	'json',
		data: 		'action=subscribe&user_subscribed=' + $author_id,
		
		// What to do when AJAX is returned.
		success: function( data ) {
			
			$('.author a.sub').toggleClass('active');
			
			if ( data.status == 'subscribed' ) {
				
				$('.author a.sub').html('<i class="fa fa-check"></i> Subscribed');
				
				
			} else if ( data.status == 'unsubscribed' ) {
				
				$('.author a.sub').html('<i class="fa fa-rocket"></i> Subscribe');
				
			}
			
		}
		
	});
	
	event.preventDefault();
	
}


$(function() {

$('#post-title a.like').hover(

function() {
	
	if ( $(this).hasClass('active') ) {
		$('i', this).removeClass('fa-thumbs-up');
		$('i', this).addClass('fa-thumbs-down');
	}
	
},

function() {
	
	if ( $(this).hasClass('active') ) {
		$('i', this).removeClass('fa-thumbs-down');
		$('i', this).addClass('fa-thumbs-up');
	}
	
}

);

});






/* ADMIN */

function toggleFeature( $post_id, $post_type ) {
	
	$.ajax( {
		
		type: 		'POST',
		url: 		'action.php',
		dataType:	'json',
		data: 		'action=feature&post_id=' + $post_id + '&post_type=' + $post_type,
		
		// What to do when AJAX is returned.
		success: function( data ) {
			
			$('.buttons.admin a.feature').toggleClass('gold');
			
			if ( data.status == 'featured' ) {
				
				//toggleError( '<i class="fa fa-star"></i> You\'ve featured this post!', 'success' );
				$('.buttons.admin a.feature').html('<i class="fa fa-star fa-fw"></i> Unfeature Post');
				
				
			} else if ( data.status == 'unfeatured' ) {
				
				//toggleError( '<i class="fa fa-star-o"></i> You\'ve unfeatured this post.', 'success' );
				$('.buttons.admin a.feature').html('<i class="fa fa-star fa-fw"></i> Feature Post');
				
			}
			
		}
		
	});
	
	event.preventDefault();
	
}
