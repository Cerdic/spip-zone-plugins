<?php
function clevermail_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['cm_lists']='cm_lists';
	$interface['table_des_tables']['cm_lists_subscribers']='cm_lists_subscribers';
	$interface['table_des_tables']['cm_pending']='cm_pending';
	$interface['table_des_tables']['cm_posts']='cm_posts';
	$interface['table_des_tables']['cm_posts_done']='cm_posts_done';
	$interface['table_des_tables']['cm_posts_links']='cm_posts_links';
	$interface['table_des_tables']['cm_posts_queued']='cm_posts_queued';
	$interface['table_des_tables']['cm_settings']='cm_settings';
	$interface['table_des_tables']['cm_subscribers']='cm_subscribers';
	return $interface;
}

function clevermail_declarer_tables_principales($tables_principales) {
	// spip_cm_lists
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
	    "lst_url_text" => "VARCHAR(255) NOT NULL",
	    "lst_auto_mode" => "ENUM('none', 'day', 'week', 'month') DEFAULT 'none'",
	    "lst_auto_hour" => "TINYINT(2) NOT NULL default '8'",
	    "lst_auto_week_day" => "TINYINT(1) NOT NULL default '1'", // 0 = dimanche
        "lst_auto_week_days" => "VARCHAR(13) NOT NULL default '1'", // concatenation numeros des jours, 0 = dimanche
	    "lst_auto_month_day" => "TINYINT(2) NOT NULL default '1'",
	    "lst_auto_subscribers" => "VARCHAR(255) NOT NULL",
	    "lst_auto_subscribers_mode" => "TINYINT(1) NOT NULL default '0'",
	    "lst_auto_subscribers_updated" => "int(11) NOT NULL default '0'"
	);

	$spip_cm_lists_key = array(
	    "PRIMARY KEY" => "lst_id"
	);

	$tables_principales['spip_cm_lists'] = array(
    'field' => &$spip_cm_lists,
    'key' => &$spip_cm_lists_key
  );

	// spip_cm_lists_subscribers
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

  $tables_principales['spip_cm_lists_subscribers'] = array(
    'field' => &$spip_cm_lists_subscribers,
    'key' => &$spip_cm_lists_subscribers_key
  );

	// spip_cm_pending
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

  $tables_principales['spip_cm_pending'] = array(
    'field' => &$spip_cm_pending,
    'key' => &$spip_cm_pending_key
  );

	// spip_cm_posts
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

  $tables_principales['spip_cm_posts'] = array(
    'field' => &$spip_cm_posts,
    'key' => &$spip_cm_posts_key
  );

	// spip_cm_posts_done
	$spip_cm_posts_done = array(
	  "pst_id" => "bigint(20) NOT NULL",
	  "sub_id" => "bigint(20) NOT NULL"
	);

	$spip_cm_posts_done_key = array(
	  "PRIMARY KEY" => "pst_id, sub_id"
	);

  $tables_principales['spip_cm_posts_done'] = array(
    'field' => &$spip_cm_posts_done,
    'key' => &$spip_cm_posts_done_key
  );

	// spip_cm_posts_links
	$spip_cm_posts_links = array(
	  "lnk_id" => "bigint(20) NOT NULL",
	  "pst_id" => "bigint(20) NOT NULL",
	  "lnk_name" => "varchar(255) NOT NULL",
	  "lnk_url" => "text NOT NULL"
	);

	$spip_cm_posts_links_key = array(
	  "PRIMARY KEY" => "lnk_id"
	);

  $tables_principales['spip_cm_posts_links'] = array(
    'field' => &$spip_cm_posts_links,
    'key' => &$spip_cm_posts_links_key
  );

	// spip_cm_posts_queued
	$spip_cm_posts_queued = array(
	  "pst_id" => "bigint(20) NOT NULL",
	  "sub_id" => "bigint(20) NOT NULL",
	  "psq_date" => "int(11) NOT NULL"
	);

	$spip_cm_posts_queued_key = array(
	  "PRIMARY KEY" => "pst_id, sub_id"
	);

  $tables_principales['spip_cm_posts_queued'] = array(
    'field' => &$spip_cm_posts_queued,
    'key' => &$spip_cm_posts_queued_key
  );

	// spip_cm_settings
	$spip_cm_settings = array(
	  "set_name" => "varchar(15) NOT NULL",
	  "set_value" => "varchar(255) NOT NULL"
	);

	$spip_cm_settings_key = array(
	  //"PRIMARY KEY" => "set_name"
	);

  $tables_principales['spip_cm_settings'] = array(
    'field' => &$spip_cm_settings,
    'key' => &$spip_cm_settings_key
  );

	// spip_cm_subscribers
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

  $tables_principales['spip_cm_subscribers'] = array(
    'field' => &$spip_cm_subscribers,
    'key' => &$spip_cm_subscribers_key
  );

  return $tables_principales;
}

