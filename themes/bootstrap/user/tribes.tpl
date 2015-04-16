<div id="flash-log" style="display:none;">
	<div id="flash-msg"><!-- spanner --></div>
</div>

<div class="tribesContent">
	<h1>{_Configure your Tribes}</h1>

<div class="button br3px" id="add-form"><a onclick="javascript:openAdd()">
	{_Create a tribe}</a>
</div>


<fieldset id="addtribe-field" style="display:none">
	<!-- Add tribe form -->
	<form id="addtribe_form" class="user">
		<div class="close-button"><a class="close" onclick="javascript:closeAdd()"><img src="../themes/{$planet.theme}/user/images/close-button.png"/></a></div>

		<p>
			<label class="required" for="tribe_name">{_Tribe name}</label>
			<input class="field" name="tribe_name" type="text" /><br/>
			<span class="description">{_ex: climate change}</span>
		</p>

		<p>
			<label class="required" for="ordering">{_Ordering}</label>
			<input class="field" name="ordering" type="text" /><br/>
			<span class="description">{_The ordering specifies the position of your tribe on the portal}</span>
		</p>

		<p>
			<input type="reset" class="button" name="reset" value="{_Reset}">&nbsp;&nbsp;
			<input type="submit" name="add_feed" class="button" value="{_Add}" />
		</p>

	</form>
</fieldset>


<fieldset>
	<div id="tribe-list">

		<!-- BEGIN tribes.box -->
		<div class="tribesbox tribe-{$tribe.state}" id="tribe-{$tribe.id}">
			<h3><a href="{$planet.url}/index.php?list=1&tribe_id={$tribe.id}">{$tribe.name}</a></h3>
			<p class="tribe-icon"><img class="tribe-icon" src="{$tribe.icon}" /></p>
			<ul class="info-list">
				<li>{_Tags :} <span class="tag-line">
					<!-- BEGIN tribes.tag -->
					<span class="tag">{$tribe_tag} <a href="javascript:rm_tag('{$tribe_id}','{$tribe_tag}')">x</a></span>
					<!-- END tribes.tag -->
					</span></li>
				<li>{_No-tags :} <span class="notag-line">
					<!-- BEGIN tribes.notag -->
					<span class="tag">{$tribe_notag} <a href="javascript:rm_notag('{$tribe_id}','{$tribe_notag}')">x</a></span>
					<!-- END tribes.notag -->
					</span></li>
				<li>{_Users :} <span class="user-line">
					<!-- BEGIN tribes.user -->
					<span class="user">{$tribe_user} <a href="javascript:rm_user('{$tribe_id}','{$tribe_user}')">x</a></span>
					<!-- END tribes.user -->
					</span></li>
				<li>{_Search :} {$tribe.search} 
					<!-- BEGIN tribes.search -->
					(<a href="javascript:rm_search('{$tribe_id}')">{_clear}</a>)
					<!-- ELSE tribes.search -->
					({_Empty})
					<!-- END tribes.search -->
					</li>
				<li>{_Last post :} {$tribe.last_post}</li>
				<li>{_Post count :} {$tribe.count}</li>
				<li>{_Ordering :} {$tribe.ordering}</li>
			</ul>
			<ul class="actions">
				<li><a href="javascript:toggleTribeVisibility('{$tribe.id}')"><img src="../themes/{$planet.theme}/user/images/tribe-state-{$tribe.state}.png" title="{_Toggle tribe visibility}"/></a></li>
				<li><a href="javascript:edit('{$tribe.id}')"><img src="../admin/meta/icons/action-edit.png" title="{_Edit tribe}" /></a></li>
				<li><a href="javascript:removeTribe('{$tribe.id}')"><img src="../admin/meta/icons/cross.png" title="{_Remove tribe}" /></a></li>
				<li><a href="javascript:add_tags('{$tribe.id}','{$tribe.stripped_name}')"><img src="../admin/meta/icons/add_tag.png" title="{_Add tags to tribe}"/></a></li>
				<li><a href="javascript:add_notags('{$tribe.id}','{$tribe.stripped_name}')"><img src="../admin/meta/icons/add_notag.png" title="{_Add unwanted tags to tribe}"/></a></li>
				<li><a href="javascript:add_users('{$tribe.id}','{$tribe.stripped_name}')"><img src="../admin/meta/icons/add_user.png" title="{_Add users to tribe}" /></a></li>
				<li><a href="javascript:add_search('{$tribe.id}','{$tribe.stripped_name}')"><img src="../admin/meta/icons/add_search.png" title="{_Add search to tribe}" /></a></li>
				<li>
				<!-- BEGIN tribes.icon.action -->
					<a href="javascript:rm_icon('{$tribe_id}')">
						<img src="../admin/meta/icons/rm_icon.png" title="{_Remove icon from tribe}" /></a>
				<!-- ELSE tribes.icon.action -->
					<a href="javascript:add_icon('{$tribe.id}','{$tribe.stripped_name}')">
						<img src="../admin/meta/icons/add_icon.png" title="{_Add icon to tribe}" /></a>
				<!-- END tribes.icon.action -->
				</li>
			</ul>
			<div class="feedlink"><a href="{$planet.url}/index.php?list=1&tribe_id={$tribe.id}">
					<img alt="RSS" src="{$planet.url}/themes/{$planet.theme}/images/rss_24.png" /></a></div>
		</div>
		<!-- END tribes.box -->
	</div>
