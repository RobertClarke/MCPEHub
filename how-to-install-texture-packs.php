<?php

require_once( 'core.php' );
show_header( 'How To Install Minecraft PE Texture Packs', FALSE, '', '', 'A step-by-step guide explaining how to download and install Minecraft PE texture packs on iOS and Android devices.', 'minecraft pe texture packs, minecraft pe texture install, minecraft pe guide, minecraft pe, mcpe' );

$error->add( 'CONTRIBUTE', 'Have a YouTube video about texture pack installs? Tweet us: <a href="http://twitter.com/MCPEHubNetwork" target="_blank">@MCPEHubNetwork</a> and we might feature it here!', 'info', 'youtube-play' );
$error->set( 'CONTRIBUTE' );

?>

<div id="page-title">
    <h1>How To Install Minecraft PE Texture Packs</h1>
    <ul class="tabs">
        <a href="/textures" class="bttn"><i class="fa fa-magic"></i> Browse Textures</a>
    </ul>
</div>

<?php $error->display(); ?>

<h2 class="inline"><i class="fa fa-apple fa-fw"></i> iOS Non-Jailbreak Install</h2>

<ol class="spaced">
    <li>
        Software you will need:<br />
        - <a href="http://www.i-funbox.com/" target="_blank">iFunBox</a> or <a href="http://www.macroplant.com/iexplorer/" target="_blank">iExplorer</a><br />
        - <a href="http://support.apple.com/downloads/#iphone" target="_blank">iPhone Configuration Utility</a><br />
        - <a href="http://www.rarlab.com/download.htm" target="_blank">WinRAR</a> or any ZIP viewer
    </li>
    <li>Go to iTunes and get a version of Minecraft PE that you are using currently. Once you get it, drag it from the iTunes apps, to your desktop and it should be a .IPA file.</li>
    <li>Now download a texture pack from our <a href="/textures" target="_blank">Texture Packs</a> list and extract it into a folder from a ZIP.</li>
    <li>After that, drag and drop the .IPA into WinRAR. Once there, open "Payload" and then go to "minecraftpe.app".</li>
    <li>Now copy and paste all the texture files you want into the "minecraftpe.app" and copy and replace all.</li>
    <li>Close the program. If prompted, save all changes you've made.</li>
    <li>Now get the iPhoneConfiguration Utility and install it. Drag the .IPA into the "Applications" top area in the running app.</li>
    <li>An item called "minecraftpe" will appear with the update it will work with. Go into ‘Devices’ and go to the applications tab and uninstall "minecraftpe" and reinstall it.</li>
    <li>Done! Open Minecraft PE and enjoy your texture pack!</li>
</ol>

<br />

<strong>IMPORTANT: Make sure you have your device plugged in, and be sure to back up your maps, mods, etc. since it will return your Minecraft PE app to new since it was deleted and re-downloaded onto the device.</strong>

<div class="spacer"></div>

<h2 class="inline"><i class="fa fa-apple fa-fw"></i> iOS Jailbreak Install</h2>

<ol class="spaced">
    <li>
        Software you will need:<br />
        - iFile (Found on Cydia)<br />
        - Safari Download Manager or Any Downloader<br />
    </li>
    <li>First download a texture file from our <a href="/textures" target="_blank">Texture Packs</a> list with the downloader and choose to "Open w/ iFile".</li>
    <li>Once in there choose extract all files/folders.</li>
    <li>Hold down in the folder and choose "Select All". Then go to the bottom right, and choose Copy/Link.</li>
    <li>Click the home button in the app and from there choose Applications and find the one labeled Minecraft PE. The from there go to "minecraftpe.app" and choose to paste all the files. Then delete the app from the multitasking menu.</li>
    <li>Done! Open Minecraft PE and enjoy your texture pack!</li>
</ol>

<div class="spacer"></div>

<h2 class="inline"><i class="fa fa-android fa-fw"></i> Android Unrooted Install</h2>

<ol class="spaced">
    <li>
        Software you will need:<br />
        - Block Launcher<br />
        - Any File Manager or Astro File Manager<br />
    </li>
    <li>Download a Minecraft PE texture pack from our <a href="/textures" target="_blank">Texture Packs</a> list.</li>
    <li>Go to "Download" in the file manager and choose to extract files here.</li>
    <li>Now go into Block Launcher and go to the Settings from the tool wrench.</li>
    <li>Choose to "Use Texture Pack" and go to the Downloads and get the folder and click on the downloaded texture pack.</li>
    <li>Done! Open Minecraft PE and enjoy your texture pack!</li>
</ol>

<?php show_footer(); ?>