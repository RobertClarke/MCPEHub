/**
 * --------------------------------------------------------------------
 * jQuery customfileinput plugin
 * Author: Scott Jehl, scott@filamentgroup.com
 * Copyright (c) 2009 Filament Group
 * licensed under MIT (filamentgroup.com/examples/mit-license.txt)
 * --------------------------------------------------------------------
 */
(function( $ ){
	$.fn.customFileInput = function(){
		//apply events and styles for file input element
		var fileInput = $(this)
			.addClass('customfile-input') //add class for CSS
			.mouseover(function(){ upload.addClass('customfile-hover'); })
			.mouseout(function(){ upload.removeClass('customfile-hover'); })
			.focus(function(){
				upload.addClass('customfile-focus');
				fileInput.data('val', fileInput.val());
			})
			.blur(function(){
				upload.removeClass('customfile-focus');
				$(this).trigger('checkChange');
			 })
			 .bind('disable',function(){
				fileInput.attr('disabled',true);
				upload.addClass('customfile-disabled');
			})
			.bind('enable',function(){
				fileInput.removeAttr('disabled');
				upload.removeClass('customfile-disabled');
			})
			.bind('checkChange', function(){
				if(fileInput.val() && fileInput.val() !== fileInput.data('val')){
					fileInput.trigger('change');
				}
			})
			.bind('change',function(){
				
				//reset classes
				upload.removeClass('customfile-bad customfile-good');
				upload.find('.customfile-msg').hide(0);
				
				//if file input exists
				if ( $(this).val() ) {
				
					//get file name
					var fileName = $(this).val().split(/\\/).pop();
					//get file extension
					var fileExt = fileName.split('.').pop().toLowerCase();
					//update the feedback
					uploadFeedback
						.text(fileName) //set feedback text to filename
						//.removeClass(uploadFeedback.data('fileExt') || '') //remove any existing file extension class
						//.addClass(fileExt) //add file extension class
						//.data('fileExt', fileExt) //store file extension for class removal on next change
						.addClass('customfile-feedback-populated'); //add class to show populated state
					//change text of button
					uploadButton.text('Change');
				
					//file not image
					if ( fileExt !== 'jpg' && fileExt !== 'jpeg' && fileExt !== 'png' && fileExt !== 'gif' ) {
						//alert('error');
						//$(uploadFeedback).parent().find('.warn-file').slideUp(300);
				
						upload.addClass('customfile-bad');
				
						upload.find('.customfile-msg').text('Invalid file format! Allowed: .jpeg, .jpg, .png, .gif');
				
						//var msg = '<span class="customfile-msg error">Invalid file format (must be image).</span>';
				
					}
					//file is image
					else {
						upload.addClass('customfile-good');
				
						//var msg = '<span class="customfile-msg">All good!</span>';
						upload.find('.customfile-msg').text('Ready for upload.');
					}
				
					upload.find('.customfile-msg').css('display','block');
				
				}
				//no file input or user cancelled
				else {
					uploadFeedback
						.text('Click here to add an image')
						.removeClass('customfile-feedback-populated');
				}
				
			})
			.click(function(){ //for IE and Opera, make sure change fires after choosing a file, using an async callback
				fileInput.data('val', fileInput.val());
				setTimeout(function(){
					fileInput.trigger('checkChange');
				},100);
			});
			
			//add 'last' class if needed
			//var last = '';
			//if ( $(fileInput).hasClass('last') ) { last = ' last'; }
			
			
		//create custom control container
		var upload = $('<div class="customfile"></div>');
		
		//create remove button
		$('<a href="#" id="removeUpload" class="remove"><i class="fa fa-times"></i></a>').appendTo(upload);
		
		//create custom control button
		var uploadButton = $('<span class="customfile-button" aria-hidden="true">Browse</span>').appendTo(upload);
		//create custom control feedback
		var uploadFeedback = $('<span class="customfile-feedback" aria-hidden="true">Click here to add an image</span>').appendTo(upload);
		
		//create message container
		$('<span class="customfile-msg"></span>').appendTo(upload);
		
		//match disabled state
		if(fileInput.is('[disabled]')){
			fileInput.trigger('disable');
		}


		//on mousemove, keep file input under the cursor to steal click
		upload
			/*.mousemove(function(e){
				fileInput.css({
					'left': e.pageX - upload.offset().left - fileInput.outerWidth() + 20, //position right side 20px right of cursor X)
					'top': e.pageY - upload.offset().top - $(window).scrollTop() - 3
				});
			})*/
			.insertAfter(fileInput); //insert after the input

		fileInput.appendTo(upload);

		//return jQuery
		return $(this);
	};
})( jQuery );
