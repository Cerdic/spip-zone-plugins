<?php
global $tables_principales;

$spip_cm_lists = array(
    "lst_id" => "INT(11) NOT NULL auto_increment",
    "lst_name" => "VARCHAR(255) NOT NULL",
    "lst_comment" => "TEXT NOT NULL",
    "lst_moderation" => "VARCHAR(10) NOT NULL",
    "lst_moderator_email" => "VARCHAR(255) NOT NULL",
    "lst_subscribe_subject" => "VARCHAR(255) NOT NULL",
    "lst_subscribe_text" => "TEXT NOT NULL",
    "lst_subject" => "VARCHAR(255) NOT NULL",
    "lst_unsubscribe_subject" => "VARCHAR(255) NOT NULL",
    "lst_unsubscribe_text" => "TEXT NOT NULL",
    "lst_subject_tag" => "TINYINT(1) NOT NULL default '1'",
    "lst_url_html" => "VARCHAR(255) NOT NULL",
    "lst_url_text" => "VARCHAR(255) NOT NULL"
);

$spip_cm_lists_key = array(
    "PRIMARY KEY" => "lst_id"
);

$spip_cm_lists_subscribers = array(
	"lst_id" => "INT(11) NOT NULL",
	"sub_id" => "BIGINT(20) NOT NULL",
	"lsr_mode" => "TINYINT(1) NOT NULL",
	"lsr_id" => "VARCHAR(32) NOT NULL"
);

$spip_cm_lists_subscribers_key = array(
    "PRIMARY KEY" => "lst_id, sub_id",
    "KEY lst_id" => "lst_id"
);

$spip_cm_pending = array(
	"lst_id" => "int(11) NOT NULL",
	"sub_id" => "bigint(20) NOT NULL",
	"pnd_action" => "varchar(15) NOT NULL",
	"pnd_mode" => "tinyint(1) NOT NULL",
 	"pnd_action_date" => "int(11) NOT NULL",
	"pnd_action_id" => "varchar(32) NOT NULL"
);

$spip_cm_pending_key = array(
	"PRIMARY KEY" => "lst_id, sub_id"
);

$spip_cm_posts = array(
	"pst_id" => "bigint(20) NOT NULL auto_increment",
	"lst_id" => "int(11) NOT NULL",
	"pst_date_create" => "int(11) NOT NULL",
	"pst_date_update" => "int(11) NOT NULL",
	"pst_date_sent" => "int(11) NOT NULL",
	"pst_subject" => "varchar(255) NOT NULL",
	"pst_html" => "longtext NOT NULL",
	"pst_text" => "longtext NOT NULL",
);

$spip_cm_posts_key = array(
	"PRIMARY KEY" => "pst_id"
);

$spip_cm_posts_done = array(
	"pst_id" => "bigint(20) NOT NULL",
	"sub_id" => "bigint(20) NOT NULL"
);

$spip_cm_posts_done_key = array(
	"PRIMARY KEY" => "pst_id, sub_id"
);

$spip_cm_posts_links = array(
	"lnk_id" => "bigint(20) NOT NULL",
	"pst_id" => "bigint(20) NOT NULL",
	"lnk_name" => "varchar(255) NOT NULL",
	"lnk_url" => "text NOT NULL"
);

$spip_cm_posts_links_key = array(
	"PRIMARY KEY" => "lnk_id"
);

$spip_cm_posts_queued = array(
	"pst_id" => "bigint(20) NOT NULL",
	"sub_id" => "bigint(20) NOT NULL",
	"psq_date" => "int(11) NOT NULL"
);

$spip_cm_posts_queued_key = array(
	"PRIMARY KEY" => "pst_id, sub_id"
);

$spip_cm_settings = array(
	"set_name" => "varchar(15) NOT NULL",
	"set_value" => "varchar(255) NOT NULL"
);

$spip_cm_settings_key = array(
	//"PRIMARY KEY" => "set_name"
);

$spip_cm_subscribers = array(
	"sub_id" => "bigint(20) NOT NULL auto_increment",
	"sub_email" => "varchar(255) NOT NULL",
	"sub_profile" => "varchar(32) NOT NULL"
);

$spip_cm_subscribers_key = array(
	"PRIMARY KEY" => "sub_id",
	"KEY sub_profile" => "sub_profile",
	"KEY sub_email" => "sub_email"
);

$tables_principales['cm_lists'] = array(
  'field' => &$spip_cm_lists,
  'key' => &$spip_cm_lists_key
);

$tables_principales['cm_lists_subscribers'] = array(
  'field' => &$spip_cm_lists_subscribers,
  'key' => &$spip_cm_lists_subscribers_key
);

$tables_principales['cm_pending'] = array(
  'field' => &$spip_cm_pending,
  'key' => &$spip_cm_pending_key
);

$tables_principales['cm_posts'] = array(
  'field' => &$spip_cm_posts,
  'key' => &$spip_cm_posts_key
);

$tables_principales['cm_posts_done'] = array(
  'field' => &$spip_cm_posts_done,
  'key' => &$spip_cm_posts_done_key
);

$tables_principales['cm_posts_links'] = array(
  'field' => &$spip_cm_posts_links,
  'key' => &$spip_cm_posts_links_key
);

$tables_principales['cm_posts_queued'] = array(
  'field' => &$spip_cm_posts_queued,
  'key' => &$spip_cm_posts_queued_key
);

$tables_principales['cm_settings'] = array(
  'field' => &$spip_cm_settings,
  'key' => &$spip_cm_settings_key
);

$tables_principales['cm_subscribers'] = array(
  'field' => &$spip_cm_subscribers,
  'key' => &$spip_cm_subscribers_key
);
?>