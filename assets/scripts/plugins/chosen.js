// @codekit-prepend "./src/chosen.js";

var chosen_selectors = {
	'.chosen'			: {disable_search_threshold: 10}
}

for (var selector in chosen_selectors) {
	$(selector).chosen(chosen_selectors[selector]);
}

// Custom redirect chosen.
$('.chosen.redirect').change( function() { window.location = $(this).val(); } );