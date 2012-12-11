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
?><?php

if (!($_s instanceof dbStruct)) {
	throw new Exception('No valid schema object');
}

/* Tables
 * Utilisation : (type, length, nullable, default value)
-------------------------------------------------------- */
$_s->user
	->user_id		('varchar',		64,	false)
	->user_fullname	('varchar',		128,true)
	->user_email	('varchar',		128,false)
	->user_pwd		('varchar',		255,true)
	->user_token	('varchar',		64,	true, null)
	->user_status	('smallint',	0,	false, 1)
	->user_lang		('varchar',	5,	true, null)
	->user_recover_key	('varchar',	32,	true,	null)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_user','user_id')
	;

$_s->pending_feed
    ->pending_id    ('integer',     0,  false)
	->user_id		('varchar',		64,	false)
	->site_url		('text',		0,	false)
	->feed_url		('text',		0,	false)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_pending_user','pending_id')
	;

$_s->post
	->post_id		('integer',		0,	false)
	->user_id		('varchar',		64,	false)
	->post_pubdate	('timestamp',	0,	false, 'now()')
	->post_permalink	('text',	0,	true, null)
	->post_title		('text',	0,	true, null)
	->post_content	('text',		0,  true, null)
	->post_image	('text',		0,	true, null)
	->post_status	('smallint',	0,	false, 1)
	->post_comment	('smallint',	0,	false, 1)
	->post_score	('integer',		0,	false, 0)
	->post_nbview	('integer',		0,	false, 0)
	->last_viewed	('timestamp',	0,	true, null)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_post','post_id')
	;

$_s->post_share
	->post_id		('integer',		0, false)
	->engine		('varchar',		255, false)
	->nb_share		('integer',		0, false)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_post_id', 'post_id', 'engine')
	;

$_s->post_click
	->post_id		('integer',		0, false)
	->surfer_id		('varchar',		255, false)
	->created		('timestamp',	0,	true, 'now()')

	->primary('pk_post_id', 'post_id', 'surfer_id')
	;

$_s->feed
	->feed_id		('integer',		0,	false)
	->user_id		('varchar',		64,	false)
	->site_id		('integer',		0,	false)
	->feed_name		('varchar',		255,true, null)
	->feed_url		('text',		0,	false)
	->feed_checked	('timestamp',	0,	true, null)
	->feed_status	('smallint',	0,	false, 1)
	->feed_trust	('smallint',	0,	false, 0)
	->feed_comment	('smallint',	0,	false, 0)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_feed','feed_id')
	;

$_s->feed_tag
	->tag_id		('varchar',		255, false)
	->feed_id		('integer',		0, false)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_feed_tag', 'tag_id', 'feed_id')
	;

$_s->post_tag
	->tag_id		('varchar',		255, false)
	->post_id		('integer',		0, false)
	->user_id		('varchar',		64,	false)
	->created		('timestamp',	0,	true, 'now()')

	->primary('pk_post_tag', 'tag_id', 'post_id')
	;

$_s->comment
	->comment_id	('integer',		0, false)
	->post_id		('integer',		0, true, null)
	->tribe_id		('integer',		0, true, null)
	->user_fullname	('varchar',		128,false)
	->user_email	('varchar',		128,false)
	->user_site		('text',		0,	false)
	->content		('text',		0,  true, null)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_post_comment', 'comment_id')
	;

$_s->site
	->site_id		('integer',		0,	false)
	->user_id		('varchar',		64,	false)
	->site_name		('varchar',		255,true, null)
	->site_url		('text',		0,	false)
	->site_status	('smallint',	0,	false, 1)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_site','site_id')
	;

$_s->votes
	->post_id		('integer',		0,	false)
	->user_id		('varchar',		64,	false)
	->vote_ip		('varchar',		255,true, null)
	->vote_value	('integer',		0,	false, 0)
	->created		('timestamp',	0,	true, 'now()')

	->primary('pk_votes','post_id','user_id')
	;

$_s->permissions
	->user_id		('varchar',		64,	false)
	->permissions	('text',		0,	true)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_permissions', 'user_id')
	;

$_s->setting
	->setting_id	('varchar',		255,	false)
	->user_id		('varchar',		64,	true)
	->setting_value	('text',		0,	true, null)
	->setting_label	('text',		0,	true, null)
	->setting_type	('varchar',		8,	false,	"'string'")
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_setting','setting_id', 'user_id')
	;

$_s->session
	->ses_id	('varchar',	40,	false)
	->ses_time	('integer',	0,	false,	0)
	->ses_start	('integer',	0,	false,	0)
	->ses_value	('text',		0,	false)

	->primary('pk_session','ses_id')
	;

$_s->tribe
	->tribe_id		('varchar',		64,	true)
	->user_id		('varchar',		64,	true)
	->ordering		('integer',		0, true, 100)
	->visibility	('smallint',	0,	false, 0)
	->tribe_name	('varchar',		128,false)
	->tribe_search	('text',		0,	true, null)
	->tribe_tags	('text',		0,	true, null)
	->tribe_notags	('text',		0,	true, null)
	->tribe_users	('text',		0,	true, null)
	->tribe_nousers	('text',		0,	true, null)
	->tribe_icon	('text',		0,	true, null)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_tribe', 'tribe_id')
	;

/* References indexes
-------------------------------------------------------- */
$_s->post->index	('idx_post_user_id', 'btree', 'user_id');
$_s->feed->index	('idx_feed_user_id', 'btree', 'user_id');
$_s->feed->index	('idx_feed_site_id', 'btree', 'site_id');
$_s->site->index	('idx_site_user_id', 'btree', 'user_id');
$_s->permissions->index	('idx_permissions_user_id', 'btree', 'user_id');
$_s->setting->index	('idx_setting_user_id', 'btree', 'user_id');

/* Foreign keys
	-------------------------------------------------------- */
$_s->post->reference('fk_post_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');
$_s->feed->reference('fk_feed_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');
$_s->feed->reference('fk_feed_site', 'site_id', 'site', 'site_id', 'cascade', 'cascade');
$_s->site->reference('fk_site_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');
$_s->permissions->reference('fk_permissions_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');

