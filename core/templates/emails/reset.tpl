{extends file="../email.tpl"}
{assign var='link' value=$link|default:'http://mcpehub.com'}

{block "body"}
<p style="margin-bottom: 15px; margin-top: 0;">We recieved a request to reset the password for the account associated with this email.</p>
<a style="display: inline-block; color: #ffffff; text-decoration: none; font-size: 14px; background: #3F51B5; padding: 10px 18px; margin-bottom: 18px; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;" href="{$link}" target="_blank">Reset My Password</a>
<p style="margin-bottom: 12px; margin-top: 0;">Please note that this password reset link expires in 24 hours for security reasons.</p>
<p style="margin-bottom: 12px; margin-top: 0;">If you didn't request this change, please ignore this email. No changes will be made to your account.</p>
<p style="margin-bottom: 12px; margin-top: 0;">Best regards,<br> MCPE Hub Team</p>
{/block}

{block "bottom"}
<p style="margin-bottom: 0; margin-top: 0;">If you have issues opening the link above, use this link: <a style="color: #666666; text-decoration: underline;" href="{$link}" target="_blank">{$link}</a></p>
{/block}