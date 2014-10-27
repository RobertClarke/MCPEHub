// @codekit-prepend "./src/dropdown.js";

// Hide Tipr in subnav when active dropdown.
$('#drop-notif, #drop-msgs').on('show', function(event, dropdownData) {
    $('#header #top .sub-nav').addClass('hideTips');
}).on('hide', function(event, dropdownData) {
    $('#header #top .sub-nav').removeClass('hideTips');
});