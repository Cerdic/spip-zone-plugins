<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public


function formulaires_ajouter_noisette_charger_dist($page, $bloc, $id_noisette, $redirect = '') {

	$valeurs = array('editable' => false);

	// Détermination de l'identifiant de la page ou de l'objet concerné
	if (is_array($page)) {
		$identifiant['objet'] = $page['objet'];
		$identifiant['id_objet'] = $page['id_objet'];
	}
	else {
		$identifiant['page'] = $page;
	}

	if (autoriser('configurerpage', 'noizetier', 0, '', $identifiant)) {
		// On ajoute l'identifiant à la liste des valeurs ainsi que le bloc
		$valeurs = array_merge($valeurs, $identifiant);
		$valeurs['id_noisette'] = $id_noisette;
		$valeurs['bloc'] = $bloc;
		$valeurs['editable'] = true;
	}

	return $valeurs;
}


function formulaires_ajouter_noisette_traiter_dist($page, $bloc, $id_noisette, $redirect = '') {

	$retour = array();

	// Détermination de l'identifiant de la page ou de l'objet concerné et construction du conteneur de la
	// noisette
	$conteneur = array();
	if (is_array($page)) {
		$identifiant['objet'] = $page['objet'];
		$identifiant['id_objet'] = $page['id_objet'];
		$conteneur['squelette'] = "${bloc}";
		$conteneur = array_merge($conteneur, $identifiant);
	}
	else {
		$identifiant['page'] = $page;
		$conteneur['squelette'] = "${bloc}/${page}";
	}

	if (autoriser('configurerpage', 'noizetier', 0, '', $identifiant)) {
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
