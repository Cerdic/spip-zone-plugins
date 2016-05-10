<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('inc/actions');

function action_kml_infos_dist() {
	global $redirect;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,', $arg, $r)) {
		spip_log('action_kml_infos_dist incompris: ' . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	} else {
		action_kml_infos_post($r);
	}
}

function action_kml_infos_post($r) {
	list(, $sign, $id_objet, $objet, $id_document, $suite) = $r;

	if (intval($id_document)) {
		$recuperer_info = charger_fonction('kml_infos', 'inc');
		$infos = $recuperer_info($id_document);
		if ($infos) {
			include_spip('inc/documents');
			$fichier = sql_getfetsel('fichier', 'spip_documents', 'id_document='.intval($id_document));
			if (is_numeric($latitude = $infos['latitude']) and is_numeric($longitude = $infos['longitude'])) {
				$c = array(
					'titre' => $infos['titre'] ? $infos['titre'] : basename($fichier),
					'lat'=> $latitude,
					'lon' => $longitude,
					'zoom' => $config['zoom'] ? $config['zoom'] :'4'
				);

				include_spip('action/editer_gis');

				if ($id_gis = sql_getfetsel('G.id_gis', 'spip_gis AS G LEFT JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis', 'T.id_objet=' . intval($id_document) . " AND T.objet='document'")) {
					// Des coordonnées sont déjà définies pour ce document => on les update
					revisions_gis($id_gis, $c);
					spip_log("GIS EXIFS : Update des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis", 'gis');
				} else {
					// Aucune coordonnée n'est définie pour ce document  => on les crées
					$id_gis = insert_gis();
					revisions_gis($id_gis, $c);
					lier_gis($id_gis, 'document', $id_document);
					spip_log("GIS EXIFS : Création des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis", 'gis');
				}
			}
			unset($infos['longitude']);
			unset($infos['latitude']);
			if (count($infos) > 0) {
				include_spip('action/editer_document');
				document_modifier($id_document, $infos);
			}
		}
	}
	$redirect = urldecode(_request('redirect'));
	return $redirect;
}
