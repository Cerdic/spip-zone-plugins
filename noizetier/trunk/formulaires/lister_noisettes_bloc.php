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

	// Récupération des inputs
	$ordre = _request('ordre');
	$nb_noisettes = intval(_request('nb_noisettes'));

	// Détermination de l'identifiant de la page ou de l'objet concerné et construction du conteneur de la
	// noisette
	$conteneur = array();
	if (is_array($page_ou_objet)) {
		$identifiant['objet'] = $page_ou_objet['objet'];
		$identifiant['id_objet'] = $page_ou_objet['id_objet'];
		// Pour le squelette on ne retient que le bloc car il est inutile de répéter le type d'objet comme nom de page.
		// Cette information est de toute façon sans intérêt pour un objet, l'objectif de la structure du conteneur
		// est juste de permette le calcul de l'id unique du dit conteneur.
		$conteneur['squelette'] = "${bloc}";
		$conteneur = array_merge($conteneur, $identifiant);
	}
	else {
		$identifiant['page'] = $page_ou_objet;
		$conteneur['squelette'] = "${bloc}/${$page_ou_objet}";
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
