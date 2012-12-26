	<ul class="menu">
		<a href="{$planet.url}/user/?page=dashboard">
			<li id="dashboard" class="entry {$menu.dashboard}">
				<img src="{$planet.url}/themes/{$planet.theme}/user/images/dashboard.png"/>
				<span class="menu-text">{_Dashboard}</span>
		</li></a>
		<a href="{$planet.url}/user/?page=profile">
			<li id="profile" class="entry {$menu.profile}">
				<img src="{$planet.url}/themes/{$planet.theme}/user/images/profile.png"/>
				<span class="menu-text">{_Profile and feeds}</span>
		</li></a>
		<a href="{$planet.url}/user/?page=social">
			<li id="social" class="entry {$menu.social}">
				<img src="{$planet.url}/themes/{$planet.theme}/user/images/social.png"/>
				<span class="menu-text">{_Social}</span>
		</li></a>
		<a href="{$planet.url}/user/?page=write">
			<li id="write" class="entry {$menu.write}" style="display:none">
				<img src="{$planet.url}/themes/{$planet.theme}/user/images/write.png"/>
				<span class="menu-text">{_Write or share}</span>
		</li></a>
		<a href="{$planet.url}/user/?page=tribes">
			<li id="tribes" class="entry {$menu.tribes}">
				<img src="{$planet.url}/themes/{$planet.theme}/user/images/tribes.png"/>
				<span class="menu-text">{_Tribes}</span>
		</li></a>
	</ul>
