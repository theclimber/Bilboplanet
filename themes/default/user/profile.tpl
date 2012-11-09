<div id="flash-log" style="display:none;">
	<div id="flash-msg"><!-- spanner --></div>
</div>

<div class="profileContent">
	<h1>{_Configure your profile}</h1>
	<form class="user" id="profile_form">
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
			<label for="user_lang">{_Language}</label>
			<select id="user_lang" name="elang">
				<!-- BEGIN lang.select -->
				<option value="{$lang.code}" {$lang.selected}>{$lang.name}</option>
				<!-- END lang.select -->
			</select>
		</p>
		<p>
			<input type="submit" id="apply" class="button" value="{_Update}">
		</p>
	</form>
	<p>&nbsp;</p>
	<div class="user-feed-list dashbox">
		<h2>{_Your feeds}</h2>
		<a href="javascript:add_feed()"><img title="{_Add new feeds}" src="{$planet.url}/themes/{$planet.theme}/user/images/add.png" /></a>
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
				<!-- BEGIN userfeed.action.activate -->
				<span class="action">
				<!-- BEGIN userfeed.action -->
				<a id="action-{$feed.id}" href="javascript:allow_comments('{$feed.id}')">
					<img title="{_Allow comments on this feed}" src="{$planet.url}/themes/{$planet.theme}/user/images/nocomment.png">
				</a>
				<!-- ELSE userfeed.action -->
				<a id="action-{$feed.id}" href="javascript:disallow_comments('{$feed.id}')">
					<img title="{_Disallow comments on this feed}" src="{$planet.url}/themes/{$planet.theme}/user/images/comment.png">
				</a>
				<!-- END userfeed.action -->
				</span>
				<span class="action">
					<a href="javascript:add_feed_tags('{$feed.id}')"><img title="{_Add tag}" src="{$planet.url}/themes/{$planet.theme}/user/images/add_tag.png"></a>
				</span>
				<span class="action">
					<a href="javascript:rm_feed('{$feed.id}')"><img title="{_Remove feed}" src="{$planet.url}/themes/{$planet.theme}/user/images/action-remove.png"></a>
				</span>
				<!-- END userfeed.action.activate -->
			</td>
			</tr>
		<!-- ELSE userfeed.item -->
			<tr><td colspan=2>{_No feed found}</td></tr>
		<!-- END userfeed.item -->
		</table>
	</div>

	<p>&nbsp;</p>

	<!-- BEGIN pendingfeed -->
	<div class="user-feed-list dashbox">
		<h2>{_Your pending for validation feeds}</h2>
		{_Theses feeds need the validation of the site administrator. Please read the charter before submitting new feeds.}
		<br/><a href="{$planet.url}/charter.php">{_Go to the charter}</a>
		<table>
		<!-- BEGIN userpfeed.item -->
			<tr class="user-feed">
			<td class="element">
				<span class="feed-title"><a href="{$pfeed.url}">{$pfeed.url}</a></span>
			</td>
			<td class="action">
				<span class="action">
					<a href="javascript:rm_pending_feed('{$pfeed.url}')"><img title="{_Remove pending feed}" src="{$planet.url}/themes/{$planet.theme}/user/images/action-remove.png"></a>
				</span>
			</td>
			</tr>
		<!-- END userpfeed.item -->
		</table>
	</div>
	<!-- END pendingfeed -->
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
<div id="new-feed-form" style="display:none">
<form>
	<label class="required" for="existing_site">{_Existing site url}</label>
	<input type="checkbox" name="existing_site"/>
	<select name="site_id">
		<option value="">{_-- choose existing site}</option>
		<!-- BEGIN existing.site -->
		<option value="{$esite.id}">{$esite.url}</option>
		<!-- END existing.site -->
	</select><br/>
	<label class="required" for="site">{_New site url}</label>
	<input type="text" id="site" name="site" value=""><br/>
	<label class="required" for="feed">{_Feed url}</label>
	<input type="text" id="feed" name="feed" value=""><br/>
	<div class="button">
		<input type="submit" name="apply" class="add_feed" value="{_Add}" />
	</div>
</form>
</div>

