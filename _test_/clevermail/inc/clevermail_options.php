<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006
	 *
	 **/

// New line string, which should be:
//		\n		on unices
//		\r		on Mac OS
//		\r\n	on Windows
define('CM_NEWLINE', "\n");

// Ajoute le bouton du plugin dans l'interface du back-office
function clevermail_ajouter_boutons($boutons_admin) {
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		$boutons_admin['configuration']->sousmenu['clevermail_index'] = new Bouton(_DIR_PLUGIN_CLEVERMAIL.'/img_pack/enveloppe.png', 'CleverMail');
	}
	return $boutons_admin;
}

function clevermail_creer_table() {
spip_query("CREATE TABLE IF NOT EXISTS `cm_lists` (
  `lst_id` int(11) NOT NULL auto_increment,
  `lst_name` varchar(255) NOT NULL default '',
  `lst_comment` text NOT NULL,
  `lst_moderation` varchar(10) NOT NULL default '',
  `lst_moderator_email` varchar(255) NOT NULL default '',
  `lst_subscribe_subject` varchar(255) NOT NULL default '',
  `lst_subscribe_text` text NOT NULL,
  `lst_subject` varchar(255) NOT NULL default '',
  `lst_unsubscribe_subject` varchar(255) NOT NULL default '',
  `lst_unsubscribe_text` text NOT NULL,
  `lst_subject_tag` tinyint(1) NOT NULL default '1',
  `lst_url_html` varchar(255) NOT NULL,
  `lst_url_text` varchar(255) NOT NULL,
  PRIMARY KEY  (`lst_id`)
) TYPE=MyISAM AUTO_INCREMENT=0 ;");

spip_query("CREATE TABLE IF NOT EXISTS `cm_lists_subscribers` (
  `lst_id` int(11) NOT NULL default '0',
  `sub_id` bigint(20) NOT NULL default '0',
  `lsr_mode` tinyint(1) NOT NULL default '0',
  `lsr_id` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`lst_id`,`sub_id`),
  KEY `lst_id` (`lst_id`)
) TYPE=MyISAM;");


spip_query("CREATE TABLE IF NOT EXISTS `cm_pending` (
  `lst_id` int(11) NOT NULL default '0',
  `sub_id` bigint(20) NOT NULL default '0',
  `pnd_action` varchar(15) NOT NULL default '',
  `pnd_mode` tinyint(1) NOT NULL default '0',
  `pnd_action_date` int(11) NOT NULL default '0',
  `pnd_action_id` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`lst_id`,`sub_id`)
) TYPE=MyISAM;");

spip_query("CREATE TABLE IF NOT EXISTS `cm_posts` (
  `pst_id` bigint(20) NOT NULL auto_increment,
  `lst_id` int(11) NOT NULL default '0',
  `pst_date_create` int(11) NOT NULL default '0',
  `pst_date_update` int(11) NOT NULL default '0',
  `pst_date_sent` int(11) NOT NULL default '0',
  `pst_subject` varchar(255) NOT NULL default '',
  `pst_html` longtext NOT NULL,
  `pst_text` longtext NOT NULL,
  PRIMARY KEY  (`pst_id`)
) TYPE=MyISAM AUTO_INCREMENT=0 ;");

spip_query("CREATE TABLE IF NOT EXISTS `cm_posts_done` (
  `pst_id` bigint(20) NOT NULL default '0',
  `sub_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`pst_id`,`sub_id`)
) TYPE=MyISAM;");

spip_query("CREATE TABLE IF NOT EXISTS `cm_posts_links` (
  `lnk_id` bigint(20) NOT NULL default '0',
  `pst_id` bigint(20) NOT NULL default '0',
  `lnk_name` varchar(255) NOT NULL default '',
  `lnk_url` text NOT NULL,
  PRIMARY KEY  (`lnk_id`)
) TYPE=MyISAM;");

spip_query("CREATE TABLE IF NOT EXISTS `cm_posts_queued` (
  `pst_id` bigint(20) NOT NULL default '0',
  `sub_id` bigint(20) NOT NULL default '0',
  `psq_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pst_id`,`sub_id`)
) TYPE=MyISAM;");

spip_query("CREATE TABLE IF NOT EXISTS `cm_settings` (
  `set_name` varchar(15) NOT NULL default '',
  `set_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`set_name`)
) TYPE=MyISAM COMMENT='Application settings';");

spip_query("CREATE TABLE IF NOT EXISTS `cm_subscribers` (
  `sub_id` bigint(20) NOT NULL auto_increment,
  `sub_email` varchar(255) NOT NULL default '',
  `sub_profile` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`sub_id`),
  KEY `sub_profile` (`sub_profile`),
  KEY `sub_email` (`sub_email`)
) TYPE=MyISAM AUTO_INCREMENT=0 ;");

spip_query("INSERT INTO `cm_settings` (`set_name`, `set_value`)
  VALUES
  ('CM_MAIL_FROM', '".$GLOBALS['meta']['email_webmaster']."'),
  ('CM_MAIL_ADMIN', '".$GLOBALS['meta']['email_webmaster']."'),
  ('CM_SEND_NUMBER', '50');");

}
?>