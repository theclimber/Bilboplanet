<div class="tribesContent">
	<h1>{_Configure your Tribes}</h1>

<div class="button br3px" id="add-form"><a onclick="javascript:openAdd()">
	{_Create a tribe}</a>
</div>

	<!-- Add tribe form -->
	<form id="addtribe_form">
		<label class="required" for="tribe_name">{_Tribe name} :
			<input name="tribe_name" type="text" />
		</label>
		<span class="description">{_ex: climate change}</span><br />

		<label class="required" for="ordering">{_Ordering} :
			<input name="ordering" type="text" />
		</label>
		<span class="description">{_The ordering specifies the position of your tribe on the portal}</span><br />

		<div class="button br3px"><input type="reset" class="reset" name="reset" value="{_Reset}"></div>&nbsp;&nbsp;
		<div class="button br3px"><input type="submit" name="add_feed" class="valide" value="{_Add}" /></div>
		<div class="button br3px close-button"><a class="close" onclick="javascript:closeAdd()">{_Close}</a></div>
	</form>

</fieldset>


<fieldset><legend>{_Manage tribes}</legend>
	<div class="message">
		<p>{_Manage tribes}</p>
	</div>
	<div id="tribe-list">
	
		<!-- BEGIN tribes.box -->
		<div class="tribesbox tribe-'.$tribe_state.'" id="tribe-{$tribe.id}">
			<h3><a href="{$planet.url}/index.php?list=1&tribe_id={$tribe.id}">{$tribe.name}</a></h3>
			<p class="tribe-icon"><img class="tribe-icon" src="{$tribe.icon}" /></p>
			<p class="nickname">
				{_Tags :} <div class="tag-line">{$tribe.tags}</div><br/>
				{_No-tags :} <div class="notag-line">{$tribe.notags}</div><br/>
				{_Users :} <div class="user-line">{$tribe.users}</div><br/>
				{_Search :} {$tribe.search} <a href="">x</a><br/>
				{_Last post :} {$tribe.last_post}<br/>
				{_Post count :} {$tribe.count}<br/>
				{_Ordering :} {$tribe.ordering}
			</p>
			<ul class="actions">
				<li><a href="javascript:toggleTribeVisibility('{$tribe_id}')"><img src="meta/icons/toggle-visibility.png" title="{_Toggle tribe visibility}"/></a></li>
				<li><a href="javascript:edit(\''.$rs->tribe_id.'\', '.$num_page.', '.$nb_items.')"><img src="meta/icons/action-edit.png" title="'.T_('Edit tribe').'" /></a></li>
				<li><a href="javascript:removeTribe(\''.$rs->tribe_id.'\','.$num_page.','.$nb_items.')"><img src="meta/icons/cross.png" title="'.T_('Remove tribe').'" /></a></li>
				<li><a href="javascript:add_tags('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_tag.png" title="'.T_('Add tags to tribe').'"/></a></li>
				<li><a href="javascript:add_notags('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_notag.png" title="'.T_('Add unwanted tags to tribe').'"/></a></li>
				<li><a href="javascript:add_users('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_user.png" title="'.T_('Add users to tribe').'" /></a></li>
				<li><a href="javascript:add_search('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_search.png" title="'.T_('Add search to tribe').'" /></a></li>
				<li>'.$icon_action.'</li>
			</ul>
			<div class="feedlink"><a href="{$planet.url}/index.php?list=1&tribe_id={$tribe.id}">
					<img alt="RSS" src="{$planet.url}/themes/{$planet.theme}/images/rss_24.png" /></a></div>
		</div>';
		<!-- END tribes.box -->
	
	
	
	</div>
</fieldset>



<div id="icon-tribe-form" style="display:none">
	<form id="icon-tribe" enctype="multipart/form-data">
		<label class="required" for="icon">{_Add tribe icon} : <br />
		<input name="icon" size="30" type="file"> </label><br />
		<input name="ajax" value="tribe" type="hidden" />
		<input name="action" value="add_icon" type="hidden" />
		<input id="tribe-id" name="tribe_id" value="" type="hidden" />
		<input type="hidden" name="MAX_FILE_SIZE" value="2097152">
		<span class="description"><i>{_The image have to be 100px*100px or will be resized}</i></span><br /><br />

		<div class="button">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="{_Cancel}">
		</div>
		'<div class="button">
			<input type="submit" name="send" id="send-icon" class="add_icon" value="{_Send}" />
		</div>
	</form>
</div>

<div id="tribe-edit-form" style="display:none">
	<form>
		<input type="hidden" name="tribe_id" value="" />
		<label class="required" for="tribe_name">{_Tribe name} : <br />
			<input name="tribe_name" type="text" value="" />
		</label><br/>

		<label class="required" for="tribe_order">{_Tribe order} : <br />
			<input name="tribe_order" type="text" value="" />
		</label><br/>

		<div class="button">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="{_Cancel}">
		</div>
		<div class="button">
			<input type="submit" name="send" class="add_site" value="{_Update}" />
		</div>
	</form>
</div>

<div id="tag-tribe-form" style="display:none">
	<form>
		<label class="required" for="content">{_Add new tags} : <br />
			<input name="tags" type="text" value="" />
		</label><br/>
		<span class="description"><i>{_Comma separated tags (ex: linux,web,event)}</i></span><br /><br />

		<div class="button">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="{_Cancel}">
		</div>
		<div class="button">
			<input type="submit" name="send" class="add_site" value="{_Send}" />
		</div>
	</form>
</div>

<div id="search-tribe-form" style="display:none">
	<form>
		<label class="required" for="content">{_Add a search} : <br />
			<input name="search" type="text" value="" />
		</label><br/>
		<span class="description"><i>{_Write your search in the text field}</i></span><br /><br />

		<div class="button">
			<input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="{_Cancel}">
		</div>
		<div class="button">
			<input type="submit" name="send" class="add_site" value="{_Send}" />
		</div>
	</form>
</div>

<div id="user-tribe-form" style="display:none">
	<span id="user-tribe-form">

		<label for="user_id">{_Add new users} :
			<select id="user_combo" name="user_id">
				<option value="{$option.user_id}">{$option.user_name}</option>
			</select>
		<br />

		<form>
			<input name="users_selected" type="text" value="" />
			</label><br/>
			<span class="description"><i>{_Comma separated user id\'s (ex: john22,jack,flipper)}</i></span><br /><br />

			<div class="button">
				<input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="{_Cancel}">
			</div>
			'<div class="button">
				<input type="submit" name="send" class="add_site" value="{_Send}" />
			</div>
		</form>
	</span>
</div>



</div>
