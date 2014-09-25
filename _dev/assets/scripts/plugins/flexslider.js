// @codekit-prepend "./src/flexslider.js";

$(window).load(function() {
  // The slider being synced must be initialized first
  
  $('#post-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 120,
    asNavFor: '#post-slider'
  });
   
  $('#post-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    sync: "#post-carousel",
    animationSpeed: 700,
    prevText: "<i class='fa fa-chevron-left'></i>",
	nextText: "<i class='fa fa-chevron-right'></i>"
  });
});

  $(window).load(function() {
    $('.flexslider').flexslider({
    
    controlNav: false,
    slideshowSpeed: 3500,
    animationSpeed: 500,
    prevText: "<i class='fa fa-chevron-left'></i>",
	nextText: "<i class='fa fa-chevron-right'></i>",
    
    });
  });
