<div class="menu list-group">
	<a id="dashboard" href="{$planet.url}/user/?page=dashboard"  class="entry {$menu.dashboard} list-group-item">

			<i class="fa fa-lg fa-tachometer"></i>
			<span class="menu-text">{_Dashboard}</span>
	</a>
	<a id="profile" href="{$planet.url}/user/?page=profile" class="entry {$menu.profile} list-group-item" >

		<i class="fa fa-lg fa-user"></i>
			<span class="menu-text">{_Profile and feeds}</span>
	</a>
	<a id="social" href="{$planet.url}/user/?page=social" class="entry {$menu.social} list-group-item" >

		<i class="fa fa-lg fa-share-alt-square"></i>
			<span class="menu-text">{_Social}</span>
	</a>
	<a id="write" href="{$planet.url}/user/?page=write" class="entry {$menu.write} list-group-item" style="display:none">

			<img src="{$planet.url}/themes/{$planet.theme}/user/images/write.png"/>
			<span class="menu-text">{_Write or share}</span>
		</a>
	<a id="tribes" href="{$planet.url}/user/?page=tribes" class="entry {$menu.tribes} list-group-item" >

		<i class="fa fa-lg fa-users"></i>
			<span class="menu-text">{_Tribes}</span>
	</a>
</div>
