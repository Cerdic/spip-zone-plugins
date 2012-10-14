<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/association_comptabilite');

function formulaires_editer_asso_categories_charger_dist($id_categorie='') {
	$id_categorie = association_passeparam_id('categorie');
	$contexte = formulaires_editer_objet_charger('asso_categories', $id_categorie, '', '',  generer_url_ecrire('categories'), '');

	// paufiner la presentation des montants
	if ($contexte['prix_cotisation'])
		$contexte['prix_cotisation'] = association_formater_nombre($contexte['prix_cotisation']);

	return $contexte;
}

function formulaires_editer_asso_categories_verifier_dist($id_categorie) {
	$erreurs = array();

	if ($erreur = association_verifier_montant('prix_cotisation') )
		$erreurs['prix_cotisation'] = $erreur;
	if ($erreur = association_verifier_montant('duree') )
		$erreurs['duree'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_categories_traiter_dist($id_categorie) {
	return formulaires_editer_objet_traiter('asso_categories', $id_categorie, '', '',  generer_url_ecrire('categories'), '');
}

?>