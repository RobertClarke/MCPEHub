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

	<h2><i class="fa fa-apple fa-fw"></i>iOS Installation</h2>
    <ol class="spaced">
        <li>
			Hardware/software you will need:<br />
			- Mac or Windows computer<br />
			- iTunes for Mac or Windows<br />
		</li>
        <li>First, connect your device to your computer. </li>
        <li>Open iTunes, and click on your device. </li>
        <li>From the sidebar in iTunes, click <strong>Apps</strong>.</li>
        <li>Scroll down on this page until you find the Minecraft: Pocket Edition app listed under "File Sharing". Click on the game. </li>
        <li>To the right, click the <strong>games</strong> folder, then click <strong>Save to...</strong>, and choose your desired location to save this folder to. </li>
        <li>Find and open the folder you just saved, then navigate to <strong>com.mojang</strong>, then to <strong>minecraftWorlds</strong>. </li>
		<li>Inside of this worlds folder, drop in the new world, which is the one you downloaded earlier. This folder should consist of files such as level.dat, a db folder, level.dat_old, and finally levelname.txt. </li>
		<li>After dropping in this new world folder, head back over to iTunes and delete the original <strong>games</strong> folder from the Minecraft: Pocket Edition app. </li>
		<li>Head back over to iTunes and delete the original <strong>games</strong> folder from the Minecraft: Pocket Edition app. </li>
		<li>Click the <strong>Add...</strong> button, navigate to the <strong>games</strong> you saved and made changes to earlier, and add this in. </li>
		<li>Close iTunes and head back over to your mobile device. </li>
		<li>Delete MC:PE from your mutlitasking page if you have not done so yet. </li>
		<li>Open up your game, load your new world, and have fun!</li>
    </ol>

	<h2><i class="fa fa-apple fa-fw"></i>iOS Jailbreak Installation</h2>
    <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/3buov7pZ9SM?rel=0" frameborder="0" allowfullscreen></iframe>

    <h2><i class="fa fa-android fa-fw"></i>Android Installation</h2>
    <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/gm2B2GbagmA?rel=0" frameborder="0" allowfullscreen></iframe>

</div>

<?php show_footer(); ?>
