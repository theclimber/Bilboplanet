	<ul class="menu">
		<a href="{$planet.url}/user/?page=dashboard">
			<li id="dashboard" class="entry {$menu.dashboard}">
			<img src="{$planet.url}/themes/{$planet.theme}/user/images/dashboard.png"/> {_Dashboard}</li></a>
		<a href="{$planet.url}/user/?page=profile">
			<li id="profile" class="entry {$menu.profile}">
			<img src="{$planet.url}/themes/{$planet.theme}/user/images/profile.png"/> {_Profile}
		</li></a>
		<a href="{$planet.url}/user/?page=social">
			<li id="social" class="entry {$menu.social}">
			<img src="{$planet.url}/themes/{$planet.theme}/user/images/social.png"/> {_Social}
		</li></a>
		<a href="{$planet.url}/user/?page=write">
			<li id="write" class="entry {$menu.write}" style="display:none">
			<img src="{$planet.url}/themes/{$planet.theme}/user/images/write.png"/> {_Write or share}
		</li></a>
		<a href="{$planet.url}/user/?page=tribes">
			<li id="tribes" class="entry {$menu.tribes}" style="display:none">
			<img src="{$planet.url}/themes/{$planet.theme}/user/images/tribes.png"/> {_Tribes}
		</li></a>
	</ul>
