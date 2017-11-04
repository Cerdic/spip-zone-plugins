<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_lister_noisettes_page_charger_dist($page, $bloc) {

	// Si on est en présence d'une page, il faut convertir l'identifiant en tableau.
	// Sinon, on est en présence d'un objet précis connu par son type et son id fourni dans un
	// tableau.
	$valeurs = is_array($page) ? $page : array('page' => $page);

	// Ajout du bloc recevant les noisettes
	$valeurs['bloc'] = $bloc;
	
	return $valeurs;
}


function formulaires_lister_noisettes_page_traiter_dist($page, $bloc) {

	$retour = array();

	$ordre = _request('ordre');
	$nb_noisettes = intval(_request('nb_noisettes'));

	include_spip('noizetier_fonctions');
	if (count($ordre) > $nb_noisettes) {
		// On vient d'ajouter par glisser-déposer une nouvelle noisette, on la rajoute d'abord en fin
		// de liste avant d'appeler la fonction de rangement pour les noisettes qui suivent.
		// -- Identifier la noisette qui vient d'être glissée dans le bloc et retenir son rang : c'est la seule
		//    valeur de type chaine qui n'est pas un id de noisette.
		$index = array_search(0, array_map('intval', $ordre));
		$noisette = $ordre[$index];
		$rang = $index + 1;
		if ($id_noisette = noizetier_noisette_ajouter($noisette, $page, $bloc, $rang)) {
			// On met à jour le tableau donnant l'ordre des noisettes avec l'id de la noisette
			// et on demande le rangement des noisettes qui suivent la noisette ajoutée.
			$ordre[$index] = "${id_noisette}";
			if (noizetier_noisette_ordonner($ordre, $index + 1)) {
				$retour['message_ok'] = _T('info_modification_enregistree');
			} else {
				$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
			}
		} else {
			$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
		}
	} else {
		// On vient juste de changer l'ordre des noisettes, on réordonne toute la liste.
		if (noizetier_noisette_ordonner($ordre)) {
			$retour['message_ok'] = _T('info_modification_enregistree');
		} else {
			$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
		}
	}

	return $retour;
}
