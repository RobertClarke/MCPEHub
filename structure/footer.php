<?php if ( $footer_page_id == 'boxed' ) { ?></div><?php } else { ?>

        </div>
<?php
global $no_sidebar;
if ( !$no_sidebar ) include_once('sidebar.php');
?>
    <div class="clearfix"></div></div>
</div>

<?php
global $page_current;



if ( $page_current != 'map' && $page_current != 'seed' && $page_current != 'texture' && $page_current != 'mod' && $page_current != 'server' ) { ?>
<div id="header-advrt">
    <div class="wrapper">
        <div class="advrt header">
            <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="9724883879"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>
    </div>
</div>
<?php } ?>

<div id="footer">
    <div class="wrapper">
        <p>Copyright &copy; <span class="logo-text"><a href="/">MCPE Hub</a></span> <?php echo date('Y'); ?>. Creations copyright of the creators.</p>
        <p>Part of the <a href="http://cubemotion.com" target="_blank">CubeMotion</a> network.</p>
    </div>
</div>

<?php } ?>


<div style="position:fixed;bottom:0;right:0;z-index:99999;opacity:0.7;font-weight:bold;color:#333;background:#ccc;">

<?php

global $db, $user;

$test_db = $db->query("SHOW SESSION STATUS LIKE 'Questions'")->fetch();

?>

<div style="background:red;padding:5px 10px;color:#fff;float:left;">SQL Queries: <?php echo $test_db[0]['Value']; ?></div>

<div style="background:blue;padding:5px 10px;color:#fff;float:left;">Auth: <?php echo ($user->logged_in()) ? 'TRUE' : 'FALSE'; ?></div>

<div style="background:green;padding:5px 10px;color:#fff;float:left;">Exec Time: <?php echo substr( microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 0, 7 ) ?>s</div>

</div>

<!-- jQuery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>




<script>

$(function() {
	
	function update_servers() {
			
		$('.post.server').each(function() {
			
			var statusDiv = $('div.status', this);
			var statusText = $('span.status', this);
			var playersDiv = $('span.players', this);
			
			statusDiv.html('<i class="fa fa-history"></i> Loading').removeClass('online offline');
			
			$.ajax({
				
				type:		'POST',
				url:		'core/server-status.php',
				data:		'server_id=' + $(this).data('server'),
				
				success:	function(data) {
					
					var result = jQuery.parseJSON(data);
					
					statusDiv.fadeOut(400, function() {
						
						if ( result.players !== undefined ) playersDiv.html(result.players);
						
						statusDiv.html(result.badge).addClass(result.class);
						statusText.html(result.text).addClass(result.class);
						
						statusDiv.fadeIn(400);
						
					});
					
				}
				
			});
			
			
			
		});
		
	}
	
	update_servers();
	
	/*$( 'a.update' ).click(function(){
		update_servers();
		event.preventDefault();
	});*/
	
});

</script>



<script>
$(function() {
	$('.search-toggle').click(function(){
		$('.post-search').slideToggle(300);
		$('input#search').focus();
		event.preventDefault();
	});
});

</script>

<script>

$(function() {
	
	$('form.form.submission').submit(function() {
		
		$(this)
			.find('button#submit')
			.attr('disabled','disabled')
			.addClass('disabled')
			.html('<i class="fa fa-spinner"></i> Please Wait');
		
		$(this).submit();
		
		event.preventDefault();
		
	});
	
});

</script>

<script>

$(function() {
	
	$('a.notifications').click(function() {
		
		$('div#notifications').fadeToggle(600);
		
		event.preventDefault();
		
	});
	
});


</script>





<link rel="stylesheet" type="text/css" href="/assets/css/chosen.css" media="all" />
<script type="text/javascript" src="/assets/js/chosen.min.js"></script>



<script type="text/javascript" src="/assets/js/jquery.fileinput.min.js"></script>

<script>


/*$(function(){
		$('.file-upload').customFileInput();	
});*/

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



</script>


  
  
  <script type="text/javascript">
    
    $('.chosen-select').chosen({
	    
	    width: '340px',
	    disable_search: true
	    
    });
    
  </script>




<!-- Main.js -->
<script type="text/javascript" src="/assets/js/main-min.js"></script>

<!-- Tipr.js (if needed) -->
<script type="text/javascript" src="/assets/js/tipr.min.js"></script>

<!-- TinyMCE (if needed) -->

<!-- Flex Slider -->
<script type="text/javascript" src="/assets/scripts/jquery.flexslider.min.js"></script>

<script>

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

</script>



<script>
$(function() {
	
	// If on desktop.
	if(!('ontouchstart' in window)) {
		
		//$('[title!=""]').qtip();
		
	$('.tip-header').tipr({
        'speed': 150,
        'mode': 'bottom'
    });
    
    
    
    $('.tip').tipr({
        'speed': 150,
        'mode': 'top'
    });
		
	}
	
	
});

</script>



<script type="text/javascript" charset="utf-8">
  $(window).load(function() {
    $('.flexslider').flexslider({
    
    controlNav: false,
    slideshowSpeed: 3500,
    animationSpeed: 500,
    prevText: "<i class='fa fa-chevron-left'></i>",
	nextText: "<i class='fa fa-chevron-right'></i>",
    
    });
  });
</script>

<script>
$('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') 
        || location.hostname == this.hostname) {

        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
           if (target.length) {
             $('html,body').animate({
                 scrollTop: target.offset().top
            }, 1000);
            return false;
        }
    }
});
</script>




<script type="text/javascript" src="/assets/js/tinymce/tinymce.min.js"></script>
    
<script type="text/javascript">
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

</script>


<script>
$(document).ready(function () {
    $('.input input').bind('blur', function () {
        $(this).parent().find('.form_helper').slideUp(300);
    }).bind('focus', function () {
        $(this).parent().find('.form_helper').slideDown(300);
    });
});
</script>


<!-- Cloudflare -->

<!-- Google Analytics -->

<!-- GoSquared Analytics -->

</body>
</html><?php ob_end_flush(); ?>