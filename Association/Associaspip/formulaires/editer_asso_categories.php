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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/association_comptabilite');

function formulaires_editer_asso_categories_charger_dist($id_categorie='')
{
	$id_categorie = association_passeparam_id('categorie');
	/* charger dans $contexte tous les champs de la table spip_asso_categories associes a l'id_categorie passe en param */
	$contexte = formulaires_editer_objet_charger('asso_categories', $id_categorie, '', '',  generer_url_ecrire('categories'), '');
//	$contexte = !$id_categorie? '' : sql_fetsel("*", "spip_asso_categories", "id_categorie=$id_categorie");

	/* paufiner la presentation des montants  */
	if ($contexte['cotisation'])
		$contexte['cotisation'] = association_formater_nombre($contexte['cotisation']);

	/* renvoyer le contexte pour (p)re-remplir le formulaire  */
	return $contexte;
}

function formulaires_editer_asso_categories_verifier_dist($id_categorie)
{
	$erreurs = array();

	/* on verifie que cotisation et duree ne sont pas negatifs */
	if ($erreur = association_verifier_montant('cotisation') )
		$erreurs['cotisation'] = $erreur;
	if ($erreur = association_verifier_montant('duree') )
		$erreurs['duree'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_categories_traiter_dist($id_categorie)
{
	return formulaires_editer_objet_traiter('asso_categories', $id_categorie, '', '',  generer_url_ecrire('categories'), '');
}

?>