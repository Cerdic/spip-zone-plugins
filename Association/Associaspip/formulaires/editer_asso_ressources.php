<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');

/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
function formulaires_editer_asso_ressources_charger_dist($id_ressource='') {
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_dons associes a l'id_don passe en param */
	$contexte = formulaires_editer_objet_charger('asso_ressources', $id_ressource, '', '',  generer_url_ecrire('ressources'), '');

	/* si c'est une nouvelle operation, on charge la date d'aujourd'hui */
	if (!$id_ressource) {
		$contexte['date_acquisition'] = date('Y-m-d');
	}

	/* paufiner la presentation des valeurs  */
	if ($contexte['pu'])
		$contexte['pu'] = association_nbrefr($contexte['pu']);

	return $contexte;
}

function formulaires_editer_asso_ressources_verifier_dist($id_ressource='') {
	$erreurs = array();
	/* on verifie que prix de location ne soit pas negatifs */
	if ($erreur = association_verifier_montant(_request('pu')) )
		$erreurs['pu'] = $erreur;
	/* verifier la date */
	if ($erreur = association_verifier_date(_request('date_acquisition')) )
		$erreurs['date_acquisition'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_ressources_traiter($id_ressource='') {
	return formulaires_editer_objet_traiter('asso_ressources', $id_ressource, '', '',  generer_url_ecrire('ressources'), '');
}
?>
