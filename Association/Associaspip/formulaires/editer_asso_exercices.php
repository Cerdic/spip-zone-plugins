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

function formulaires_editer_asso_exercices_charger_dist($id_exercice='')
{
	/* charger dans $contexte tous les champs de la table spip_asso_exercices associes a l'id_exercice passe en param */
	$contexte = formulaires_editer_objet_charger('asso_exercices', $id_exercice, '', '',  generer_url_ecrire('exercices'), '');
	if (!$id_exercice) { /* si c'est un ajout */
		// rien a faire...
		// intitule, commentaire, debut, fin, valent '';
	}

	/* renvoyer le contexte pour (p)re-remplir le formulaire  */
	return $contexte;
}

function formulaires_editer_asso_exercices_verifier_dist($id_exercice)
{
	$erreurs = array();

	/* on verifie la validite des dates */
	if ($erreur = association_verifier_date('debut') )
		$erreurs['debut'] = $erreur;
	if ($erreur = association_verifier_date('fin') )
		$erreurs['fin'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_exercices_traiter_dist($id_exercice)
{
	return formulaires_editer_objet_traiter('asso_exercices', $id_exercice, '', '',  generer_url_ecrire('exercices'), '');
}

?>