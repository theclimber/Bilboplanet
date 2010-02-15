<?php ?>
<div id="bp_pannel">
	<h2 class="toggler headbar bdinbox"><?php echo T_('Administration');?></h2>
	<div class="element">
		<ul>
			<li>
				<a href="index.php" class="tips" rel="<?php echo T_('Access the quick summary of the planet');?>." id="dashboardm" ><?=T_('Dashboard');?></a>
			</li>
			<li>
				<a href="gestion-membre.php" class="tips" rel="<?php echo T_('Manage members');?>." id="members" ><?=T_('Members');?></a>
			</li>
			<li>
				<a href="gestion-flux.php" class="tips" rel="<?php echo T_('Manage feed');?>." id="feed" ><?=T_('Feed');?></a>
			</li>
			<li>
				<a href="gestion-articles.php" class="tips" rel="<?php echo T_('Manage articles of the planet');?>." id="articles" ><?=T_('Articles');?></a>
			</li>
		</ul>
	</div>
	<h2 class="toggler headbar bdinbox"><?php echo T_('Configuration');?></h2>
	<div class="element">
		<ul>
			<li>
				<a href="gestion-user.php" class="tips" rel="<?php echo T_('User configuration');?>." id="users" ><?=T_('Users');?></a>
			</li>
			<li>
				<a href="gestion-option.php" class="tips" rel="<?php echo T_('Planet Configuration');?>." id="planet" ><?=T_('Planet');?></a>
			</li>
		</ul>
	</div>
	<h2 class="toggler headbar bdinbox"><?php echo T_('System');?></h2>
	<div class="element">
		<ul>
			<li>
				<a href="gestion-logs.php" class="tips" rel="<?php echo T_('View logs information');?>." id="logs" ><?=T_('Logs Files');?></a>
			</li>
			<li>
				<a href="gestion-mysql.php" class="tips" rel="<?php echo T_('Import/Export Planet configuration');?>." id="database" ><?=T_('Import/Export');?></a>
			</li>
			<li>
				<a href="gestion-cache.php" class="tips" rel="<?php echo T_('Clear Planet cache');?>." id="cache" ><?=T_('Clear cache');?></a>
			</li>
			<li>
				<a href="gestion-update.php" class="tips" rel="<?php echo T_('System configuration update');?>." id="config" ><?=T_('Configuration update');?></a>
			</li>
	</div>
</div>

<div id="BP_separator" class="bgbox bdbox">
	<div class="bginbox bdinbox"></div>
</div>
