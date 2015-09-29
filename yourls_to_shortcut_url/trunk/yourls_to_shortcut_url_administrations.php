<?php

/**
 * Administrations pour yourls_to_shortcut_url
 *
 * @plugin     yourls_to_shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\yourls_to_shortcut_url\administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables yourls_to_shortcut_url
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function yourls_to_shortcut_url_upgrade($nom_meta_base_version,$version_cible){
	
	$maj = array();

	$maj['create'] = array(
		// Migration des données des URL de yourls vers shortcut_url
		array('translation_yourls_to_shortcut_url', ''));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/** 
 * Translation des champs de l'ancienne table yourls_url 
 * vers la table spip_shortcut_url
 *
 */
function translation_yourls_to_shortcut_url() {
	$yourls_urls = sql_allfetsel('*', 'yourls_url', '', '', '', '', '', 'fdip_yourls');
   
	foreach ($yourls_urls as $yourls_url) {
		$new_shorturl_urls = array();
		$new_shorturl_urls['titre'] = $yourls_url['keyword'];
		$new_shorturl_urls['url'] = $yourls_url['url'];

		include_spip('inc/distant');
		$recup = recuperer_page($yourls_url['url'], true);
		if (preg_match(',<title[^>]*>(.*),im', $recup, $regs))
			$new_shorturl_urls['description'] = filtrer_entites(supprimer_tags(trim(preg_replace(',</title>.*,i', '', $regs[1]))));

		$new_shorturl_urls['date_modif'] = $yourls_url['timestamp'];
		$new_shorturl_urls['ip_address'] = $yourls_url['ip'];
		$new_shorturl_urls['click'] = $yourls_url['clicks'];

		$id_shorturl = sql_insertq('spip_shortcut_urls', $new_shorturl_urls);
		spip_log($id_shorturl, 'test.' . _LOG_ERREUR);

		if(intval($id_shorturl)>0){
			$yourls_url_logs = sql_allfetsel('*','yourls_log','keyword ='.sql_quote($yourls_url['keyword']),'','','','','fdip_yourls');
			foreach ($yourls_url_logs as $yourls_url_log) {

				$new_shorturl_urls_logs = array('id_shortcut_url' => $id_shorturl);
				$new_shorturl_urls_logs['id_shortcut_urls_log'] = $yourls_url_log['click_id'];
				$new_shorturl_urls_logs['date_modif'] = $yourls_url_log['click_time'];
				$new_shorturl_urls_logs['shorturl'] = $yourls_url_log['shorturl'];
				$new_shorturl_urls_logs['referrer'] = $yourls_url_log['referrer'];
				$new_shorturl_urls_logs['user_agent'] = $yourls_url_log['user_agent'];
				$new_shorturl_urls_logs['ip_address'] = $yourls_url_log['ip_address'];
				$new_shorturl_urls_logs['country_code'] = $yourls_url_log['country_code'];

				$id_shorturl_logs = sql_insertq('spip_shortcut_urls_logs', $new_shorturl_urls_logs);
				spip_log($id_shorturl_logs, 'test.' . _LOG_ERREUR);

				if(intval($id_shorturl_logs)>0)
					sql_delete('yourls_log', 'click_id = ' . intval($yourls_url_log['click_id']),'fdip_yourls');

			}
			sql_delete('yourls_url', 'keyword = ' . sql_quote($yourls_url['keyword']),'fdip_yourls');
		}
		if (time() >= _TIME_OUT)
			return;
	}

	return false;
}

/**
 * Desinstallation/suppression des tables yourls_to_shortcut_url
 *
 * @param string $nom_meta_base_version
 */
function yourls_to_shortcut_url_vider_tables($nom_meta_base_version) {
	
	effacer_meta("yourls_to_shortcut_url");
	effacer_meta($nom_meta_base_version);
}

?>