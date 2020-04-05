<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('base/abstract_sql');
include_spip('inc/session');
include_spip('inc/autoriser');

function formulaires_projets_rapide_charger_dist()
{
	$valeurs = array();
	$valeurs['projets'] = (_request('projets')) ? _request('projets') : '';

	return $valeurs;
}

function formulaires_projets_rapide_verifier_dist()
{
	$erreurs = array();
	$obligatoires = array('projets');
	foreach ($obligatoires as $obligatoire) {
		if (!_request($obligatoire)) {
            $erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}
	if (_request('projets')) {
		$urls_projets = explode("\n", _request('projets'));

		foreach ($urls_projets as $key => $value) {
			$sans_slash = preg_replace("/\/$/", '', trim($value));
			if (sql_countsel('spip_projets', 'url_site=' . sql_quote(trim($value))) > 0) {
				unset($urls_projets[$key]);
			} elseif (sql_countsel('spip_projets', "url_site LIKE '" . trim($sans_slash) . "%'") > 0) {
				unset($urls_projets[$key]);
			} else {
				$urls_projets[$key] = trim($value);
			}
		}
		set_request('projets', implode("\n", $urls_projets));
	}
	return $erreurs;
}

function formulaires_projets_rapide_traiter_dist()
{
	$res = array();
	$urls_projets = _request('projets');
	foreach (explode("\n", $urls_projets) as $key => $value) {
		$date_today = date('Y-m-d h:i:s', strtotime("now"));
		preg_match('/\<title\>(.*)\<\/title\>/', file_get_contents(trim($value)), $title);
		$id_projet = sql_insertq('spip_projets', array('nom' => $title[1], 'url_site' => trim($value), 'date_publication' => $date_today, 'date_debut' => $date_today));
		$id_projets_site = sql_insertq('spip_projets_sites', array('titre' => $title[1], 'fo_url' => trim($value), 'date_creation' => $date_today));
		$lien = sql_insertq('spip_projets_sites_liens', array('id_projets_site' => $id_projets_site, 'id_objet' => $id_projet, 'objet' => 'projet'));
	}
	return array(
		'message_ok' =>  _T('info_modification_enregistree'),
		'redirect' => generer_url_public('projets')
	);
}

?>