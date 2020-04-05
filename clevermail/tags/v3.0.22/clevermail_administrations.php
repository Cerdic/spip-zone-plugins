<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function clevermail_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_cm_lists','spip_cm_lists_subscribers','spip_cm_pending'
	,'spip_cm_posts','spip_cm_posts_done','spip_cm_posts_links'
	,'spip_cm_posts_queued','spip_cm_settings','spip_cm_subscribers')),
		array('peupler_base_0_0_1',array())

	);

	$maj['0.2.0'] = array(
		array('sql_alter',"TABLE cm_lists RENAME spip_cm_lists"),
		array('sql_alter',"TABLE cm_lists_subscribers RENAME spip_cm_lists_subscribers"),
		array('sql_alter',"TABLE cm_pending RENAME spip_cm_pending"),
		array('sql_alter',"TABLE cm_posts_done RENAME spip_cm_posts_done"),
		array('sql_alter',"TABLE cm_posts_links RENAME spip_cm_posts_links"),
		array('sql_alter',"TABLE cm_posts_queued RENAME spip_cm_posts_queued"),
		array('sql_alter',"TABLE cm_settings RENAME spip_cm_settings"),
		array('sql_alter',"TABLE cm_subscribers RENAME spip_cm_subscribers")
	);

	$maj['0.3.0'] = array(
		array('maj_tables',array('spip_cm_lists')));

	$maj['0.4.0'] = array(
		array('maj_tables',array('spip_cm_lists')));

	$maj['0.5.0'] = array(
		array('sql_updateq',array("spip_cm_lists_subscribers", array('lsr_mode' => 1), "lsr_mode=2")));

	$maj['0.6.0'] = array(
		array('maj_tables',array('spip_cm_lists')),
		array('sql_updateq',array('spip_cm_lists', array('lst_auto_week_days' => 'lst_auto_week_day'))),
		array('sql_alter',"TABLE spip_cm_lists DROP lst_auto_week_day"));
		
	$maj['0.7.0'] = array(
		array('maj_tables',array('spip_cm_lists')));

	$maj['0.8.0'] = array(
		array('maj_tables',array('spip_cm_lists')),
		array('sql_alter',"TABLE spip_cm_lists DROP lst_subscribe_subject_multiple"),
		array('sql_alter',"TABLE spip_cm_lists DROP lst_subscribe_text_multiple"),
		array('sql_insertq',array('spip_cm_settings',  array('set_name' => 'CM_MAIL_SUBJECT', 'set_value' => _T('clevermail:confirmation_votre_inscription_multiple')))),
		array('sql_insertq',array('spip_cm_settings',  array('set_name' => 'CM_MAIL_TEXT', 'set_value' => _T('clevermail:confirmation_votre_inscription_text_multiple')))),
     	);
	$maj['0.9.0'] = array(
		array('sql_alter',"TABLE spip_cm_lists DROP lst_subject")
    	);
	$maj['0.9.1'] = array(
		array('sql_alter',"TABLE spip_cm_lists CHANGE lst_auto_subscribers_mode lst_auto_subscribers_mode TINYINT(1) DEFAULT 1 NOT NULL;")
    	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
 }





function peupler_base_0_0_1()
{
      // pas besoin d'insert si pas de mail webmaster défini
      if ($GLOBALS['meta']['email_webmaster']){
	      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_FROM', 'set_value' => $GLOBALS['meta']['email_webmaster']));
	      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_ADMIN', 'set_value' => $GLOBALS['meta']['email_webmaster']));
	      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_MAIL_RETURN', 'set_value' => $GLOBALS['meta']['email_webmaster']));
	  }
      sql_insertq('spip_cm_settings',  array('set_name' => 'CM_SEND_NUMBER', 'set_value' => 50));
}


function clevermail_vider_tables($nom_meta_base_version) {
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