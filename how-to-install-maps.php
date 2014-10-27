<?php

require_once('core.php');

$pg = [
	'title_main'	=> 'Maps',
	'title_sub'		=> 'How To Install Minecraft PE',
	'seo_desc'		=> 'A step-by-step guide explaining how to download and install Minecraft PE maps on iOS and Android devices.',
	'seo_keywords'	=> 'minecraft pe maps, minecraft pe map install, minecraft pe guide, minecraft pe, mcpe'
];

show_header('How To Install Minecraft PE Maps', FALSE, $pg);

?>

<div id="p-title">
    <h1>How To Install Minecraft PE Maps</h1>
    <div class="tabs">
        <a href="/maps" class="bttn"><i class="fa fa-map-marker"></i> Browse Maps</a>
    </div>
</div>

<div id="how-to">
	
	<h2><i class="fa fa-apple fa-fw"></i> iOS Non-Jailbreak Install</h2>
	<iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/IKRa4TeEwOM?rel=0" frameborder="0" allowfullscreen></iframe>
	
	<h2><i class="fa fa-apple fa-fw"></i> iOS Jailbreak Install</h2>
	<iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/3buov7pZ9SM?rel=0" frameborder="0" allowfullscreen></iframe>
	
	<h2><i class="fa fa-android fa-fw"></i> Android Install</h2>
	<iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/gm2B2GbagmA?rel=0" frameborder="0" allowfullscreen></iframe>

</div>

<?php show_footer(); ?>