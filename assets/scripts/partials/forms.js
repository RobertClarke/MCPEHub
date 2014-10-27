// Changing to "Please Wait..." button on submit.
$('form').submit(function() {
	$(this)
		.find('button[type=submit]')
		.attr('disabled','disabled')
		.addClass('disabled')
		.html('Please Wait...');
	
	$(this).submit();
	event.preventDefault();
});

// Setting active state on associated input labels.
$('input[type=text], input[type=password]').focus(function() {
	var label = $('label[for="'+$(this).attr('id')+'"]');
	if ( label.length != 0 ) $(label).addClass('active');
}).blur(function() {
	var label = $('label[for="'+$(this).attr('id')+'"]');
	if ( label.length != 0 ) $(label).removeClass('active');
});