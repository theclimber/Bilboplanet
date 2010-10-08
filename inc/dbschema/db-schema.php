<?php

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

$_s->pending_user
	->puser_id		('varchar',		64,	false)
	->user_fullname	('varchar',		128,true)
	->user_email	('varchar',		128,false)
	->user_pwd		('varchar',		255,true)
	->user_lang		('varchar',		5,	true, null)
	->licence		('varchar',		255,true, null)
	->feed_url		('text',		0,	false)
	->feed_tags		('text',		0,	false)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_pending_user','puser_id')
	;

$_s->post
	->post_id		('integer',		0,	false)
	->user_id		('varchar',		64,	false)
	->feed_id		('integer',		0,	false)
	->post_pubdate	('timestamp',	0,	false, 'now()')
	->post_permalink	('text',	0,	true, null)
	->post_title		('text',	0,	true, null)
	->post_content	('text',		255,true, null)
	->post_status	('smallint',	0,	false, 1)
	->post_score	('integer',		0,	false, 0)
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')
	
	->primary('pk_post','post_id')
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
	->created		('timestamp',	0,	true, 'now()')
	->modified		('timestamp',	0,	true, 'now()')

	->primary('pk_feed','feed_id')
	;

$_s->feed_tag
	->tag_id		('varchar',		255, false)
	->feed_id		('integer',		0, false)

	->primary('pk_feed_tag', 'tag_id', 'feed_id')
	;

$_s->post_tag
	->tag_id		('varchar',		255, false)
	->post_id		('integer',		0, false)

	->primary('pk_post_tag', 'tag_id', 'post_id')
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

	->primary('pk_setting','setting_id')
	;

$_s->session
	->ses_id	('varchar',	40,	false)
	->ses_time	('integer',	0,	false,	0)
	->ses_start	('integer',	0,	false,	0)
	->ses_value	('text',		0,	false)
	
	->primary('pk_session','ses_id')
	;


/* References indexes
-------------------------------------------------------- */
$_s->post->index	('idx_post_user_id', 'btree', 'user_id');
$_s->post->index	('idx_post_feed_id', 'btree', 'feed_id');
$_s->feed->index	('idx_feed_user_id', 'btree', 'user_id');
$_s->feed->index	('idx_feed_site_id', 'btree', 'site_id');
$_s->site->index	('idx_site_user_id', 'btree', 'user_id');
$_s->permissions->index	('idx_permissions_user_id', 'btree', 'user_id');
$_s->setting->index	('idx_setting_user_id', 'btree', 'user_id');

/* Foreign keys
	-------------------------------------------------------- */
$_s->post->reference('fk_post_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');
$_s->post->reference('fk_post_feed', 'feed_id', 'feed', 'feed_id', 'cascade', 'cascade');
$_s->feed->reference('fk_feed_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');
$_s->feed->reference('fk_feed_site', 'site_id', 'site', 'site_id', 'cascade', 'cascade');
$_s->site->reference('fk_site_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');
$_s->permissions->reference('fk_permissions_user', 'user_id', 'user', 'user_id', 'cascade', 'cascade');

