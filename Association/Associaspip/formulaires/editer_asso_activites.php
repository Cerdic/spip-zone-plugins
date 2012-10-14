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

function formulaires_editer_asso_activites_charger_dist($id_activite='') {
	$contexte = formulaires_editer_objet_charger('asso_activites', $id_activite, '', '',  generer_url_ecrire('activites'), '');
	if (!$id_activite) { // si c'est un ajout
		$contexte['id_evenement'] = association_recuperer_entier('id_evenement');
		if ( !sql_countsel('spip_evenements', 'id_evenement='. $contexte['id_evenement']) )
			exit; // sortir sans proces si evenement inexistant
		$contexte['date_inscription'] = date('Y-m-d');
		$contexte['date_paiement'] = '';
	}
	association_chargeparam_operation('activites', $id_activite, $contexte);
	association_chargeparam_destinations('activites', $contexte);
	$contexte['_hidden'] .= "<input type='hidden' name='id_evenement' value='$contexte[id_evenement]' />"; // transmettre id_evenement via un champ cache

	// paufiner la presentation des valeurs
	if ($contexte['date_paiement']=='0000-00-00')
		$contexte['date_paiement'] = '';
	if (!$contexte['id_auteur'])
		$contexte['id_auteur']='';
	if ($contexte['quantite'])
		$contexte['quantite'] = association_formater_nombre($contexte['quantite']);

	return $contexte;
}

function formulaires_editer_asso_activites_verifier_dist($id_activite='') {
	$erreurs = array();

	if ($erreur = association_verifier_date('date_inscription') )
		$erreurs['date_inscription'] = $erreur;
	if ($erreur = association_verifier_date('date_paiement', TRUE) )
		$erreurs['date_paiement'] = $erreur;
	if ($erreur = association_verifier_montant('quantite') )
		$erreurs['quantite'] = $erreur;
	if ($erreur = association_verifier_montant('prix_activite') )
		$erreurs['prix_activite'] = $erreur;
	if ($erreur = association_verifier_membre('id_auteurt') )
		$erreurs['id_auteur'] = $erreur;
	if ($erreur = association_verifier_destinations('prix_activite') )
		$erreurs['destinations'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_activites_traiter_dist($id_activite='') {
	return formulaires_editer_objet_traiter('asso_activites', $id_activite, '', '',  generer_url_ecrire('inscrits_activite','id='.association_recuperer_entier('id_evenement')), '');
}

?>