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
include_spip('inc/association_comptabilite');

function formulaires_editer_asso_destinations_charger_dist($id_destination='')
{
	/* charger dans $contexte tous les champs de la table spip_asso_destination associes a l'id_destination passe en param */
//	$contexte = formulaires_editer_objet_charger('asso_destination', $id_destination, '', '',  generer_url_ecrire('destination'), ''); // ne fonctionne pas ...parce-que la table n'est pas au pluriel ! (va savoir pourquoi)
	$contexte = sql_fetsel('*', 'spip_asso_destination', "id_destination='$id_destination' ");
	$contexte['_action'] = array('editer_asso_destinations', $id_destination);
	$contexte['retour'] = generer_url_ecrire('destination');

	/* renvoyer le contexte pour (p)re-remplir le formulaire  */
	return $contexte;
}

function formulaires_editer_asso_destinations_verifier_dist($id_destination)
{
	$erreurs = array();

	// formulaire tres simple : rien de particulier a verifier

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_destinations_traiter_dist($id_destination)
{
	return formulaires_editer_objet_traiter('asso_destinations', $id_destination, '', '',  generer_url_ecrire('destination'), '');
}

?>