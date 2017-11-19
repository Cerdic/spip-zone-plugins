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

	// Récupération des inputs
	$ordre = _request('ordre');
	$nb_noisettes = intval(_request('nb_noisettes'));

	// Détermination de l'identifiant de la page ou de l'objet concerné et construction du conteneur de la
	// noisette
	$conteneur = array();
	if (is_array($page)) {
		$identifiant['objet'] = $page['objet'];
		$identifiant['id_objet'] = $page['id_objet'];
		// Pour le squelette on ne retient que le bloc car il est inutile de répéter le type d'objet comme nom de page.
		// Cette information est de toute façon sans intérêt pour un objet, l'objectif de la structure du conteneur
		// est juste de permette le calcul de l'id unique du dit conteneur.
		$conteneur['squelette'] = "${bloc}";
		$conteneur = array_merge($conteneur, $identifiant);
	}
	else {
		$identifiant['page'] = $page;
		$conteneur['squelette'] = "${bloc}/${page}";
	}

	if (autoriser('configurerpage', 'noizetier', 0, '', $identifiant)) {
		if (count($ordre) > $nb_noisettes) {
			// On vient d'ajouter par glisser-déposer une nouvelle noisette, on la rajoute au rang choisi.
			// -- Identifier la noisette qui vient d'être glissée dans le bloc et retenir son rang : c'est la seule
			//    valeur de type chaine qui n'est pas un id de noisette.
			$index = array_search(0, array_map('intval', $ordre));
			$type_noisette = $ordre[$index];
			$rang = $index + 1;

			include_spip('inc/ncore_noisette');
			if ($id_noisette = noisette_ajouter('noizetier', $type_noisette, $conteneur, $rang)) {
				$retour['message_ok'] = _T('info_modification_enregistree');
			} else {
				$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
			}
		} else {
			// On vient juste de changer l'ordre des noisettes, on réordonne toute la liste.
			include_spip('noizetier_fonctions');
			if (noizetier_noisette_ordonner($ordre)) {
				$retour['message_ok'] = _T('info_modification_enregistree');
			} else {
				$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
			}
		}
	} else {
		$retour['message_erreur'] = _T('noizetier:probleme_droits');
	}

	return $retour;
}
