<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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
?>
<div id="bp_pannel">
	<h2 class="toggler headbar bdinbox"><?php echo T_('Common');?></h2>
	<div class="element">
		<ul>
		<li><a href="index.php" class="tips" rel="<?php echo T_('Access the quick summary of the planet');?>." id="dashboardm" ><?=T_('Dashboard');?></a></li>
		<li><a href="manage-account.php" class="tips" rel="<?php echo T_('Manage your profile account');?>." id="accountm" ><?=T_('Manage your account');?></a></li>
		</ul>
	</div>

<?php if ($core->hasPermission('administration')): ?>
	<h2 class="toggler headbar bdinbox"><?php echo T_('Administration');?></h2>
	<div class="element">
		<ul>
		<li>
			</li>
			<li>
				<a href="manage-user.php" class="tips" rel="<?php echo T_('Manage users');?>." id="members" ><?=T_('Manage users');?></a>
			</li>
			<li>
				<a href="manage-feed.php" class="tips" rel="<?php echo T_('Manage feeds');?>." id="feed" ><?=T_('Manage feeds');?></a>
			</li>
			<li>
				<a href="manage-post.php" class="tips" rel="<?php echo T_('Manage posts');?>." id="articles" ><?=T_('Manage posts');?></a>
			</li>
            <li>
            	<a href="manage-newsletter.php" class="tips" rel="<?php echo T_('Manage newsletter of the planet');?>." id="newsletter" ><?=T_('Newsletter');?></a>
			</li>
		</ul>
	</div>
<?php endif; ?>

<?php if ($core->hasPermission('configuration')): ?>
	<h2 class="toggler headbar bdinbox"><?php echo T_('Configuration');?></h2>
	<div class="element">
		<ul>
			<li>
				<a href="manage-useroption.php" class="tips" rel="<?php echo T_('Author configuration');?>." id="users" ><?=T_('Planet Author');?></a>
			</li>
			<li>
				<a href="manage-option.php" class="tips" rel="<?php echo T_('Planet Configuration');?>." id="planet" ><?=T_('Planet configuration');?></a>
			</li>
			<li>
				<a href="manage-logs.php" class="tips" rel="<?php echo T_('View logs information');?>." id="logs" ><?=T_('Logs Files');?></a>
			</li>
			<li>
				<a href="manage-database.php" class="tips" rel="<?php echo T_('Import/Export Planet configuration');?>." id="database" ><?=T_('Import/Export');?></a>
			</li>
			<li>
				<a href="manage-cache.php" class="tips" rel="<?php echo T_('Clear Planet cache');?>." id="cache" ><?=T_('Clear cache');?></a>
			</li>
			<li>
				<a href="manage-update.php" class="tips" rel="<?php echo T_('Feed fetching system configuration');?>." id="config" ><?=T_('Feed fetching');?></a>
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
					<?=T_('Moderation');?></a>
			</li>
<?php
/*
			<li>
				<a href="manage-selection.php" class="tips" rel="
					<?php echo T_('Make your selection of the week');?>." id="selection" >
					<?=T_('Selection of the week');?></a>
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
					<?=T_('Permissions');?></a>
			</li>
		</ul>
	</div>
<?php endif; ?>
</div>

<div id="BP_separator" class="bgbox bdbox">
	<div class="bginbox bdinbox"></div>
</div>
