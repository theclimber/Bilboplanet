<div class="profileContent">
	<h1>{_Configure your profile}</h1>
	<form name="profile_form">
		<p>
			<label for="user_fullname">{_User id}</label>
			<input type="text" id="user_fullname" value="{$user.user_id}" disabled=true>
		</p>
		<p>
			<label for="user_fullname">{_Fullname}</label>
			<input type="text" id="user_fullname" value="{$user.user_fullname}">
		</p>
		<p>
			<label for="user_email">{_E-mail}</label>
			<input type="e-mail" id="user_email" value="{$user.user_email}">
		</p>
		<p>
			<label for="password">{_Password}</label>
			<input type="password" id="password"><br/>
			<span class="description">{_You can leave the password field blank if you don't want to change it}</span>
		</p>
		<p>
			<label for="password2">{_Confirm password}</label>
			<input type="password" id="password2">
		</p>
		<p>
			<input type="submit" id="apply" class="button" value="{_Update}">
		</p>
	</form>
</div>
