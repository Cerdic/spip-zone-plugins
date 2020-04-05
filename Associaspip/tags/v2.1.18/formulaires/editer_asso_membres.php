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
function formulaires_editer_asso_membres_charger_dist($id_auteur) {
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_membres associes a l'id_auteur passe en param */
	$contexte = formulaires_editer_objet_charger('asso_membres', $id_auteur, '', '',  generer_url_ecrire('adherents'), '');

	/* on a ajoute dans le contexte les metas de gestion optionnelle des champs Civilite, Prenom et Ref. Interne */
	$contexte['meta_civilite'] = $GLOBALS['association_metas']['civilite'];
	$contexte['meta_prenom'] = $GLOBALS['association_metas']['prenom'];
	$contexte['meta_id_asso'] = $GLOBALS['association_metas']['id_asso'];

	return $contexte;
}

function formulaires_editer_asso_membres_verifier_dist($id_auteur) {
	$erreurs = array();

	/* verifier la validite de la date de validite */
	if ($erreur_validite = association_verifier_date(_request('validite'))) {
		$erreurs['validite'] = _request('validite')."&nbsp;:&nbsp;".$erreur_validite;
	}

	if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}

	return $erreurs;
}

function formulaires_editer_asso_membres_traiter($id_auteur) {
	return formulaires_editer_objet_traiter('asso_membres', $id_auteur, '', '',  generer_url_ecrire('adherents'), '');
}
?>
