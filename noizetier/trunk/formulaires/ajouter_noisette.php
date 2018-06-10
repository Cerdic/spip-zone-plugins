<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public


/**
 * Formulaire listant les noisettes incluses dans un conteneur de type (page, bloc), (objet, bloc) ou noisette conteneur.
 * La fonction charger déclare les champs postés et y intègre les valeurs par défaut.
 *
 * @param array|string $page_ou_objet
 *        Page au sens SPIP ou objet spécifiquement identifié.
 *        - dans le cas d'une page SPIP comme sommaire, l'argument est une chaîne.
 *        - dans le cas d'un objet SPIP comme un article d'id x, l'argument est un tableau associatif à deux index,
 *          `objet` et `id_objet`.
 * @param string       $bloc
 * 		  Bloc de page au sens Z.
 * @param array        $noisette
 *        Tableau descriptif d'une noisette contenant à minima son type et son id.
 * @param string $redirect
 * 		URL de redirection. La valeur dépend du type d'édition.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_ajouter_noisette_charger_dist($page_ou_objet, $bloc, $noisette = array(), $redirect = '') {

	// Ajout à la liste des valeurs l'identifiant de la page ou de l'objet concerné
	$valeurs = is_array($page_ou_objet) ? $page_ou_objet : array('page' => $page_ou_objet);

	if (autoriser('configurerpage', 'noizetier', 0, '', $valeurs)) {
		// On ajoute à la liste des valeurs :
		// - le bloc
		// - la noisette conteneur si elle existe
		$valeurs = array_merge($valeurs, $noisette);
		$valeurs['bloc'] = $bloc;

		// Ajout de l'identifiant du conteneur
		include_spip('inc/noizetier_conteneur');
		$valeurs['id_conteneur'] = noizetier_conteneur_composer($page_ou_objet, $bloc, $noisette);

		$valeurs['editable'] = true;
	} else {
		$valeurs = array('editable' => false);
	}

	return $valeurs;
}

function formulaires_ajouter_noisette_verifier_dist($page_ou_objet, $bloc, $noisette = array(), $redirect = '') {

	$erreurs = array();
	if (!_request('type_noisette')) {
		$erreurs['type_noisette'] = _T('noizetier:erreur_aucune_noisette_selectionnee');
	}

	return $erreurs;
}


function formulaires_ajouter_noisette_traiter_dist($page_ou_objet, $bloc, $noisette = array(), $redirect = '') {

	$retour = array();

	// Récupération de l'identifiant du conteneur dans lequel ajouter les noisettes.
	$id_conteneur = _request('conteneur_id');

	// Décomposition du conteneur en tableau associatif.
	include_spip('inc/noizetier_conteneur');
	$conteneur = noizetier_conteneur_decomposer($id_conteneur);

	if (autoriser('configurerpage', 'noizetier', 0, '', $conteneur)) {
		if ($type_noisette = _request('type_noisette')) {
			include_spip('inc/ncore_noisette');
			if (!is_array($type_noisette)) {
				$type_noisette = array($type_noisette);
			}

			// On insère chaque noisette sélectionnée en fin de liste dans l'ordre retourné par le formulaire.
			$erreurs = array();
			foreach ($type_noisette as $_type_noisette) {
				if (!$id_noisette = noisette_ajouter('noizetier', $_type_noisette, $conteneur)) {
					$erreurs[] = $_type_noisette;
				}
			}

			// Ajout de la noisette en fin de liste pour le squelette concerné.
			if (!$erreurs) {
				$retour['message_ok'] = _T('info_modification_enregistree');
				if (count($type_noisette) == 1) {
					// On a rajouté une seule noisette, on peut se rendre dans la page d'édition de la noisette
					// pour finir la configuration.
					$redirect = parametre_url(generer_url_ecrire('noisette_edit'), 'id_noisette', $id_noisette);
				}
				$retour['redirect'] = $redirect;
			} else {
				$retour['message_erreur'] =
					_T('noizetier:erreur_ajout_noisette', array('noisettes', implode(', ', $erreurs)));
			}
		} else {
			$retour['message_erreur'] = _T('noizetier:erreur_aucune_noisette_selectionnee');
		}
	} else {
		$retour['message_erreur'] = _T('noizetier:probleme_droits');
	}

	return $retour;
}
