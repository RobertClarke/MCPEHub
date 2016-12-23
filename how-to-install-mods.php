<?php

require_once('core.php');

$pg = [
	'title_main'	=> 'Mods',
	'title_sub'		=> 'How To Install Minecraft PE',
	'seo_desc'		=> 'A step-by-step guide explaining how to download and install Minecraft PE mods on iOS and Android devices.',
	'seo_keywords'	=> 'minecraft pe mods, minecraft pe mod install, minecraft pe guide, minecraft pe, mcpe'
];

show_header('How To Install Minecraft PE Mods', FALSE, $pg);

?>

<div id="p-title">
    <h1>How To Install Minecraft PE Mods</h1>
    <div class="tabs">
        <a href="/mods" class="bttn"><i class="fa fa-puzzle-piece"></i> Browse Mods</a>
    </div>
</div>

<div id="how-to" class="tleft">
	
    <h2><i class="fa fa-apple fa-fw"></i> iOS Install</h2>
    
    <p><i>Jailbreak will always be required, please do not bug the modders to make mods for non-jailbroken devices.</i></p><br>
    <p>- If the mod is a .deb (which is most likely a .dylib or .plist), please use <b>Method #1</b>.<br>- If the mod is a ModScript, please use <b>Method #2</b>.</p>
    
    <br><p><b>Method #1 (.deb):</b></p>
    <ol class="spaced">
        <li>If the mod link is a Cydia repo, please enter that repo into Cydia. Then look for the mod in that repo and install.</li>
        <li>If the mod link is a download link to a .deb file, you will need iFile to install this mod.</li>
        <li>First download the file on your device. Open up iFile and go to the directory where you downloaded the file.</li>
        <li>Tap on the deb and tap “Installer” then wait for the installation to finish and press done.</li>
        <li>Re-spring your device (or just reboot if you don’t know how) and you're finished!</li>
    </ol>
    
    <br><p><b>Method #2 (.dylib or .plist):</b></p><br>
    <center><iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/AaZCwZU1EEo?rel=0" frameborder="0" allowfullscreen></iframe></center>
    
    <div class="spacer"></div>
    
    <h2 class="tcenter"><i class="fa fa-android fa-fw"></i> Android Install</h2>
    <center><iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/CuSQ9xAt7Ts?rel=0" frameborder="0" allowfullscreen></iframe></center>
    
</div>

<?php show_footer(); ?>