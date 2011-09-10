<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
function formulaires_editer_asso_meta_utilisateur_charger_dist($nom_meta='') {
	return array('nom_meta' => str_replace('_', ' ', str_replace('meta_utilisateur_', '', $nom_meta)));
}
function formulaires_editer_asso_meta_utilisateur_verifier_dist($nom_meta) {
	$erreurs = array();
	$nom_meta = _request('nom_meta');
	
	/* verifier que le nom de la meta ne contient rien d'autre que des caracteres A-za-z0-9 */
	if (preg_match('/[^A-Za-z0-9 ]+/', $nom_meta)) {
		$erreurs['nom_meta'] = _T('asso:erreur_nom_meta_utilisateur_incorrect');
	}
	
	/* on verifie que le nom de la meta ne depasse pas 237 catacteres(il faut laisser de la place pour le prefixe et ne pas depasser 255) */
	if (strlen($nom_meta)>237) {
		$erreurs['nom_meta'] = _T('asso:erreur_nom_meta_utilisateur_trop_long');
	}

	if ($nom_meta=='') {
		$erreurs['nom_meta'] = _T('asso:erreur_pas_de_nom_meta_utilisateur');
	}

	if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	
	return $erreurs;
}

function formulaires_editer_asso_meta_utilisateur_traiter($nom_meta='') {
	$nouveau_nom_meta = "meta_utilisateur_".str_replace(' ', '_', _request('nom_meta'));
	if ($nom_meta!=$nouveau_nom_meta && $nom_meta!='') { /* un changement de nom de meta, on recupere la valeur de la meta, cree la nouvelle puis on la supprime */
		$valeur_meta = $GLOBALS['association_metas'][$nom_meta];
		ecrire_meta($nouveau_nom_meta, $valeur_meta, 'oui', 'association_metas');
		effacer_meta($nom_meta, 'association_metas');
		
	} else { /* creation d'une nouvelle meta */
		ecrire_meta($nouveau_nom_meta, '', 'oui', 'association_metas');
	}

	$res = array();
	$res['message_ok'] = ''; 
	$res['redirect'] = generer_url_ecrire('editer_asso_metas_utilisateur');
	return $res;
}
?>
