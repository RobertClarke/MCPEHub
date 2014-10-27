// @codekit-prepend "./src/flexslider.js";

$(window).load(function() {
	$('.slider').flexslider({
		controlNav: false,
		slideshowSpeed: 3500,
		animationSpeed: 500,
		prevText: "<i class='fa fa-chevron-left'></i>",
		nextText: "<i class='fa fa-chevron-right'></i>",
	});
});

// Syncing slider to post thumbnails.
$(window).load(function() {
  
	$('#slideshow #carousel').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		itemWidth: 120,
		asNavFor: '#slideshow #slider'
	});
	 
	$('#slideshow #slider').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		sync: "#slideshow #carousel",
		animationSpeed: 800,
		prevText: "<i class='fa fa-chevron-left'></i>",
		nextText: "<i class='fa fa-chevron-right'></i>"
	});
	
});