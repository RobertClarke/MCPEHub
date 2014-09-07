<?php

require_once( 'core.php' );
show_header( 'How To Install Minecraft PE Mods', FALSE, '', '', 'A step-by-step guide explaining how to download and install Minecraft PE mods on iOS and Android devices.', 'minecraft pe mods, minecraft pe mod install, minecraft pe guide, minecraft pe, mcpe' );

$error->add( 'CONTRIBUTE', 'Have a YouTube video about mod installs? Tweet us: <a href="http://twitter.com/MCPEHubNetwork" target="_blank">@MCPEHubNetwork</a> and we might feature it here!', 'info', 'youtube-play' );
$error->set( 'CONTRIBUTE' );

?>

<div id="page-title">
    <h1>How To Install Minecraft PE Mods</h1>
    <ul class="tabs">
        <a href="/mods" class="bttn"><i class="fa fa-codepen"></i> Browse Mods</a>
    </ul>
</div>

<?php $error->display(); ?>

<h2 class="inline"><i class="fa fa-apple fa-fw"></i> iOS Install</h2>

<p>NOTE: Jailbreak will always be required, please do not bug the modders to make mods for non-jailbroken devices.</p>

<p>
    - If the mod is a .deb (which is most likely a .dylib or .plist), please use <strong>Method #1</strong>.<br />
    - If the mod is a ModScript, please use <strong>Method #2</strong>.
</p>

<br />
<p><strong>Method #1 (.deb):</strong></p>

<ol class="spaced">
    <li>If the mod link is a Cydia repo, please enter that repo into Cydia. Then look for the mod in that repo and install.</li>
    <li>If the mod link is a download link to a .deb file, you will need iFile to install this mod.</li>
    <li>First download the file on your device. Open up iFile and go to the directory where you downloaded the file.</li>
    <li>Tap on the deb and tap “Installer” then wait for the installation to finish and press done.</li>
    <li>Re-spring your device (or just reboot if you don’t know how) and you're finished!</li>
</ol>

<br />
<p><strong>Method #2 (.dylib or .plist):</strong></p>

<center><iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/AaZCwZU1EEo?rel=0" frameborder="0" allowfullscreen></iframe></center>

<div class="spacer"></div>

<h2 class="inline"><i class="fa fa-android fa-fw"></i> Android Install</h2>

<center><iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/CuSQ9xAt7Ts?rel=0" frameborder="0" allowfullscreen></iframe></center>

<?php show_footer(); ?>