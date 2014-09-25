// @codekit-append "./plugins/chosen.js", "./plugins/tipr.js", "./plugins/fileinput.js", "./plugins/flexslider.js";

$(function() {
	
	// Changing post title sizes/position based on length.
	$('.overlay h2').each(function() {
		
		// Check if post more than 2 lines.
		if ( $(this).height() > 25 ) {
			
			$(this).addClass('multi');
			
			// Check if post has smaller font but 1 line.
			if ( $(this).height() < 17 ) $(this).addClass('solo');
			
		}
		
	});
	
	// Search toggle.
	$('a.search-show').click(function(){
		$('.search').slideToggle(250);
		$('.search input#search').focus();
		event.preventDefault();
	});
	
	// Server refresh.
	if ( $('#server-list').length) {
		
		var servers = [];
		
		$('#server-list').children('.post').each(function() {
			servers.push($(this).data('server'));
		});
		
		$.ajax({
			
			type:	'GET',
			cache:	false,
			url:	'/core/actions/ping',
			data:	's=' + servers,
			
			success: function(data) {
				
				var result = jQuery.parseJSON(data);
				
				$.each(result.servs, function() {
					
					var cont = $('#server-list div[data-server=\''+this.id+'\']');
					var divStatus = $('div.status', cont);
					
					divStatus.addClass(this.status).html(this.status_html);
					
					if ( this.players !== undefined ) {
						
						var divPlayers = $('.info ul', cont);
						divPlayers.append('<li><i class="fa fa-male"></i> <b>'+this.players + '</b> players</li>');
						
					}
					
				});
				
			}
			
		});
		
	}
	
	// Delete confirmation in dash.
	$('a.del').click(function(){
		
		// Set var for the div which we're working with.
		var thisconf = $(this).closest('.post').find('.delconf');
		
		// Slide up any confirmations already down.
		$('.delconf').not(thisconf).slideUp(500);
		
		// Slide toggle the confirmation area.
		$(thisconf).slideToggle(500);
		
		event.preventDefault();
		
	});
	
	// Delete confirmation in dash.
	$('a.reject').click(function(){
		
		// Set var for the div which we're working with.
		var thisconf = $(this).closest('.post').find('.rejconf');
		
		// Slide up any confirmations already down.
		$('.rejconf').not(thisconf).slideUp(500);
		
		// Slide toggle the confirmation area.
		$(thisconf).slideToggle(500);
		
		event.preventDefault();
		
	});
	
});

$(document).ready(function () {
    $('.input input').bind('blur', function () {
        $(this).parent().find('.form_helper').slideUp(300);
    }).bind('focus', function () {
        $(this).parent().find('.form_helper').slideDown(300);
    });
});