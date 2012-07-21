<div id="content" class="pages">
	<!-- BEGIN contact.flash -->
	<div class="post box">
		<div id="flashmsg">
			{$flashmsg}
		</div>
	</div>
		<!-- END contact.flash -->

		<div class="post box">
			<h1>Proposer un article</h1>
			<p>Si vous voulez simplement nous proposer un article afin qu'il soit lu par les lecteurs du Planet-Libre, merci de remplir le formulaire ci-dessous.</p>
			<p>Les <span style="color:red">*</span> indique l'obligation de remplir un champ.</p>

			<form method="post" id="commentform">
				<p>
					<label for="author" class="authorinpout">Pr&eacute;nom Nom : <span style="color:red">*</span></label><br>
					<input type="text" class="short" id="author" class="contact" tabindex="1" size="70" name="name" value="{$form.name}" />
				</p>
				<p>
					<label for="email">Email : <span style="color:red">*</span></label><br>
					<input id="email" name="email" class="short" tabindex="2" size="70" type="text" name="email" value="{$form.email}" />
				</p>
				<p>
					<label for="subject">Source : <span style="color:red">*</span></label><br>
					<input id="url" class="short" tabindex="3" size="70" type="text" name="subject" value="{$form.subject}" />
				</p>
				<p>
					<label for="comment">Contenu de l'article : <span style="color:red">*</span></label><br />
					<textarea tabindex="5" style="width:630px;height:280px;" id="comment" name="content">{$form.content}</textarea>
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
