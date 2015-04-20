<div class="row">
	<div class="col-xs-12">
		<div id="flash-log" style="display:none;">
			<div id="flash-msg"><!-- spanner --></div>
		</div>
	</div>
</div>
<div class="profileContent">
	<div class="row">
		<div class="col-xs-12">
			<h1>{_Share and interact}</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<form  name="social_form" id="social_form">
						<div class="form-group" style="display:none">
							<label for="newsletter">{_Subscribe to a newsletter}</label>
							<select id="newsletter">
							<!-- BEGIN newsletter.option -->
								<option value="{$news.value}" {$news.selected}>{$news.text}</option>
							<!-- END newsletter.option -->
							</select>
						</div>
						<div class="form-group">
							<label for="twitter">{_Share on Twitter}</label>
							<input class="field" type="checkbox" id="twitter" {$checked.twitter}>
						</div>
						<div class="form-group">
							<label for="google">{_Share on Google+}</label>
							<input class="field" type="checkbox" id="google" {$checked.google}>
						</div>
						<div class="form-group"p>
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
						</div>
						<div class="form-group">
							<label for="statusnet">{_Share on StatusNet}</label>
							<input class="field" type="checkbox" id="statusnet" {$checked.statusnet}>
						</div>
						<div class="form-group" id="statusnet-input" style="display:none;">
							<label for="statusnet_account">{_StatusNet account}</label>
							<input class="field" type="text" id="statusnet-account" value="{$statusnet_account}"><br/>
							<span class="description">{_(eg. http://identi.ca/myaccount or http://statusnet.example.com)}</span>
						</div>
						<div class="form-group">
							<label for="reddit">{_Share on Reddit}</label>
							<input class="field" type="checkbox" id="reddit" {$checked.reddit}>
						</div>

							<button type="submit" id="apply" class="btn btn-primary" value="{_Update}">{_Update}</button>

					</form>
				</div>
			</div>		
		</div>
	</div>
</div>
