<?php

/**
 * Fonctions pour shortcut_url.
 *
 * @plugin     shortcut_url
 *
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Générer la chaine de caractères.
 *
 * @param string $length taille de la chaîne de caractère
 *
 * @return string
 */
function generer_chaine($length) {
	return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

/**
 * Créer les titres pour les liens raccourcis
 * On doit retrouver une valeur numérique dans le titre pour eviter les gros mots.
 *
 * @param string $length taille de la chaîne de caractère
 *
 * @return string
 */
function generer_chaine_aleatoire($length = 5) {
	$pass = generer_chaine($length);
	do {
		$pass = generer_chaine($length);
	} while (!preg_match('/(?=.*\d)/', $pass));

	return $pass;
}

/**
 * Stocker les visites des clicks en séparant les humains des bots.
 *
 * @param int $id_shortcut_url id de l'URL raccourcis
 *
 * @return bool
 */
function shortcut_compteur($id_shortcut_url) {
	if (!defined('_IS_BOT')) {
		define(
			'_IS_BOT',
			isset($_SERVER['HTTP_USER_AGENT'])
			and preg_match(
				// mots generiques
				',bot|slurp|crawler|spider|webvac|yandex|'
				// MSIE 6.0 est un botnet 99,9% du temps, on traite donc ce USER_AGENT comme un bot
				.'MSIE 6\.0|'
				// UA plus cibles
				.'80legs|accoona|AltaVista|ASPSeek|Baidu|Charlotte|EC2LinkFinder|eStyle|Google|Genieo|INA dlweb|InfegyAtlas|Java VM|LiteFinder|Lycos|Rambler|Scooter|ScrubbyBloglines|Yahoo|Yeti'
				.',i',
				(string) $_SERVER['HTTP_USER_AGENT']
			)
		);
	}

	$ip_address = $GLOBALS['ip'];
	if (_IS_BOT) {
		$appele = sql_getfetsel('id_shortcut_urls_bot', 'spip_shortcut_urls_bots', 'ip_address = '.sql_quote($ip_address).' AND id_shortcut_url = '.intval($id_shortcut_url).' AND date_modif > '.sql_quote(date('Y-m-d H:i:s', strtotime('-30 seconds'))));
	} else {
		$appele = sql_getfetsel('id_shortcut_urls_log', 'spip_shortcut_urls_logs', 'ip_address = '.sql_quote($ip_address).' AND id_shortcut_url = '.intval($id_shortcut_url).' AND date_modif > '.sql_quote(date('Y-m-d H:i:s', strtotime('-30 seconds'))));
	}
	if (!$appele) {
		$date = sql_getfetsel('date_modif', 'spip_shortcut_urls_logs', 'ip_address = '.sql_quote($ip_address).' AND id_shortcut_url = '.intval($id_shortcut_url).' AND date_modif < '.sql_quote(date('Y-m-d H:i:s', strtotime('-30 seconds'))), '', 'date_modif DESC');
		$shorturl = sql_fetsel('url, click', 'spip_shortcut_urls', 'id_shortcut_url='.intval($id_shortcut_url));

		$date_modif = date('Y-m-d H:i:s');
		$referrer = $_SERVER['REMOTE_ADDR'];
		$user_agent = get_user_agent();

		$country_code = get_geoip($referrer);
		$click = $shorturl['click'] + 1;
		sql_updateq('spip_shortcut_urls', array('click' => $click), 'id_shortcut_url='.intval($id_shortcut_url));

		if (_IS_BOT) {
			$humain = 'bot';
			sql_insertq('spip_shortcut_urls_bots', array('id_shortcut_url' => $id_shortcut_url, 'date_modif' => $date_modif, 'referrer' => $referrer, 'user_agent' => $user_agent, 'ip_address' => $ip_address));
		} else {
			$humain = 'oui';
			sql_insertq('spip_shortcut_urls_logs', array('id_shortcut_url' => $id_shortcut_url, 'date_modif' => $date_modif, 'shorturl' => $shorturl['url'], 'referrer' => $referrer, 'user_agent' => $user_agent, 'ip_address' => $ip_address, 'country_code' => $country_code, 'humain' => $humain));
		}
	}

	return false;
}

/**
 * Récupérer le user agent de l'utilisateur.
 *
 * @return string
 */
function get_user_agent() {
	if (!isset($_SERVER['HTTP_USER_AGENT'])) {
		return '-';
	}

	$ua = strip_tags(html_entity_decode($_SERVER['HTTP_USER_AGENT']));
	$ua = preg_replace('![^0-9a-zA-Z\':., /{}\(\)\[\]\+@&\!\?;_\-=~\*\#]!', '', $ua);

	return $ua;
}

/**
 * Récupérer le le code pays de l'utilisateur.
 *
 * @param int $ip ip de l'utilisateur
 *
 * @return string
 */
function get_geoip($ip = null) {
	static $gi = null;
	if (is_null($ip)) {
		include_spip('lib/geoip/geoip');
		geoip_close($gi);
		return;
	}
	if (is_null($gi)) {
		include_spip('lib/geoip/geoip');
		$gi = geoip_open(find_in_path('lib/geoip/GeoIP.dat'), GEOIP_STANDARD);
	}

	return geoip_country_code_by_addr($gi, $ip);
}