</fieldset>



<div id="icon-tribe-form" style="display:none">
	<form id="icon-tribe" enctype="multipart/form-data" class="boxy">
		<input name="ajax" value="tribes" type="hidden" />
		<input name="action" value="add_icon" type="hidden" />
		<input id="tribe-id" name="tribe_id" value="" type="hidden" />
		<input type="hidden" name="MAX_FILE_SIZE" value="2097152">

		<label class="required" for="icon">{_Add tribe icon}</label>
		<input name="icon" size="30" type="file"><br />
		<span class="description"><i>{_The image have to be 100px*100px or will be resized}</i></span><br /><br />

		<div class="bbutton">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($('form.boxform')).hide()" value="{_Cancel}">
		</div>
		<div class="bbutton">
			<input type="submit" name="send" id="send-icon" class="add_icon" value="{_Send}" />
		</div>
	</form>
</div>

<div id="tribe-edit-form" style="display:none">
	<form class="boxy">
		<input id="tribe_id" type="hidden" name="tribe_id" value="" />
		<label class="required" for="tribe_name">{_Tribe name}</label>
		<input id="tribe_name" name="tribe_name" type="text" value="" /><br/>

		<label class="required" for="tribe_order">{_Tribe order}</label>
		<input id="tribe_order" name="tribe_order" type="text" value="" /><br/>

		<div class="bbutton">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($('form.boxform')).hide()" value="{_Cancel}">
		</div>
		<div class="bbutton">
			<input type="submit" name="send" class="button add_site" value="{_Update}" />
		</div>
	</form>
</div>

<div id="tag-tribe-form" style="display:none">
	<form class="boxy">
		<label class="required" for="content">{_Add new tags}</label>
		<input name="tags" type="text" value="" /><br/>
		<span class="description"><i>{_Comma separated tags (ex: linux,web,event)}</i></span><br /><br />

		<div class="bbutton">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($('form.boxform')).hide()" value="{_Cancel}">
		</div>
		<div class="bbutton">
			<input type="submit" name="send" class="add_site" value="{_Send}" />
		</div>
	</form>
</div>

<div id="search-tribe-form" style="display:none">
	<form class="boxy">
		<label class="required" for="content">{_Add a search}</label>
		<input name="search" type="text" value="" /><br/>
		<span class="description"><i>{_Write your search in the text field}</i></span><br /><br />

		<div class="bbutton">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($('form.boxform')).hide()" value="{_Cancel}">
		</div>
		<div class="bbutton">
			<input type="submit" name="send" class="add_site" value="{_Send}" />
		</div>
	</form>
</div>

<div id="user-tribe-form" style="display:none">
	<span id="user-tribe-form">

		<label for="user_id">{_Add new users}</label>
			<select id="user_combo" name="user_id">
				<!-- BEGIN tribe.option.userlist -->
				<option value="{$option.user_id}">{$option.user_name}</option>
				<!-- END tribe.option.userlist -->
			</select>
		<br />

		<form class="boxy">
			<input id="users_selected" name="users_selected" type="text" value="" /><br/>
			<span class="description"><i>{_Comma separated user id's (ex: john22,jack,flipper)}</i></span><br /><br />

			<div class="bbutton">
				<input type="button" class="cancel" name="cancel" onClick="Boxy.get($('form.boxform')).hide()" value="{_Cancel}">
			</div>
			<div class="bbutton">
				<input type="submit" name="send" class="add_site" value="{_Send}" />
			</div>
		</form>
	</span>
</div>



</div>
