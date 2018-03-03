<?php

/**
 * Chargement des valeurs
 * @return array
 */

function formulaires_migrer_logos_roles_traiter_dist() {

	// on récupère la liste des objets pour lesquels il faut migrer les logos
	$liste_objets_a_traiter = array_filter(_request('liste_objet'));


	// Si laliste n'est pas vide, alors on traite...
	if (!empty($liste_objets_a_traiter)) {
		include_spip('action/editer_logo');
		include_spip('inc/chercher_logo');
		include_spip('action/ajouter_documents');
		$ajouter_un_document = charger_fonction('ajouter_un_document', 'action');

		foreach ($liste_objets_a_traiter as $objet) {
			$les_objets = null;
			$nom_objet = objet_type($objet);
			$colonne_id = id_table_objet($nom_objet);
			// On va chercher tout les ID pour l'objet pour vérifier la présence
			// de logo standard spip...
			$les_objets = sql_allfetsel($colonne_id, $objet);

			foreach ($les_objets as $objet_a_traiter) {
				$id_objet = $objet_a_traiter[$colonne_id];
				// la fonction rechercher_logo_original est reprise de action
				// editer_logo de la distribution pour n'aller chercher que les logo
				// "nommé" dans IMG. Ca fait aussi en sorte que si on a beaucoup
				// d'élément, c'est toujours juste les "vieux logos" qui seront
				// importé... Une fois traité, ils sont supprimé.... donc on répète
				// tant qu'on veut l'opération....
				if ($logo_actuel = rechercher_logo_original($id_objet, $colonne_id)) {
					$chemin_logo = array(
						'tmp_name' => $logo_actuel[0],
						'name' => $logo_actuel[2].'.'.$logo_actuel[3],
					);

					$id_document = $ajouter_un_document('new', $chemin_logo, null, null, 'image');
					if (is_string($id_document)) {
						return $erreur = $id_document;
					}

					if ($resultat =  logo_modifier_document($objet, $id_objet, 'logo', $id_document)) {
						spip_log($resultat, 'logos_roles.' . _LOG_ERREUR);
					} else {
						spip_unlink($logo_actuel[0]);
					}
				}

				// On refait le même traitement pour les logo de survol...
				if ($logo_actuel = rechercher_logo_original($id_objet, $colonne_id, 'off')) {
					$chemin_logo = array(
						'tmp_name' => $logo_actuel[0],
						'name' => $logo_actuel[2].'.'.$logo_actuel[3],
					);

					$id_document = $ajouter_un_document('new', $chemin_logo, null, null, 'image');
					if (is_string($id_document)) {
						return $erreur = $id_document;
					}

					if ($resultat =  logo_modifier_document($objet, $id_objet, 'logo_survol', $id_document)) {
						spip_log($resultat, 'logos_roles.' . _LOG_ERREUR);
					} else {
						spip_unlink($logo_actuel[0]);
					}
				}
			}
		}
	}
}

function rechercher_logo_original($id, $_id_objet, $mode = 'on') {
	# attention au cas $id = '0' pour LOGO_SITE_SPIP : utiliser intval()

	$type = type_du_logo($_id_objet);
	$nom = $type . $mode . intval($id);

	foreach ($GLOBALS['formats_logos'] as $format) {
		if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format))) {
			return array($d, _DIR_LOGOS, $nom, $format, @filemtime($d));
		}
	}

	# coherence de type pour servir comme filtre (formulaire_login)
	return array();
}