function clevermail_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('inc/meta');
  $current_version = 0.0;
  if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
      || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
    if (version_compare($current_version,'0.1','<')) {
      include_spip('base/abstract_sql');
      include_spip('base/create');
      creer_base();
      // pas besoin d'insert si pas de mail webmaster défini
      if ($GLOBALS['meta']['email_webmaster']){
	      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_FROM', 'set_value' => $GLOBALS['meta']['email_webmaster']));
	      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_ADMIN', 'set_value' => $GLOBALS['meta']['email_webmaster']));
	      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_RETURN', 'set_value' => $GLOBALS['meta']['email_webmaster']));
	  }
      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_SEND_NUMBER', 'set_value' => 50));
      ecrire_meta($nom_meta_base_version,$current_version="0.1",'non');
      spip_log('Installation des tables du plugin CleverMail en version 0.1', 'clevermail');
    }
    if (version_compare($current_version,'0.2','<')) {
		  sql_alter("TABLE cm_lists RENAME spip_cm_lists");
		  sql_alter("TABLE cm_lists_subscribers RENAME spip_cm_lists_subscribers");
		  sql_alter("TABLE cm_pending RENAME spip_cm_pending");
		  sql_alter("TABLE cm_posts RENAME spip_cm_posts");
		  sql_alter("TABLE cm_posts_done RENAME spip_cm_posts_done");
		  sql_alter("TABLE cm_posts_links RENAME spip_cm_posts_links");
		  sql_alter("TABLE cm_posts_queued RENAME spip_cm_posts_queued");
		  sql_alter("TABLE cm_settings RENAME spip_cm_settings");
		  sql_alter("TABLE cm_subscribers RENAME spip_cm_subscribers");
      ecrire_meta($nom_meta_base_version,$current_version="0.2",'non');
      spip_log('Mise à jour des tables du plugin CleverMail en version 0.2', 'clevermail');
    }
    if (version_compare($current_version,'0.3','<')) {
      include_spip('base/abstract_sql');
      include_spip('base/create');
    	maj_tables('spip_cm_lists');
      ecrire_meta($nom_meta_base_version,$current_version="0.3",'non');
      spip_log('Mise à jour des tables du plugin CleverMail en version 0.3', 'clevermail');
    }
    if (version_compare($current_version,'0.4','<')) {
      include_spip('base/abstract_sql');
      include_spip('base/create');
      maj_tables('spip_cm_lists');
      ecrire_meta($nom_meta_base_version,$current_version="0.4",'non');
      spip_log('Mise à jour des tables du plugin CleverMail en version 0.4', 'clevermail');
    }
    if (version_compare($current_version,'0.5','<')) {
      include_spip('base/abstract_sql');
      // On avait inventé un troisième mode pour rien
      sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => 1), "lsr_mode=2");
      ecrire_meta($nom_meta_base_version,$current_version="0.5",'non');
      spip_log('Mise à jour des tables du plugin CleverMail en version 0.5', 'clevermail');
    }
    if (version_compare($current_version,'0.6','<')) {
      include_spip('base/abstract_sql');
      include_spip('base/create');
      maj_tables('spip_cm_lists');
      sql_update('spip_cm_lists', array('lst_auto_week_days' => 'lst_auto_week_day')); 
      sql_alter("TABLE spip_cm_lists DROP lst_auto_week_day");
      ecrire_meta($nom_meta_base_version,$current_version="0.6",'non');
      spip_log('Mise à jour des tables du plugin CleverMail en version 0.6', 'clevermail');
    }
    if (version_compare($current_version,'0.7','<')) {
      include_spip('base/abstract_sql');
      include_spip('base/create');
      maj_tables('spip_cm_lists');
      ecrire_meta($nom_meta_base_version,$current_version="0.7",'non');
      spip_log('Mise à jour des tables du plugin CleverMail en version 0.7', 'clevermail');
    }
    if (version_compare($current_version,'0.8','<')) {
      include_spip('base/abstract_sql');
      include_spip('base/create');
      maj_tables('spip_cm_lists');
      sql_alter("TABLE spip_cm_lists DROP lst_subscribe_subject_multiple");
      sql_alter("TABLE spip_cm_lists DROP lst_subscribe_text_multiple");
      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_SUBJECT', 'set_value' => _T('clevermail:confirmation_votre_inscription_multiple')));
      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_TEXT', 'set_value' => _T('clevermail:confirmation_votre_inscription_text_multiple')));
      ecrire_meta($nom_meta_base_version,$current_version="0.8",'non');
      spip_log('Mise à jour des tables du plugin CleverMail en version 0.8', 'clevermail');
    }
  }
}

function clevermail_vider_tables($nom_meta_base_version) {
  include_spip('inc/meta');
  include_spip('base/abstract_sql');
  sql_drop_table('spip_cm_lists');
  sql_drop_table('spip_cm_lists_subscribers');
  sql_drop_table('spip_cm_pending');
  sql_drop_table('spip_cm_posts');
  sql_drop_table('spip_cm_posts_done');
  sql_drop_table('spip_cm_posts_links');
  sql_drop_table('spip_cm_posts_queued');
  sql_drop_table('spip_cm_settings');
  sql_drop_table('spip_cm_subscribers');
  effacer_meta($nom_meta_base_version);
  spip_log('Suppression des tables du plugin CleverMail', 'clevermail');
}
?>