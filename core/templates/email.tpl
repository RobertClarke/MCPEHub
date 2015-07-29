{assign var='title' value=$title|default:''}
{assign var='username' value=$username|default:''}
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>{$title}</title>
</head>
<body style="font-size: 13px; font-family: Helvetica, Arial, sans-serif; line-height: 1.9em; color: #333333; background: #ffffff; margin: 0; padding: 10px;">
	<dic style="padding: 15px;">
		{if !empty($username)}<p style="margin-bottom: 15px; margin-top: 0;">Howdy <b>{$username}</b>!</p>{/if}
		{block "body"}{/block}
		<div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd;">
			<p style="margin: 0;">Follow us on Twitter: <a style="color: #1976D2; text-decoration: none;" href="http://twitter.com/MCPEHubNetwork" target="_blank">@MCPEHubNetwork</a> for MCPE news, updates, giveaways and more!</p>
		</div>
		<div style="color: #999999; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd; font-size: 13px;">
			{block "bottom"}{/block}
		</div>
	</div>
</body>
</html>