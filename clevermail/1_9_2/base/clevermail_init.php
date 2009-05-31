<?php
function clevermail_install($action) {
	switch ($action) {
		case 'test':
			return (isset($GLOBALS['meta']['clevermail_base_version']));
			break;
		case 'install':
			include_spip('base/create');
			include_spip('base/clevermail');
			creer_base();
			spip_query("INSERT INTO `cm_settings` (`set_name`, `set_value`)
			  VALUES
			  ('CM_MAIL_FROM', '".$GLOBALS['meta']['email_webmaster']."'),
			  ('CM_MAIL_ADMIN', '".$GLOBALS['meta']['email_webmaster']."'),
			  ('CM_SEND_NUMBER', '50');");
			ecrire_meta('clevermail_base_version','0.1');
			ecrire_metas();
			break;
		case 'uninstall':
			spip_query("DROP TABLE cm_lists");
			spip_query("DROP TABLE cm_lists_subscribers");
			spip_query("DROP TABLE cm_pending");
			spip_query("DROP TABLE cm_posts");
			spip_query("DROP TABLE cm_posts_done");
			spip_query("DROP TABLE cm_posts_links");
			spip_query("DROP TABLE cm_posts_queued");
			spip_query("DROP TABLE cm_settings");
			spip_query("DROP TABLE cm_subscribers");
			effacer_meta('clevermail_base_version');
			ecrire_metas();
			break;
	}
}
?>