// @codekit-prepend "./src/tipr.js";

$(function() {
	
	// Show tooltips only on desktop machines.
	if(!('ontouchstart' in window)) {
		
		$('#header .sub-nav > a').tipr({
			'speed': 150,
			'mode': 'bottom'
		});
		
		$('.tip').tipr({
			'speed': 150,
			'mode': 'top'
		});
		
		$('.tip-bottom').tipr({
			'speed': 150,
			'mode': 'bottom'
		});
		
	}
	
});