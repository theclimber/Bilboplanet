<div class="profileContent">
	<h1>{_Configure your profile}</h1>
	<form id="profile_form">
		<p>
			<label for="user_fullname">{_User id}</label>
			<input type="text" id="user_fullname" name="user_id" value="{$user.user_id}" disabled=true>
		</p>
		<p>
			<label for="user_fullname">{_Fullname}</label>
			<input type="text" id="user_fullname" name="efullname" value="{$user.user_fullname}">
		</p>
		<p>
			<label for="user_email">{_E-mail}</label>
			<input type="e-mail" id="user_email" name="eemail" value="{$user.user_email}">
		</p>
		<p>
			<label for="password">{_Password}</label>
			<input type="password" name="password" id="password"><br/>
			<span class="description">{_You can leave the password field blank if you don't want to change it}</span>
		</p>
		<p>
			<label for="password2">{_Confirm password}</label>
			<input type="password" name=password2 id="password2">
		</p>
		<p>
			<input type="submit" id="apply" class="button" value="{_Update}">
		</p>
	</form>
	<p>&nbsp;</p>
	<div class="user-feed-list dashbox">
		<h2>{_Your feeds}</h2>
		<table>
		<!-- BEGIN userfeed.item -->
			<tr class="user-feed {$feed.status}" id="{$feed.id}">
			<td class="element">
				<span class="feed-title"><a href="{$feed.url}">{$feed.url}</a>

				</span><br/>
				<span class="tag-line">
				<!-- BEGIN userfeed.tags -->
					<span class="tag" onclick="javascript:rm_feed_tag('{$feed_id}', '{$tag}')">{$tag}</span>
				<!-- END userfeed.tags -->
				</span>
			</td>
			<td class="action">
				<span class="action">
				<!-- BEGIN userfeed.action -->
				<a id="action-{$feed.id}" href="javascript:allow_comments('{$feed.id}')">
					<img title="{_Allow comments on this feed}" src="tpl/images/nocomment.png">
				</a>
				<!-- ELSE userfeed.action -->
				<a id="action-{$feed.id}" href="javascript:disallow_comments('{$feed.id}')">
					<img title="{_Disallow comments on this feed}" src="tpl/images/comment.png">
				</a>
				<!-- END userfeed.action -->
				</span>
				<span class="action">
					<a href="javascript:add_feed_tags('{$feed.id}')"><img title="{_Add tag}" src="tpl/images/add_tag.png"></a>
				</span>
			</td>
			</tr>
		<!-- ELSE userfeed.item -->
			<tr><td colspan=2>{_No feed found}</td></tr>
		<!-- END userfeed.item -->
		</table>
	</div>
</div>
<div id="tag-feed-form" style="display:none">
<form>
	<label class="required" for="tags">{_Tags}</label>
	<input type="text" id="tags" name="tags" value=""><br/>
	<span class="description">{_Comma separated tags (ex: linux,web,event)}</span>
	<div class="button">
		<input type="submit" name="apply" class="add_tags" value="{_Apply}" />
	</div>
</form>
</div>

<script type="text/javascript" src="{$planet.url}/user/tpl/js/jquery.boxy.js" ></script>
<script type="text/javascript" src="{$planet.url}/user/tpl/js/profile.js" ></script>
