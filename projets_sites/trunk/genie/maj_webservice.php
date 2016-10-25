<?php
/**
 * Mettre à jour les informations d'un site par son webservice
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Genie
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_maj_webservice_dist($t) {
	include_spip('base/abstract_sql');
	include_spip('inc/utils');
	$analyser_webservice = charger_fonction('analyser_webservice', 'inc');

	$webservices = sql_allfetsel('webservice,id_projets_site,fo_login,fo_password', 'spip_projets_sites',
		"webservice!=''");

	if (count($webservices) > 0) {
		foreach ($webservices as $key_webservice => $value_webservice) {
			$champs = $analyser_webservice($value_webservice['webservice'], $value_webservice['fo_login'],
				$value_webservice['fo_password']);
			// var_dump($champs);
			if ($champs and count($champs) > 0) {
				sql_updateq('spip_projets_sites', $champs, 'id_projets_site=' . $value_webservice['id_projets_site']);
				spip_log(_T('projets_site:maj_webservice_log_ok', array(
					'id' => $value_webservice['id_projets_site'],
					'webservice' => $value_webservice['webservice'],
				)), 'projets_sites');
			} else {
				spip_log(_T('projets_site:maj_webservice_log_ko', array(
					'id' => $value_webservice['id_projets_site'],
					'webservice' => $value_webservice['webservice'],
				)), 'projets_sites');
			}
		}
	}

	return $t;
}

