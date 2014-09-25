// @codekit-prepend "./src/fileinput.js";

 $(function() {

    $('.file-upload').each(function() {
        $(this).customFileInput();  
    }); 
 });
 
 $(function() {
        
        
        var scntDiv = $('#uploadInputs');
        var i = $('#uploadInputs .customfile').size();
        
        $('#addUpload').on('click', function() {
            
            //alert(i);
            
            if ( i > 5 ) return false;
            
            if ( i == 4 ) $('span.addUpload').hide(0);
            
            //add 'last' class if needed
            //var last = '';
            //if ( i%2 !== 0 ) last = ' last';
            
            
            //$('<p><label for="p_scnts"><input type="text" id="p_scnt" size="20" name="p_scnt_' + i +'" value="" placeholder="Input Value" /></label> <a href="#" id="remScnt">(x)</a></p>').appendTo(scntDiv);
            
            //var cont = $('<p></p>');
            //var newInput = $('<input type="file" name="file" id="file" class="file-upload" />').appendTo(cont);
            //$(cont).appendTo(scntDiv);
            
            var newInput = $('<input type="file" name="images[]" id="image" class="file-upload" />').appendTo(scntDiv);
            
            $(newInput).customFileInput();
            
            i++;
            return false;
            
            
            
            
        });
        
        $( scntDiv ).on('click', '#removeUpload', function() {
        //$('#remScnt').on('click', function() { 
            
            //alert(i);
            
            
            if ( i < 6 ) $('span.addUpload').slideDown(400);
                
                
				
                
                if ( i != 1 ) {
	                
	                $(this).parents('.customfile').slideUp(400, function(){ $(this).remove(); } );
	                
	                i--;
                } 
                else {
                	
                	$(this).parents('.customfile').remove();
                	
                	//var cont = $('<p></p>');
                	//var newInput = $('<input type="file" name="file" id="file" class="file-upload" />').appendTo(cont);
            		//$(cont).appendTo(scntDiv);
            		
            		var newInput = $('<input type="file" name="images[]" id="image" class="file-upload" />').appendTo(scntDiv);
            		
            		
            		$(newInput).customFileInput();
                	
                }          
                
                return false;
        });
});