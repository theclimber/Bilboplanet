<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

if (!($_s instanceof dbStruct)) {
	throw new Exception('No valid schema object');
}

/* Tables
-------------------------------------------------------- */
$_s->article
	->num_article		('integer',	0,	false)
	->num_membre	('integer',	0,	false)
	->article_pub	('integer',	0,	true, null)
	->article_titre	('varchar',	255,	true, null)
	->article_url	('varchar',	255,	true, null)
	->article_content	('text',	255,	true, null)
	->article_statut	('integer',	0,	false, 1)
	->article_score	('integer',	0,	false,	0)
	
	->primary('pk_article','num_article')
	->index		('idx_art', 'btree', 'num_article')
	;

$_s->flux
	->num_flux		('integer',	0,	false)
	->url_flux		('varchar',	255,	true, null)
	->num_membre	('integer',	255,	false)
	
	->primary('pk_flux','num_flux')
	
	->unique('uk_membre_flux','url_flux','num_membre')
	->index		('idx_flux', 'btree', 'num_flux')
	;

$_s->membre
	->num_membre		('integer',	0,	false)
	->nom_membre	('varchar',	50,	true)
	->email_membre	('varchar',	50,	false)
	->site_membre	('varchar',	255,true)
	->statut_membre	('integer',	0,	true, null)
	
	->index ('idx_user_id','btree','nom_membre','num_membre')
	->primary('pk_membre','num_membre')
	->index		('idx_membre', 'btree', 'num_membre')
	;

$_s->votes
	->num_article		('integer',	0,	false)
	->vote_ip			('varchar',	15,	false)
	
	->primary('pk_votes','num_article', 'vote_ip')
	;


$_s->user
	->user_id			('varchar',	32,	false)
	->user_super		('smallint',	0,	true)
	->user_status		('smallint',	0,	false,	1)
	->user_pwd		('varchar',	40,	false)
	->user_recover_key	('varchar',	32,	true,	null)
	->user_name		('varchar',	255,	true,	null)
	->user_firstname	('varchar',	255,	true,	null)
	->user_displayname	('varchar',	255,	true,	null)
	->user_email		('varchar',	255,	true,	null)
	->user_url		('varchar',	255,	true,	null)
	->user_desc		('text',		0,	true)
	->user_default_blog	('varchar',	32,	true,	null)
	->user_options		('text',		0,	true)
	->user_lang		('varchar',	5,	true,	null)
	->user_tz			('varchar',	128,	false,	"'UTC'")
	->user_post_status	('smallint',	0,	false,	-2)
	->user_creadt		('timestamp',	0,	false,	'now()')
	->user_upddt		('timestamp',	0,	false,	'now()')
	
	->primary('pk_user','user_id')
	->index		('idx_user', 'btree', 'user_id')
	;

/* References indexes
-------------------------------------------------------- */
/*$_s->category->index	('idx_category_blog_id',			'btree',	'blog_id');
$_s->category->index	('idx_category_cat_lft_blog_id',	'btree',	'blog_id', 'cat_lft');
$_s->category->index	('idx_category_cat_rgt_blog_id',	'btree',	'blog_id', 'cat_rgt');
$_s->setting->index		('idx_setting_blog_id',			'btree',	'blog_id');
$_s->user->index		('idx_user_user_default_blog',	'btree',	'user_default_blog');
$_s->permissions->index	('idx_permissions_blog_id',		'btree',	'blog_id');
$_s->post->index		('idx_post_cat_id',				'btree',	'cat_id');
$_s->post->index		('idx_post_user_id',			'btree',	'user_id');
$_s->post->index		('idx_post_blog_id',			'btree',	'blog_id');
$_s->media->index		('idx_media_user_id',			'btree',	'user_id');
$_s->post_media->index	('idx_post_media_post_id',		'btree',	'post_id');
$_s->log->index		('idx_log_user_id',				'btree',	'user_id');
$_s->comment->index		('idx_comment_post_id',			'btree',	'post_id');

/* Performance indexes
-------------------------------------------------------- */
/*$_s->comment->index		('idx_comment_post_id_dt_status',	'btree',	'post_id', 'comment_dt', 'comment_status');
$_s->post->index		('idx_post_post_dt',			'btree',	'post_dt');
$_s->post->index		('idx_post_post_dt_post_id',		'btree',	'post_dt','post_id');
$_s->post->index		('idx_blog_post_post_dt_post_id',	'btree',	'blog_id','post_dt','post_id');
$_s->post->index		('idx_blog_post_post_status',		'btree',	'blog_id','post_status');
$_s->blog->index		('idx_blog_blog_upddt',			'btree',	'blog_upddt');
$_s->user->index		('idx_user_user_super',			'btree',	'user_super');

/* Foreign keys
	-------------------------------------------------------- */
/*
$_s->category->reference('fk_category_blog','blog_id','blog','blog_id','cascade','cascade');
$_s->setting->reference('fk_setting_blog','blog_id','blog','blog_id','cascade','cascade');
$_s->user->reference('fk_user_default_blog','user_default_blog','blog','blog_id','cascade','set null');
$_s->permissions->reference('fk_permissions_blog','blog_id','blog','blog_id','cascade','cascade');
$_s->permissions->reference('fk_permissions_user','user_id','user','user_id','cascade','cascade');
$_s->post->reference('fk_post_category','cat_id','category','cat_id','cascade','set null');
$_s->post->reference('fk_post_user','user_id','user','user_id','cascade','cascade');
$_s->post->reference('fk_post_blog','blog_id','blog','blog_id','cascade','cascade');
$_s->media->reference('fk_media_user','user_id','user','user_id','cascade','cascade');
$_s->post_media->reference('fk_media','media_id','media','media_id','cascade','cascade');
$_s->post_media->reference('fk_media_post','post_id','post','post_id','cascade','cascade');
$_s->ping->reference('fk_ping_post','post_id','post','post_id','cascade','cascade');
$_s->comment->reference('fk_comment_post','post_id','post','post_id','cascade','cascade');
 */

