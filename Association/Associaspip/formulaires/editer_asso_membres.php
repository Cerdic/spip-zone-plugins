<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_asso_membres_charger_dist($id_auteur) {
	// cet appel va charger dans $contexte tous les champs de la table spip_asso_membres associes a l'id_auteur passe en param
	$contexte = formulaires_editer_objet_charger('asso_membres', $id_auteur, '', '',  generer_url_ecrire('adherents'), '');
	// on verifie que la date de validite n'est pas nulle et si oui on la met a hier
	list($annee, $mois, $jour) = explode('-',$contexte['validite']);
	if ($jour==0 OR $mois==0 OR $annee==0)
		$contexte['validite'] = date('Y-m-d', mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	return $contexte;
}

function formulaires_editer_asso_membres_verifier_dist($id_auteur) {
	$erreurs = array();

	// verifier la validite de la date de validite
	if ($erreur = association_verifier_date('validite'))
		$erreurs['validite'] = $erreur;

	if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_membres_traiter($id_auteur) {
	// traitement des appartenance a un groupe
	$action_membre_groupes = charger_fonction('editer_membre_groupes','action');
	$action_membre_groupes($id_auteur);
	// ajout a un groupe
	$action_ajouter_groupes = charger_fonction('ajouter_membre_groupes', 'action');
	$action_ajouter_groupes($id_auteur);
	// traitement des informations du membre
	return formulaires_editer_objet_traiter('asso_membres', $id_auteur, '', '',  generer_url_ecrire('adherents'), '');
}

?>