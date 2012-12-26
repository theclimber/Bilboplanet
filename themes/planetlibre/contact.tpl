<div id="content" class="pages">
	<!-- BEGIN contact.flash -->
	<div class="post box">
		<div id="flashmsg">
			{$flashmsg}
		</div>
	</div>
	<!-- END contact.flash -->

		<div class="post box">
			<h1>{_Contact us}</h1>
			<p>{_You can contact the administration team through the contact form below.}</p>

			<form method="post" id="commentform">
				<p>
					<label for="author" class="authorinpout">{_Name} <span style="color:red">*</span></label><br>
					<input type="text" class="short" id="author" class="contact" tabindex="1" size="40" name="name" value="{$form.name}" />
				</p>
				<p>
					<label for="email">{_Email} <span style="color:red">*</span></label><br>
					<input id="email" name="email" class="short" tabindex="2" size="40" type="text" name="email" value="{$form.email}" />
				</p>
				<p>
					<label for="subject">{_Subject} <span style="color:red">*</span></label><br>
					<input id="url" class="short" tabindex="3" size="40" type="text" name="subject" value="{$form.subject}" />
				</p>
				<p>
					<label for="comment">{_Content} <span style="color:red">*</span></label><br />
					<textarea tabindex="4" rows="10" cols="61" id="comment" name="content">{$form.content}</textarea>
				</p>
				<p>{$captcha_html}</p>
				<p>	
					<input type="submit" value="{_Send}" tabindex="5" id="submit" class="short" name="submit">
				</p>
				<br>
			</form>
		</div>
	</div>
</div>
