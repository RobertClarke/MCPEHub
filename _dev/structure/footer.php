<?php

global $pi;
$page_current = basename($_SERVER['PHP_SELF'], '.php');

if ( $pi['body_id'] == 'boxed' ) { // Boxed template.

?>
        </div>
    </div>
<?php } else { // End: boxed template. ?>
            </div>
<?php if ( !isset($pi['no_sidebar']) ) include_once(ABS . 'structure/sidebar.php'); ?>
        </div>
    </div>
    
    <div class="avrt-wide">
        <div class="wrapper">
            <div class="avrt">
                <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="9724883879"></ins>
                <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
            </div>
        </div>
    </div>
    
    <div id="footer">
        <div class="wrapper">
            <p>&copy; <a href="/"><span>MCPE Hub</span></a> 2014 - Creations copyright of the creators.</p>
            <p class="side">Part of the CubeMotion network.</p>
        </div>
    </div>
    
<?php } // End normal template. ?>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="/assets/js/main-min.js"></script>
    
</body>
</html><?php ob_end_flush(); ?>