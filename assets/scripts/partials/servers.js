if ( $('#posts.servers').length) {
	
	var servers = [];
	
	$('#posts.servers').children('.post').each(function() {
		servers.push($(this).data('server'));
	});
	
	$.ajax({
		
		type:	'GET',
		cache:	false,
		url:	'/ajax/ping',
		data:	's=' + servers,
		
		success: function(data) {
			
			var result = jQuery.parseJSON(data);
			
			$.each(result.servs, function() {
				
				var cont = $('#posts.servers div[data-server=\''+this.id+'\']');
				var divStatus = $('span.status', cont);
				var divPlayers = $('span.players', cont);
				
				divStatus.addClass(this.status).html(this.status_html);
				
				if ( this.players !== undefined ) {
					
					//var divPlayers = $('.info ul', cont);
					//divPlayers.append('<li><i class="fa fa-male"></i> <b>'+this.players + '</b> players</li>');
					divPlayers.html(this.players_html);
					
				}
				
			});
			
		}
		
	});
	
}