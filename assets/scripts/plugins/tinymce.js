tinymce.init({
	selector: "textarea.visual",
	height: "150px",
	theme: "modern",
	skin: "light",
	plugins: ["link smileys paste"],
	toolbar: "bold underline italic strikethrough | smileys | alignleft aligncenter alignright | bullist numlist | link unlink | undo redo",
	statusbar: false,
	menubar: false,
	paste_as_text: true,
	object_resizing : false
});

tinymce.init({
	selector: "textarea.visual-comment",
	height: "120px",
	width: "600px",
	theme: "modern",
	skin: "light",
	plugins: ["link smileys paste"],
	toolbar: "bold underline italic strikethrough | smileys | bullist numlist | undo redo",
	statusbar: false,
	menubar: false,
	paste_as_text: true,
	object_resizing : false
});