<div class="profileContent">
	<h1>{_Share and interact}</h1>
	<form name="social_form">
		<p>
			<label for="newsletter">{_Subscribe to a newsletter}</label>
			<select id="newsletter">
			<!-- BEGIN newsletter.option -->
				<option value="{$news.value}" {$news.selected}>{$news.text}</option>
			<!-- END newsletter.option -->
			</select>
		</p>
		<p>
			<label for="twitter">{_Share on twitter}</label>
			<input type="checkbox" id="twitter" {$checked.twitter}>
		</p>
		<p>
			<label for="facebook">{_Share on facebook}</label>
			<input type="checkbox" id="facebook" {$checked.facebook}>
		</p>
		<p>
			<label for="statusnet">{_Share on statusnet}</label>
			<input type="checkbox" id="statusnet" {$checked.statusnet}>
		</p>
		<p>
			<label for="statusnet_account">{_StatusNet account}</label>
			<input type="text" id="statusnet_account" value="{$statusnet.account}"><br/>
			<span class="description">{_(eg. http://identi.ca/myaccount or http://statusnet.example.com)}</span>
		</p>
		<p>
			<input type="submit" id="apply" class="button" value="{_Update}">
		</p>
	</form>
</div>
<script type="text/javascript" src="{$planet.url}/user/tpl/js/social.js" ></script>
