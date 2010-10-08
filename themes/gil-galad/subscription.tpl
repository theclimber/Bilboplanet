<!-- BEGIN subscription.flash -->
<div id="flashmsg">
	{$flashmsg}
</div>
<!-- END subscription.flash -->

<div id="subscription_content">{$subscription_content}</div>
<div id="main_subscription_content">
	<h2>{_Test your feeds}</h2>

	<p>
		{_Before to subscribe, do not hesitate to test your RSS/Atom feeds to be sure they'll be well interpretated by the planet aggregator engine which is using the <a href='http://simplepie.org/' title='Simple Pie' rel='noffolow'>Simple Pie</a> library (distributed under the LGPL licecne). Check also if your feeds are perfectly valid on <a href='http://feedvalidator.org/check.cgi' target='_blank'>Feedvalidator</a> and correct them if needed. Otherwise you could have some problems using them.}<br/>
		{_You can test the simplepie engine on the following page :}<br/>
		<a href="http://simplepie.org/demo/" title="test" rel="nofollow">http://simplepie.org/demo/</a>
	</p>

	<h2>{_Subscribe / Unsubscribe}</h2>
	<p>{_To add or remove your website from the planet, just fill this form in :}</p>

	<form method="post">
	<table border="0" width="600">
		<tr>
			<td>{_Name or nickname}</td>
			<td><input type="text" name="user_id" value="{$form.user_id}" /></td>
		</tr>
		<tr>
			<td>{_Fullname}</td>
			<td><input type="text" name="fullname" value="{$form.fullname}" /></td>
		</tr>
		<tr>
			<td>{_Contact email}</td>
			<td><input type="text" name="email" value="{$form.email}" /></td>
		</tr>
		<tr>
			<td>{_Website URL}</td>
			<td><input type="text" name="url" value="{$form.url}" /></td>
		</tr>
		<tr>
			<td>{_Feed URL (this can be a tag or category specific feed feed too)}</td>
			<td><input type="text" name="feed" value="{$form.feed}" /></td>
		</tr>
		<tr>
		<td colspan="2"><br/><input type="radio" name="choice" value="abonnement" checked /> {_Subscribe}</td>
		</tr>
		<tr>
		<td colspan="2"><input type="radio" name="choice" value="desabonnement" /> {_Unsubscribe}</td>
		</tr>
		<tr>
			<td>{_Please fill in the captcha}</td>
			<td >{$captcha_html}</td>
		</tr>
		<tr>
			<td colspan="2"><input type="checkbox" name="ok" value="" />{_I have read and accept the charter}</td>
		</tr>
		<tr>
			<td  colspan="2" align="center"><br/>
			<input type="reset" value="{_Reset}" onclick="this.form.reset()">&nbsp;&nbsp;
			<input type="submit" value="{_Send}" name="submit"></td>
		</tr>
	</table>
	</form>
</div>

<div class="post_small">
	<h2>{_Contact us}</h2>

	<p>
		{_If you need to contact the administration team for any reason (change of your feed URL, suggestion ...), you can do it by <a href='contact.php'>clicking here</a>.}
	</p>
</div>
