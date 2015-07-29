<article id="post" class="{$type}">
	<header>
		<h1>{$title}</h1>
		<div class="stats">
			<span><i class="icon-eye"></i> {$views}</span>
			<a href="#" class="bttn like"><i class="icon-like"></i> Like</a>
		</div>
	</header>
	<section class="slider">
		<ul class="bxslider">
{foreach from=$images item=img}
			<li><img src="/uploads/700x300/maps/{$img}" alt="" width="700" height="300"></li>
{/foreach}
		</ul>
	</section>
	<section class="info">
		<div class="author">
			<div class="avatar"><a href="/user/{$author}"><img src="/avatar/64x64/{$avatar}" alt="" width="64" height="64"></a></div>
			<p><a href="/user/{$author}">{$author}</a></p>
			<a href="#" class="bttn mini"><i class="icon-follow"></i> Follow</a>
		</div>
		<div class="get">
			<a href="#" class="bttn xl"><i class="icon-plus"></i> Save For Later</a>
			<a href="#" class="bttn xl gold dl" target="_blank"><i class="icon-download"></i> Download Now <span>{$downloads} Times</span></a>
		</div>
	</section>
	<section class="actions">
		<a href="#"><div class="bttn mini with-counter"><i class="icon-like"></i> Like</div><div class="bttn-counter mini">{$likes}</div></a>
		<a href="#"><div class="bttn mini with-counter"><i class="icon-comments"></i> Comment</div><div class="bttn-counter mini">{$comments}</div></a>
		<a href="#" class="bttn mini"><i class="icon-heart"></i> Favorite</a>
		<div class="share">
			<span><a class="twitter-share-button" data-text="Check out this #MinecraftPE {$type}! {$title}" data-url="{$url}" data-via="MCPEHubNetwork" data-related="MCPEHubNetwork"></a></span>
			<span class="nospace"><div class="fb-share-button" data-href="{$url}" data-layout="button_count"></div></span>
			<span><div class="g-plus" data-action="share" data-annotation="bubble" data-href="{$url}"></div></span>
		</div>
	</section>
	<section class="description">
		<h2>Map Description</h2>
		{$description}
	</section>
	<section class="comments">
		<B>COMMENTS GO HERE</B>
	</section>
	<section class="report">
		<B>REPORT BUTTON HERE</B>
	</section>
</article>