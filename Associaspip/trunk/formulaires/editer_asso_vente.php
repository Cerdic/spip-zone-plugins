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

function formulaires_editer_asso_vente_charger_dist($id_vente=0) {
	$contexte = formulaires_editer_objet_charger('asso_vente', $id_vente, '', '',  generer_url_ecrire('ventes'), '');
	if (!$id_vente) { // si c'est une nouvelle operation, on charge la date d'aujourd'hui
		$contexte['date_vente'] = $contexte['date_envoi'] = date('Y-m-d');
		$contexte['quantite'] = 1;
	}
	association_chargeparam_operation('ventes', $id_vente, $contexte);
	association_chargeparam_destinations('ventes', &$contexte);

	// paufiner la presentation des valeurs
	if (!$contexte['id_auteur'])
		$contexte['id_auteur'] = '';
	if ($contexte['prix_unitaire'])
		$contexte['prix_unitaire'] = association_formater_nombre($contexte['prix_unitaire']);
	if ($contexte['frais_envoi'])
		$contexte['frais_envoi'] = association_formater_nombre($contexte['frais_envoi']);
	if ($contexte['quantite'])
		$contexte['quantite'] = association_formater_nombre($contexte['quantite']);

	return $contexte;
}

function formulaires_editer_asso_vente_verifier_dist($id_vente=0) {
	$erreurs = array();

	if ($erreur = association_verifier_montant('prix_unitaire') )
		$erreurs['prix_unitaire'] = $erreur;
	if ($erreur = association_verifier_montant('frais_envoi') )
		$erreurs['frais_envoi'] = $erreur;
	if ($erreur = association_verifier_montant('quantite') )
		$erreurs['quantite'] = $erreur;
	if ($erreur = association_verifier_membre('id_auteur') )
		$erreurs['id_auteur'] = $erreur;
	if ($erreur = association_verifier_destinations('prix_unitaire') )
		$erreurs['destinations'] = $erreur;
	if ($erreur = association_verifier_date('date_vente') )
		$erreurs['date_vente'] = $erreur;
	if ($erreur = association_verifier_date('date_envoi') )
		$erreurs['date_envoi'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_vente_traiter($id_vente=0) {
	return formulaires_editer_objet_traiter('asso_vente', $id_vente, '', '',  generer_url_ecrire('ventes'), '');
}

?>