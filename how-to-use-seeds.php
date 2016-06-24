<?php

require_once('core.php');

$pg = [
	'title_main'	=> 'Seeds',
	'title_sub'		=> 'How To Use Minecraft PE Seeds',
	'seo_desc'		=> 'A step-by-step guide explaining how to use Minecraft PE seeds on iOS and Android devices.',
	'seo_keywords'	=> 'minecraft pe seeds, minecraft pe seeds use, minecraft pe guide, minecraft pe, mcpe'
];

show_header('How To Use Minecraft PE Seeds', FALSE, $pg);

?>

<div id="p-title">
    <h1>How To Use Minecraft PE Seeds</h1>
    <div class="tabs">
        <a href="/seeds" class="bttn"><i class="fa fa-paint-brush"></i> Browse Seeds</a>
    </div>
</div>

<div id="how-to" class="tleft">

    <h2><i class="fa fa-apple fa-fw"></i>iOS &amp; Android</h2>
    <ol class="spaced">
        <li>First, you'll want to find a cool seed you like from <a href="/seeds" target="_blank">our seeds page</a>, and mark it down somewhere (or just remember it). </li>
        <li>Next, open up your game, and from the home screen, tap the <strong>Play</strong> button. </li>
        <li>Under the <strong>Worlds</strong> tab, tap the <strong>Create New World</strong> button. </li>
        <li> Feel free to set the name of your world to anything you'd like, and set the game mode as well. </li>
        <li>In the top right corner of your screen, tap the <strong>Advanced</strong> button. </li>
        <li>In the "Seed" field, input the seed you picked out earlier from <a href="/seeds" target="_blank">our seeds page</a>. </li>
        <li>Finally, tap the <strong>Create World!</strong> button to start generating your new world. Play, and have fun! </li>
    </ol>

</div>

<?php show_footer(); ?>
