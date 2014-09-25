// @codekit-prepend "./src/tipr.js";

$(function() {
	
	// Show tooltips only on desktop machines.
	if(!('ontouchstart' in window)) {
		
		$('.tip-b').tipr({
			'speed': 150,
			'mode': 'bottom'
		});
		
		$('.tip').tipr({
			'speed': 150,
			'mode': 'top'
		});
		
	}
	
});