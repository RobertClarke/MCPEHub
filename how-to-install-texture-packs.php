<?php

require_once('core.php');

$pg = [
	'title_main'	=> 'Texture Packs',
	'title_sub'		=> 'How To Install Minecraft PE',
	'seo_desc'		=> 'A step-by-step guide explaining how to download and install Minecraft PE texture packs on iOS and Android devices.',
	'seo_keywords'	=> 'minecraft pe texture packs, minecraft pe texture install, minecraft pe guide, minecraft pe, mcpe'
];

show_header('How To Install Minecraft PE Texture Packs', FALSE, $pg);

?>

<div id="p-title">
    <h1>How To Install Minecraft PE Texture Packs</h1>
    <div class="tabs">
        <a href="/textures" class="bttn"><i class="fa fa-paint-brush"></i> Browse Textures</a>
    </div>
</div>

<div id="how-to" class="tleft">
	
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
        <li>Find and open the folder you just saved.</li>
		<li>Inside of the folder, create a new one titled "resource_packs" (this title is case sensitive). </li>
		<li>Open your new (resource_packs) folder, and drop in your extracted texture pack folder you downloaded earlier from <a href="/textures" target="_blank">our texture packs page</a>. </li>
		<li>Head back over to iTunes and delete the original <strong>games</strong> folder from the Minecraft: Pocket Edition app. </li>
		<li>Click the <strong>Add...</strong> button, navigate to the <strong>games</strong> you saved and made changes to earlier, and add this in. </li>
		<li>Close iTunes and head back over to your mobile device. </li>
		<li>Delete MC:PE from your mutlitasking page if you have not done so yet. </li>
		<li>Open up your game, load a world, and have fun!</li>
    </ol>
    
    <div class="spacer"></div>
    
    <h2><i class="fa fa-apple fa-fw"></i> iOS Jailbreak Installation</h2>
    <ol>
        <li>
            Software you will need:<br />
            - iFile (found in Cydia)<br />
            - Safari Download Manager or Any Downloader<br />
        </li>
        <li>First, download a texture pack file from our <a href="/textures" target="_blank">Texture Packs</a> list with the downloader, and choose to <strong>Open with iFile</strong> (or whichever downloader you have chosen to use for this process.</li>
        <li>Once in there, choose <strong>Extract All Files/Folders</strong>.</li>
        <li>Hold down in the folder and choose <strong>Select All</strong>; then go to the bottom right, and choose <strong>Copy/Link</strong>.</li>
        <li>Click the home button in the app and from there choose the <strong>Applications</strong> folder and find the one labeled "Minecraft PE".</li>
        <li>From there, go to <strong>minecraftpe.app</strong> and choose to paste all of the files. Then delete your downloader (and Minecraft: Pocket Edition if necessary) from the multitasking menu.</li>
		<li>Open up your game, load a world, and have fun!</li>
    </ol>
    
    <div class="spacer"></div>
    
    <h2><i class="fa fa-android fa-fw"></i> Android Installation</h2>
    <ol class="spaced">
        <li>
            Software you will need:<br />
            - Block Launcher<br />
            - Any File Manager or Astro File Manager<br />
        </li>
        <li>Download a texture pack file from our <a href="/textures" target="_blank">Texture Packs</a> list.</li>
        <li>Go to <strong>Download</strong> in the file manager, and choose to extract files here.</li>
        <li>Now open up Block Launcher and go to <strong>Settings</strong> from the tool wrench.</li>
        <li>Choose to <strong>Use Texture Pack</strong>, and go to the <strong>Downloads</strong> and get the folder and click on the downloaded texture pack.</li>
        <li>Open up your game, load a world, and have fun!</li>
    </ol>

</div>

<?php show_footer(); ?>
