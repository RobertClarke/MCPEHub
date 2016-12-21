<?php

require_once('core.php');

$pg = [
	'title_main'	=> 'Skins',
	'title_sub'		=> 'How To Install Minecraft PE Skins',
	'seo_desc'		=> 'A step-by-step guide explaining how to install Minecraft PE skins on iOS and Android devices.',
	'seo_keywords'	=> 'minecraft pe skins, minecraft pe skins install, minecraft pe guide, minecraft pe, mcpe'
];

show_header('How To Use Minecraft PE Skins', FALSE, $pg);

?>

<div id="p-title">
    <h1>How To Install Minecraft PE Skins</h1>
    <div class="tabs">
        <a href="/skins" class="bttn"><i class="fa fa-paint-brush"></i> Browse Skins</a>
    </div>
</div>

<div id="how-to" class="tleft">

    <h2><i class="fa fa-apple fa-fw"></i>iOS &amp; Android</h2>
    <ol class="spaced">
        <li>First, you'll want to find a cool skin you like from <a href="/skins" target="_blank">our skins page</a>, and download/save it onto your device. </li>
        <li>Next, open up your game, and tap on the hanger icon under your character on the home screen. </li>
        <li>In the "Default" box, tap the "Custom" skin all the way to the right, and in the box to the right, tap the <strong>Choose New Skin</strong> button. </li>
        <li>Navigate to your recently downloaded skin file and upload it. </li>
        <li>Below the skin preview, tap <strong>Confirm Skin</strong>. </li>
        <li>Head over to your worlds list, load one, and have fun!</li>
    </ol>

</div>

<?php show_footer(); ?>
