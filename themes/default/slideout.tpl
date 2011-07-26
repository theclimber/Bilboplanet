	<div id="loginBox" class="page-wrap">
		<div class="loginLeft">
			<ul>
				<li>Blabla</li>
				<li>Blublu</li>
			</ul>
		</div>
		<div class="loginRight">
<!-- BEGIN page.loginbox -->
			{_Welcome} {$login.username}
			| <a href="javascript:popup('{$planet.url}/user/')">Dashboard</a>
		<!-- BEGIN page.loginadmin -->
			| <a href="{$planet.url}/admin/">Administration</a>
		<!-- END page.loginadmin -->
			| <a href="?logout={$planet.url}">Logout</a>
<!-- ELSE page.loginbox -->
			<form class="login" method="POST" action="{$planet.url}/auth.php">
				<input type="hidden" name="came_from" value="{$planet.url}"><br>
				<input type="text" name="user_id" value="" placeholder="{_Login}"><br>
				<input type="password" name="user_pwd" value="" placeholder="{_Password}"><br>
				<label class="remember">
					<input type="checkbox" name="user_remember" value="1" checked>
					<span>{_Remember me}</span>
				</label>
				<input class="submit button" type="submit" value="{_Connect}" />
				</p><p>
				<a href="{$planet.url}/auth.php?recover=1" class="forgot">{_Password forgotten?}</a><br>
				</p>
			</form>
<!-- END page.loginbox -->
		</div>
		<div class="clear"></div>
		<a href="#" class="loginMenuButton">Login | Register</a>
	</div>
