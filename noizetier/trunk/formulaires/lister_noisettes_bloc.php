<?php
/**
 * Ce fichier contient le formulaire listant les noisettes incluses dans un conteneur de type (page, bloc)
 * ou (objet, bloc).
 * Ce formulaire autorise le déplacement de noisette ou l'ajout de noisette dans le conteneur par simple
 * glisser-déposer
 *
 * @package SPIP\NOIZETIER\NOISETTE\FORMULAIRE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 *
 * La fonction charger déclare les champs postés et y intègre les valeurs par défaut.
 *
 * @param array|string $page_ou_objet
 * 		Page au sens SPIP ou objet spécifiquement identifié.
 *      - dans le cas d'une page SPIP comme sommaire, l'argument est une chaîne.
 * 		- dans le cas d'un objet SPIP comme un article d'id x, l'argument est un tableau associatif à deux index,
 *        `objet` et `id_objet`.
 * @param string       $bloc
 * 		Bloc de page au sens Z.
 * @param int          $id_noisette
 * 		Identifiant de la noisette de type conteneur dans laquelle inclure une noisette.
 * @param string $redirect
 * 		URL de redirection. La valeur dépend du type d'édition.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_lister_noisettes_bloc_charger_dist($page_ou_objet, $bloc) {

	// Si on est en présence d'une page au sens SPIP, il faut convertir l'identifiant en tableau.
	// Sinon, on est en présence d'un objet précis connu par son type et son id fourni dans un
	// tableau.
	$valeurs = is_array($page_ou_objet) ? $page_ou_objet : array('page' => $page_ou_objet);

	// Ajout du bloc recevant les noisettes
	$valeurs['bloc'] = $bloc;

	// Ajout de l'identifiant du conteneur qui servira à la boucle des noisettes
	include_spip('inc/noizetier_conteneur');
	$valeurs['id_conteneur'] = noizetier_conteneur_composer($page_ou_objet, $bloc);
	
	return $valeurs;
}


/**
 * @param array|string $page_ou_objet
 * 		Page au sens SPIP ou objet spécifiquement identifié.
 *      - dans le cas d'une page SPIP comme sommaire, l'argument est une chaîne.
 * 		- dans le cas d'un objet SPIP comme un article d'id x, l'argument est un tableau associatif à deux index,
 *        `objet` et `id_objet`.
 * @param string       $bloc
 * 		Bloc de page au sens Z.
 * @param int          $id_noisette
 * 		Identifiant de la noisette de type conteneur dans laquelle inclure une noisette.
 * @param string $redirect
 * 		URL de redirection. La valeur dépend du type d'édition.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_lister_noisettes_bloc_traiter_dist($page_ou_objet, $bloc) {

	$retour = array();

	// Récupération de l'identifiant du conteneur constitué par le bloc en cours de configuration
	// et conversion en tableau associatif.
	include_spip('inc/noizetier_conteneur');
	$id_conteneur = _request('conteneur_id');
	$conteneur = noizetier_conteneur_decomposer($id_conteneur);

	if (autoriser('configurerpage', 'noizetier', 0, '', $conteneur)) {
		// Récupération de l'ordre des noisettes, des id de conteneur, des rangs de chaque noisette
		// et du nombre total de noisettes dans le bloc.
		$ordre = _request('ordre');
		$nb_noisettes = intval(_request('nb_noisettes'));

		if (count($ordre) > $nb_noisettes) {
			// On vient d'ajouter par glisser-déposer une nouvelle noisette, on la rajoute au rang choisi.
			// -- Identifier la noisette qui vient d'être glissée dans le bloc et retenir son rang : c'est la seule
			//    valeur de type chaine qui n'est pas un id de noisette.
			$index = array_search(0, array_map('intval', $ordre));
			$type_noisette = $ordre[$index];
			$rang = $index + 1;

			include_spip('inc/ncore_noisette');
//			if ($id_noisette = noisette_ajouter('noizetier', $type_noisette, $conteneur, $rang)) {
//				$retour['message_ok'] = _T('info_modification_enregistree');
//			} else {
//				$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
//			}
		} else {
			// On vient juste de changer l'ordre des noisettes, on réordonne toute la liste.
			include_spip('inc/noizetier_noisette');
//			if (noizetier_noisette_ordonner($ordre)) {
//				$retour['message_ok'] = _T('info_modification_enregistree');
//			} else {
//				$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
//			}
		}
	} else {
		$retour['message_erreur'] = _T('noizetier:probleme_droits');
	}

	return $retour;
}
