<!-- BEGIN login.popup -->
<div class="login-box popup">
	<span class="close-popup-button"><a href="" onclick="javascript:hide_login()">x</a></span>
	<h2>Login into {$planet.title}</h2>
	<form action="{$planet.url}/auth.php" method="post" id="login-form">
		<input type="hidden" name="came_from" value="{$came_from}" id="came_from"  />
		<div class="field">
			<label>{_Username or email}</label>
			<span class="input">
				<input type="text" size="2" name="user_id" maxlength="32" class="text" tabindex="1"  />
			</span>
		</div>
		<div class="field">
			<label>{_Password}</label>
			<span class="input">
				<input type="password" size="2" name="user_pwd" maxlength="255" class="text" tabindex="2" id=login_password />
				<a href="auth.php?recover=1" title="{_I forgot my password}">(?)</a>
			</span>
		</div>
		<div class="field">
			<label for="checkbox1" style="float:left; padding:0; padding-left: 24px; margin:1px; width:300px;">
				{_Remember me}
			</label>
				<input type="checkbox" name="user_remember" value="1" class="crirHiddenJS" tabindex="3" id="checkbox1" />
		</div>
		<div class="field">
			<button type="submit" tabindex="4"/>
				<span>{_Login}</span>
			</button>
			{_or} <a href="{$planet.url}/signup.php">{_register}</a>
		</div>
	</form>
</div>
<!-- END login.popup -->
