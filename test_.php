<?php require_once( 'core.php' ); ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script type="text/javascript">

$(function() {
	
	/*$( '.server' ).each(function(){
		
		
		$(this).html('<strong>Server ' + $(this).data('server') + '</strong> <div class="response hide"></div>' );
		
		
	});*/
	
	function update_servers() {
		
		$( '.server' ).each(function(){
			
			//alert( $(this).data('server') );
			
			
			
			$('.response', $this).fadeOut(400);
			
			
			
			var obj = null;
			
			var $this = $(this);
			
			$.ajax({
	            type:'POST',
	            url: 'test2.php',
	            data: 'id=' + $this.data('server'),
	            success:function(data){
	                
	                obj = jQuery.parseJSON( data );
	                //alert( obj.response );
	                
	                
	                $('.response', $this).html( obj.response ).fadeIn(400).addClass( obj.response );
	                
	            }
	        });
	        
	        //alert(obj.response);
	        
	        //$(this).html( obj.response );
	        
	        
	        
	        
			
			
			
			
		});
		
	}
	
	update_servers();
	
	$( 'a.update' ).click(function(){
		
		update_servers();
		event.preventDefault();
		
	});
	
});

</script>

<style>
* {
	font: 13px Arial, sans-serif;
}
.loader {
	width: 16px;
	height: 16px;
	display: block;
	background: url('ajax-loader.gif') no-repeat 0 0;
}
.hide {
	display: none;
}
#servers {
	width: 500px;
	margin: 25px auto;
	border: 1px solid #eee;
	border-bottom: 0;
}
.server {
	border-bottom: 1px solid #eee;
	padding: 15px;
}
.response {
	float: right;
	background: #f7f7f7;
	color: #000;
	display: inline-block;
	top: -5px;
	position: relative;
	padding: 5px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}
.online {
	background: green;
	color: #fff;
}
.offline {
	background: red;
	color: #fff;
}
</style>

<div id="servers">
    
<?php

$query = $db->from( 'content_servers' )->where( array( 'active' => '1' ) )->fetch();
$num = $db->affected_rows;

foreach( $query as $server ) {
	
?>
    <div class="server" data-server="<?php echo $server['id']; ?>">
        <!--<div class="loader"></div>-->
        <a href="server.php?server=<?php echo $server['id']; ?>"><?php echo $server['title']; ?></a> - <?php echo $server['ip'].':'.$server['port']; ?>
        <div class="response hide"></div>
    </div>
<?php } ?>
    
</div>

<center><a href="#" class="update">Update Servers</a></center>