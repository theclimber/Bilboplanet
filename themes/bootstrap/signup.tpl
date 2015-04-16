<div id="content" class="pages">
	<div class="row">
	  <div class="col-md-12">
			<!-- BEGIN signup.flash -->
			<div class="post box">
				<div id="flashmsg">
					{$flashmsg}
				</div>
			</div>
			<!-- END signup.flash -->
	  </div>
	</div>
	<div class="row">
	  <div class="col-md-12">
			<div class="post box well well-sm">
				<h3>{_Your Bilboplanet Account}</h3>
				<p>{_Discover the background of all what you can do with Bilboplanet. Read, share, rate, add your own blog}</p>
			</div>
			<div class="post box">
				<h2 class="post-title">{_Signup}</h2>
				<form method="post" id="commentform">
					<div class="form-group">
						<label for="name" class="authorinpout">{_Your login} <span style="color:red">*</span></label><br>
						<input type="text" class="short form-control" id="name" tabindex="1"  name="user_id" value="{$form.user_id}" />
					</div>
					<div class="form-group">
						<label for="fullname">{_Your full name (Will be displayed)} <span style="color:red">*</span></label><br>
						<input type="text" class="short form-control" id="fullname" tabindex="2"  name="fullname" value="{$form.fullname}" />
					</div>
					<div class="form-group">
						<label for="email">{_Email} <span style="color:red">*</span></label><br>
						<input type="text" id="email" name="email" class="short form-control" tabindex="3"   value="{$form.email}" />
					</div>
					<div class="form-group">
						<label for="pass">{_Password} <span style="color:red">*</span></label><br>
						<input type="password" id="pass" name="pass" class="short form-control" tabindex="3"  value="" />
					</div>
					<div class="form-group">
					<label>{_Captcha} <span style="color:red">*</span><br /></label>
					{$captcha_html}
					</div>
					<button  type="submit" value="{_Send}" tabindex="5" id="submit" class="btn btn-primary" name="submit">{_Send}</button>
				</form>
			</div>
		</div>
	</div>
</div>
