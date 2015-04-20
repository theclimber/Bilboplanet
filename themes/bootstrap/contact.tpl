<div id="content" class="pages">
<div class="row">
	<div class="col-xs-12">
		<!-- BEGIN contact.flash -->
		<div class="post box">
			<div id="flashmsg">
				{$flashmsg}
			</div>
		</div>
		<!-- END contact.flash -->
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="post box">
			<h1>{_Contact us}</h1>
			<p>{_You can contact the administration team through the contact form below.}</p>
			<form method="post" id="commentform">
				<div class="form-group">
					<label for="author" class="authorinpout control-label">{_Name} <span style="color:red">*</span></label><br>
					<input type="text" class="short form-control" id="author" class="contact" tabindex="1" size="40" name="name" value="{$form.name}" />
				</div>
				<div class="form-group">
					<label for="email" class="control-label">{_Email} <span style="color:red">*</span></label><br>
					<input id="email" name="email" class="short form-control" tabindex="2" size="40" type="text" name="email" value="{$form.email}" />
				</div>
				<div class="form-group">
					<label for="subject" class="control-label">{_Subject} <span style="color:red">*</span></label><br>
					<input id="url" class="short form-control" tabindex="3" size="40" type="text" name="subject" value="{$form.subject}" />
				</div>
				<div class="form-group">
					<label for="comment" class="control-label">{_Content} <span style="color:red">*</span></label><br />
					<textarea tabindex="4" rows="10" cols="61" id="comment" name="content" class="form-control">{$form.content}</textarea>
				</div>
				<div class="form-group">{$captcha_html}</div>

				<button type="submit" value="{_Send}" tabindex="5" id="submit" class="short btn btn-primary" name="submit">{_Send}</button>

			</form>
		</div>
	</div>
</div>
