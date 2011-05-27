<div class="writeContent">
	<h1>{_Write a post}</h1>
	<form name="write_form">
		<p>
			<label for="post_title">{_Post title}</label>
			<input type="text" id="post_title">
		</p>
		<p>
			<label for="shared_link">{_Link to share}</label>
			<input type="text" id="shared_link" value="http://"><br/>
			<span class="description">{_(Optional)}</span>
		</p>
		<p>
			<label for="post_content" class="textarea">{_Post content}</label>
			<textarea id="post_content"></textarea><br/>
			<span class="description">{_Here you'll find some help concerning the syntax :} <a href="#">Wiki syntax</a></span>
		</p>
		<p>
			<label for="post_tags">{_Tags}</label>
			<input type="text" id="post_tags"><br/>
			<span class="description">{_Comma separated tags (ex: linux,web,event)}</span>
		</p>
		<p>
			<label for="comments">{_Allow comments}</label>
			<input type="checkbox" id="comments" checked>
		</p>
		<p>
			<input type="submit" id="apply" class="button" value="{_Publish}">
		</p>
	</form>
</div>
