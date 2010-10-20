<!-- BEGIN contact.flash -->
<div id="flashmsg">
	{$flashmsg}
</div>
<!-- END contact.flash -->

<div id="contact_form">
	<h2>{_Contact us}</h2>

	<p>{_You can contact the administration team with the form below:}</p>
	<br/>
	<form method="post">
		{_Name / Nickname :}<br>
		<input class="contact" size="30" maxlength="30" type="text" name="name" value="{$form.name}" /><br>
		{_Email :}<br>
		<input class="contact" size="30" maxlength="30" type="text" name="email" value="{$form.email}" /><br>
		{_Subject :}<br>
		<input class="contact" size="73" maxlength="96" type="text" name="subject" value="{$form.subject}" /><br>
		{_Content :'}<br>
		<textarea id="styled" class="contact" type="text" name="content">{$form.content}</textarea><br>
		{$captcha_html}<br>
		<input type="reset" value="{_Reset}" onclick="this.form.reset()">
		&nbsp;&nbsp;
		<input type="submit" value="{_Send}" name="submit">
	</form>
</div>
