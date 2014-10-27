$('#dashboard .post a.actn_del').click(function() {
	
	var d_cont = $(this).closest('.post');
	
	var d_post = d_cont.data('post');
	var d_type = d_cont.data('type');
	
	$('#actn_del.modal a.del').attr('href', '/delete?post='+d_post+'&type='+d_type);
	
});

$('#dashboard .post a.actn_link').click(function() {
	
	var p_cont = $(this).closest('.post');
	
	var p_slug = p_cont.data('slug');
	var p_type = p_cont.data('type');
	
	$('#actn_link.modal input#link_copy').attr('value', 'http://mcpehub.com/'+p_type+'/'+p_slug);
	
});