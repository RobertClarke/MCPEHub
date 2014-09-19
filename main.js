$(function() {
	$('.search-tog').click(function(){
		$('#search-head').fadeToggle(400);
		$('input#search').focus();
		event.preventDefault();
	});
});

tinymce.init({
    selector: "textarea.visual",
    /*width: "560px",*/
    height: "130px",
    theme: "modern",
	skin: "light",
    plugins: ["link smileys"],
    toolbar: "bold underline italic strikethrough | smileys | alignleft aligncenter alignright alignjustify | bullist numlist | link unlink | undo redo",
    statusbar: false,
    menubar: false,
 });
 
 tinymce.init({
    selector: "textarea.visual-comment",
    width: "700px",
    height: "110px",
    theme: "modern",
	skin: "light",
    plugins: ["smileys"],
    toolbar: "bold underline italic strikethrough | smileys | undo redo",
    statusbar: false,
    menubar: false,
 });
 
 function comment_reply( $user ) {
	    tinymce.activeEditor.setContent( '@' + $user + '&nbsp;' );
    }