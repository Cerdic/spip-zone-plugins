<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_asso_pret_charger_dist($id_pret=0) {
	$contexte = formulaires_editer_objet_charger('asso_prets', $id_pret, '', '',  generer_url_ecrire('prets'), '');
	if (!$id_pret) { // si c'est une nouvelle operation, on charge la date d'aujourd'hui, le statut et les prix de location de base et de caution a deposer
		$contexte['date_sortie'] = $contexte['date_retour'] = date('Y-m-d');
		$contexte['date_retour'] = '';
		$contexte['heure_sortie'] = $contexte['heure_retour'] = date('H:i');
		$contexte['commentaire_sortie'] = $contexte['commentaire_retour'] = '';
		$contexte['id_ressource'] = association_passeparam_id('ressource');
		$ressource = sql_fetsel('pu, ud, prix_caution', 'spip_asso_ressources', "id_ressource=" . intval($contexte['id_ressource']));
		$contexte['ud'] = $ressource['ud'];
		$montant = $contexte['prix_unitaire'] = $ressource['pu'];
		$caution = $contexte['prix_caution'] = $ressource['prix_caution'];
	} else { // sinon on recupere les informations relatives au depot de caution
		$montant = sql_getfetsel('recette', 'spip_asso_comptes', "imputation=". sql_quote($GLOBALS['association_metas']['pc_prets']) ." AND id_journal='$id_pret' ");
		$contexte['ud'] =  sql_getfetsel('ud', 'spip_asso_ressources', "id_ressource=" . intval($contexte['id_ressource']));
		$contexte['heure_sortie'] = substr($contexte['date_sortie'],12,5);
		$contexte['date_sortie'] = substr($contexte['date_sortie'],0,10);
		$contexte['heure_retour'] = substr($contexte['date_retour'],12,5);
		$contexte['date_retour'] = substr($contexte['date_retour'],0,10);
		$contexte['mode_caution1'] = sql_getfetsel('journal', 'spip_asso_comptes', "imputation=". sql_quote($GLOBALS['association_metas']['pc_cautions']) ." AND id_journal='$id_pret' AND date_operation='$contexte[date_caution1]' and recette>0 ");
		$contexte['mode_caution0'] = sql_getfetsel('journal', 'spip_asso_comptes', "imputation=". sql_quote($GLOBALS['association_metas']['pc_cautions']) ." AND id_journal='$id_pret' AND date_operation='$contexte[date_caution0]' and depense>0 ");
	}
	association_chargeparam_operation('prets', $id_pret, $contexte);
	association_chargeparam_destinations('prets', $contexte);
	$contexte['montant'] = $montant;
	$contexte['_hidden'] .= "<input type='hidden' name='id_ressource' value='$contexte[id_ressource]' />";
	$contexte['_hidden'] .= "<input type='hidden' name='ud' value='$contexte[ud]' />";
	$contexte['_hidden'] .= "<input type='hidden' name='prix_caution' value='$contexte[prix_caution]' />";

	// paufiner la presentation des valeurs
	if ($contexte['date_retour']=='0000-00-00')
		$contexte['date_retour'] = '';
	if ($contexte['date_caution1']=='0000-00-00')
		$contexte['date_caution1'] = '';
	if ($contexte['date_caution0']=='0000-00-00')
		$contexte['date_caution0'] = '';
	if (!$contexte['id_auteur'])
		$contexte['id_auteur']='';
	if ( floatval($contexte['prix_caution'])==0 )
		$contexte['prix_caution'] ='';
	if ($contexte['montant'])
		$contexte['montant'] = association_formater_nombre($contexte['montant']);
	if ($contexte['prix_unitaire'])
		$contexte['prix_unitaire'] = association_formater_nombre($contexte['prix_unitaire']);
	if ($contexte['duree'])
		$contexte['duree'] = association_formater_nombre($contexte['duree']);

	return $contexte;
}

function formulaires_editer_asso_pret_verifier_dist($id_pret=0) {
	$erreurs = array();

	set_request('montant', _request('prix_unitaire')*_request('duree') );
	if ($erreur = association_verifier_montant('montant') )
		$erreurs['montant'] = $erreur;
	if ($erreur = association_verifier_montant('prix_unitaire') )
		$erreurs['prix_unitaire'] = $erreur;
	if ($erreur = association_verifier_montant('duree') )
		$erreurs['duree'] = $erreur;
	if ($erreur = association_verifier_membre('id_auteur') )
		$erreurs['id_auteur'] = $erreur;
	if ($erreur = association_verifier_destinations('montant') )
		$erreurs['destinations'] = $erreur;
	if ($erreur = association_verifier_date('date_sortie') )
		$erreurs['date_sortie'] = $erreur;
	if ($erreur = association_verifier_date('date_retour', TRUE) )
		$erreurs['date_retour'] = $erreur;
	if ($erreur = association_verifier_date('date_caution1', TRUE) )
		$erreurs['date_caution1'] = $erreur;
	if ($erreur = association_verifier_date('date_caution0', TRUE) )
		$erreurs['date_caution0'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_pret_traiter_dist($id_pret=0) {
	$id_ressource = association_recuperer_entier('id_ressource');
	return formulaires_editer_objet_traiter('asso_prets', $id_pret, '', '',  generer_url_ecrire('prets',"id=$id_ressource"), '');
}

?>