{extends file="../email.tpl"}
{assign var='link' value=$link|default:'http://mcpehub.com'}

{block "body"}
<p style="margin-bottom: 15px; margin-top: 0;">Welcome to MCPE Hub, we can't wait to see what you have to contribute to our community! Before you start posting, you have to activate your account by clicking the link below.</p>
<a style="display: inline-block; color: #ffffff; text-decoration: none; font-size: 14px; background: #8BC34A; padding: 10px 18px; margin-bottom: 18px; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;" href="{$link}" target="_blank">Activate My Account</a>
<p style="margin-bottom: 12px; margin-top: 0;">Once you've activated your account, you'll be able to publish content, comment and participate in our growing community.</p>
<p style="margin-bottom: 12px; margin-top: 0;">Thanks for joining our community!</p>
<p style="margin-bottom: 12px; margin-top: 0;">Best regards,<br> MCPE Hub Team</p>
{/block}

{block "bottom"}
<p style="margin-bottom: 6px; margin-top: 0;">If you have issues opening the link above, use this link: <a style="color: #666666; text-decoration: underline;" href="{$link}" target="_blank">{$link}</a></p>
<p style="margin-bottom: 0; margin-top: 0;">If you didn't register this account, please ignore this email.</p>
{/block}