<div id="content" class="pages">

	<!-- BEGIN signup.flash -->
	<div class="post box">
		<div id="flashmsg">
			{$flashmsg}
		</div>
	</div>
	<!-- END signup.flash -->
	<div class="post box">
		<h3>{_Your Bilboplanet Account}</h3>
		<div>{_Discover the background of all what you can do with Bilboplanet. Read, share, rate, add your own blog}</div>
	</div>
	<div class="post box">
		<h1 class="post-title">{_Signup}</h1>
		<form method="post" id="commentform">
			<p>
				<label for="name" class="authorinpout">{_Your login} <span style="color:red">*</span></label><br>
				<input type="text" class="short" id="name" tabindex="1" size="40" name="user_id" value="{$form.user_id}" />
			</p>
			<p>
				<label for="fullname">{_Your full name (Will be displayed)} <span style="color:red">*</span></label><br>
				<input type="text" class="short" id="fullname" tabindex="2" size="100" name="fullname" value="{$form.fullname}" />
			</p>
			<p>
				<label for="email">{_Email} <span style="color:red">*</span></label><br>
				<input type="text" id="email" name="email" class="short" tabindex="3" size="100"  value="{$form.email}" />
			</p>
			<p>
				<label for="pass">{_Password} <span style="color:red">*</span></label><br>
				<input type="password" id="pass" name="pass" class="short" tabindex="3" size="100"  value="" />
			</p>
			<p>{_Captcha} <span style="color:red">*</span><br />
			{$captcha_html}</p><tr>
			<p>
				<input type="submit" value="{_Send}" tabindex="5" id="submit" class="short" name="submit">
			</p>
		</form>
	</div>
</div>
