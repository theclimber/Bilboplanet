<div id="flash-log" style="display:none;">
	<div id="flash-msg"><!-- spanner --></div>
</div>

<div class="profileContent">
	<h1>{_Share and interact}</h1>
	<form class="user" name="social_form" id="social_form">
		<p style="display:none">
			<label for="newsletter">{_Subscribe to a newsletter}</label>
			<select id="newsletter">
			<!-- BEGIN newsletter.option -->
				<option value="{$news.value}" {$news.selected}>{$news.text}</option>
			<!-- END newsletter.option -->
			</select>
		</p>
		<p>
			<label for="twitter">{_Share on Twitter}</label>
			<input class="field" type="checkbox" id="twitter" {$checked.twitter}>
		</p>
		<p>
			<label for="google">{_Share on Google+}</label>
			<input class="field" type="checkbox" id="google" {$checked.google}>
		</p>
		<p>
			<label for="shaarli">{_Share on Shaarli}</label>
			<input class="field" type="checkbox" id="shaarli" {$checked.shaarli}>
		<div id="shaarli-details">
			<p>
				<label for="type">{_Where is your Shaarli?}</label>
				<select name="shaarli-type" id="shaarli-type">
					<option value="local" {$checked.shaarli-type.local}>{_Local instance}</option>
					<option value="remote" {$checked.shaarli-type.remote}>{_Remote shaarli}</option>
				</select>
			</p>
			<p id="shaarli-remote-instance" style="display:none;">
				<label for="shaarli_instance">{_Shaarli instance}</label>
				<input class="field" type="text" id="shaarli-instance" value="{$shaarli_instance}"><br/>
				<span class="description">{_(eg. http://website.tld/shaarli}</span>
			</p>
			<p id="shaarli-local-instance" style="display:none;">
				{_NOTE : if you have no shaarli instance, you can create one on this planet.}<br/>
				<a href="{$planet.url}/shaarli">{_Go on Shaarli}</a>
			</p>
		</div>
		</p>
		<p>
			<label for="statusnet">{_Share on StatusNet}</label>
			<input class="field" type="checkbox" id="statusnet" {$checked.statusnet}>
		</p>
		<p id="statusnet-input" style="display:none;">
			<label for="statusnet_account">{_StatusNet account}</label>
			<input class="field" type="text" id="statusnet-account" value="{$statusnet_account}"><br/>
			<span class="description">{_(eg. http://identi.ca/myaccount or http://statusnet.example.com)}</span>
		</p>
		<p>
			<label for="reddit">{_Share on Reddit}</label>
			<input class="field" type="checkbox" id="reddit" {$checked.reddit}>
		</p>
		<p>
			<input type="submit" id="apply" class="button" value="{_Update}">
		</p>
	</form>
</div>
