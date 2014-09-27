<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
* Blog : www.bilboplanet.com
*
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
***** END LICENSE BLOCK *****/
$pendingfeed = getNbPendingFeed();
?>
<div id="bp_pannel">
	<h2 class="toggler headbar bdinbox"><?php echo T_('Common'); ?></h2>
	<div class="element">
		<ul>
		<li><a href="index.php" class="tips" rel="<?php echo T_('Access the quick summary of the planet');?>." id="dashboardm" ><?php echo T_('Dashboard');?></a></li>
		<li><a href="manage-account.php" class="tips" rel="<?php echo T_('Manage your profile account');?>." id="accountm" ><?php echo T_('Manage your account');?></a></li>
		</ul>
	</div>

<?php if ($core->hasPermission('administration')): ?>
	<h2 class="toggler headbar bdinbox"><?php echo T_('Administration');?></h2>
	<div class="element">
		<ul>
		<li>
			</li>
            <li>
            	<a href="manage-tribe.php" class="tips" rel="<?php echo T_('Manage tribes');?>." id="tribes" ><?php echo T_('Tribes');?></a>
			</li>
			<li>
				<a href="manage-user.php" class="tips" rel="<?php echo T_('Manage users');?>." id="members" ><?php echo T_('Manage users');?></a>
			</li>
			<li>
				<a href="manage-pendingfeed.php" class="tips" rel="<?php echo T_('Pending feeds');?>." id="pendingfeed" ><?php echo T_('Pending feeds');?> 
<?php 
if ($pendingfeed > 0) {
	echo '<span class="pending-nbr">'.$pendingfeed.'</span>';
}
?>
</a>
			</li>
			<li>
				<a href="manage-feed.php" class="tips" rel="<?php echo T_('Manage feeds');?>." id="feed" ><?php echo T_('Manage feeds');?></a>
			</li>
			<li>
				<a href="manage-post.php" class="tips" rel="<?php echo T_('Manage posts');?>." id="articles" ><?php echo T_('Manage posts');?></a>
			</li>
            <li>
            	<a href="manage-newsletter.php" class="tips" rel="<?php echo T_('Manage newsletter of the planet');?>." id="newsletter" ><?php echo T_('Newsletter');?></a>
			</li>
		</ul>
	</div>
<?php endif; ?>

<?php if ($core->hasPermission('configuration')): ?>
	<h2 class="toggler headbar bdinbox"><?php echo T_('Configuration');?></h2>
	<div class="element">
		<ul>
			<li>
				<a href="manage-useroption.php" class="tips" rel="<?php echo T_('Author configuration');?>." id="users" ><?php echo T_('Planet Author');?></a>
			</li>
			<li>
				<a href="manage-option.php" class="tips" rel="<?php echo T_('Planet Configuration');?>." id="planet" ><?php echo T_('Planet configuration');?></a>
			</li>
			<li>
				<a href="manage-logs.php" class="tips" rel="<?php echo T_('View logs information');?>." id="logs" ><?php echo T_('Logs Files');?></a>
			</li>
			<li>
				<a href="manage-database.php" class="tips" rel="<?php echo T_('Import/Export Planet configuration');?>." id="database" ><?php echo T_('Import/Export');?></a>
			</li>
			<li>
				<a href="manage-cache.php" class="tips" rel="<?php echo T_('Clear Planet cache');?>." id="cache" ><?php echo T_('Clear cache');?></a>
			</li>
			<li>
				<a href="manage-update.php" class="tips" rel="<?php echo T_('Feed fetching system configuration');?>." id="config" ><?php echo T_('Feed fetching');?></a>
			</li>
	</div>
<?php endif; ?>

<?php if ($core->hasPermission('moderation')): ?>
	<h2 class="toggler headbar bdinbox"><?php echo T_('Moderation');?></h2>
	<div class="element">
		<ul>
			<li>
				<a href="manage-moderation.php" class="tips" rel="
					<?php echo T_('Moderation interface');?>." id="moderation" >
					<?php echo T_('Moderation');?></a>
			</li>
			<li>
				<a href="manage-tagging.php" class="tips" rel="
					<?php echo T_('Tag the last posts');?>." id="tagging">
					<?php echo T_('Tagging');?></a>
			</li>
<?php
/*
			<li>
				<a href="manage-selection.php" class="tips" rel="
					<?php echo T_('Make your selection of the week');?>." id="selection" >
					<?php echo T_('Selection of the week');?></a>
			</li>
*/
?>
		</ul>
	</div>
<?php endif; ?>

<?php if ($core->auth->superUser()): ?>
	<h2 class="toggler headbar bdinbox"><?php echo T_('System');?></h2>
	<div class="element">
		<ul>
			<li>
				<a href="manage-permissions.php" class="tips" rel="
					<?php echo T_('Manage user permissions');?>." id="permissions" >
					<?php echo T_('Permissions');?></a>
			</li>
		</ul>
	</div>
<?php endif; ?>
</div>

<div id="BP_separator" class="bgbox bdbox">
	<div class="bginbox bdinbox"></div>
</div>
