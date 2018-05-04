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
			// Ajout de la noisette en fin de liste pour le squelette concerné.
			if ($id_noisette = noisette_ajouter('noizetier', $type_noisette, $conteneur)) {
				$retour['message_ok'] = _T('info_modification_enregistree');
				if ($redirect) {
					// Note : $redirect indique la page à charger en cas d'ajout
					//        @id_noisette@ étant alors remplacé par la bonne valeur, connue seulement après ajout de la noisette
					// TODO : Grrr, y a surement plus propre => à trouver
					$redirect = str_replace('&amp;', '&', $redirect);
					$redirect = str_replace('@id_noisette@', $id_noisette, $redirect);
					if (strncmp($redirect, 'javascript:', 11) == 0) {
						$retour['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($redirect, 11).'/*]]>*/</script>';
						$retour['editable'] = true;
					} else {
						$retour['redirect'] = $redirect;
					}
				}
			} else {
				$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
			}
		} else {
			$retour['message_erreur'] = _T('noizetier:erreur_aucune_noisette_selectionnee');
		}
	} else {
		$retour['message_erreur'] = _T('noizetier:probleme_droits');
	}

	return $retour;
}
