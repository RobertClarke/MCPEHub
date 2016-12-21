<?php

/**
  
  * Pagination Class
  *
  * Functions are used to build and display pagination across
  * various parts of the website.
  *
  * build();	Builds pagination, outputs offset.
  * display();	Echoes HTML version of pagination links.
  
**/

class Pagination {
	
	private $html = '';
	
	function __construct($url) {
		$this->url = $url;
	}
	
	// Builds pagination, outputs offset.
	public function build($total_posts, $per_page, $current_page='') {
		
		$total_pages = ceil($total_posts / $per_page);
		
		$page = ( !empty($current_page) && is_numeric($current_page) ) ? (int)$current_page : 1;
		
		if ( $page > $total_pages ) $page = $total_pages;
		if ( $page < 1 ) $page = 1;
		
		// Set page number through URL class.
		if ( $page != 1 ) $this->url->add('page', $page);
		
		$offset = ($page - 1) * $per_page;
		
		$range = 2;
		
		if ( $total_pages > 1 ) {
			
			// Back link.
			if ( $page>1 ) $this->html .= '<a href="'.$this->url->show('page='.($page-1)).'" class="bttn"><i class="fa fa-angle-double-left solo"></i></a>';
			
			for ( $i = ($page - $range); $i < ($page + $range + 1); $i++ ) {
				
				if ( ($i > 0) && ($i <= $total_pages) ) {
					
					if ( $i == $page ) $this->html .= '<a href="'.$this->url->show('page='.$i).'" class="bttn active">'.$i.'</a>';
					else $this->html .= '<a href="'.$this->url->show('page='.$i).'" class="bttn">'.$i.'</a>';
					
				}
				
			}
			
			// Forward link.
			if ($page != $total_pages) $this->html .= '<a href="'.$this->url->show('page='.($page+1)).'" class="bttn"><i class="fa fa-angle-double-right solo"></i></a>';
			
		}
		
		return $offset;
		
	}
	
	// Echoes HTML version of pagination links.
	public function html($container_class='') {
		if ( !empty($this->html) ) echo '<div class="pagination bttn-group '.$container_class.'"><div class="pages">'.$this->html.'</div></div>';
	}
	
}

?>