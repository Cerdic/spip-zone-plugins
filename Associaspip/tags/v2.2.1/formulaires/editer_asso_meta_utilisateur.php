<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_asso_meta_utilisateur_charger_dist($nom_meta='') {
	return array('nom_meta' => str_replace('_', ' ', str_replace('meta_utilisateur_', '', $nom_meta)));
}

function formulaires_editer_asso_meta_utilisateur_verifier_dist($nom_meta='') {
	$erreurs = array();

	$nom_meta_form = _request('nom_meta');
	spip_log("nom meta $nom_meta form $nom_meta_form",'associaspip');
	if (preg_match('/[^A-Za-z0-9 ]+/', $nom_meta_form)) { // verifier que le nom de la meta ne contient rien d'autre que des lettres non accentues et des chiffres
		$erreurs['nom_meta'] = _T('asso:erreur_nom_meta_utilisateur_incorrect');
	} else { // on verifie que le nom de la meta ne depasse pas 237 catacteres(il faut laisser de la place pour le prefixe et ne pas depasser 255)
		if (strlen($nom_meta_form)>237) {
			$erreurs['nom_meta'] = _T('asso:erreur_nom_meta_utilisateur_trop_long');
		} else {
			if ($nom_meta_form=='') { // pas de noms nuls
				$erreurs['nom_meta'] = _T('asso:erreur_pas_de_nom_meta_utilisateur');
			} else {
				$nom_meta_form = 'meta_utilisateur_'.str_replace(' ', '_', strtolower($nom_meta_form));
				$meta_lower_cased = array_change_key_case($GLOBALS['association_metas']);
				if ((strtolower($nom_meta)!=$nom_meta_form) && isset($meta_lower_cased[$nom_meta_form])) { // on verifie qu'on n'ecrase pas une autre meta_utilisateur
					$erreurs['nom_meta'] = _T('asso:erreur_meta_utilisateur_deja_definie');
				}
			}
		}
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_meta_utilisateur_traiter($nom_meta='') {
	$nouveau_nom_meta = 'meta_utilisateur_'.str_replace(' ', '_', _request('nom_meta'));
	if ($nom_meta!=$nouveau_nom_meta) { // si on a change vraiment quelque chose
		if ($nom_meta!='') { // un changement de nom de meta, on recupere la valeur de la meta, cree la nouvelle puis on la supprime
			$valeur_meta = $GLOBALS['association_metas'][$nom_meta];
			effacer_meta($nom_meta, 'association_metas');
			ecrire_meta($nouveau_nom_meta, $valeur_meta, 'oui', 'association_metas');
		} else { // creation d'une nouvelle meta
			ecrire_meta($nouveau_nom_meta, '', 'oui', 'association_metas');
		}
	}

	$res = array();
	$res['message_ok'] = '';
	$res['redirect'] = generer_url_ecrire('editer_asso_metas_utilisateur');
	return $res;
}

?>