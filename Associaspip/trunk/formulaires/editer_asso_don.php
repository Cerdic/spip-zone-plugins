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

function formulaires_editer_asso_don_charger_dist($id_don=0, $id_auteur=0, $editable=true) {
	$contexte = formulaires_editer_objet_charger('asso_dons', $id_don, '', '',  generer_url_ecrire('dons'), '');
	if (!$id_don) { // si c'est une nouvelle operation, on charge la date d'aujourd'hui
		$contexte['date_don'] = date('Y-m-d');
	}
	association_chargeparam_operation('dons', $id_don, $contexte);
	association_chargeparam_destinations('dons', $contexte);

	// paufiner la presentation des valeurs
	if (!$contexte['id_auteur'] AND is_numeric($id_auteur)) {
		$contexte['id_auteur']= $id_auteur;
		$contexte['nom']= sql_getfetsel('nom', 'spip_auteurs', "id_auteur=$id_auteur");
		$contexte['auteur_fixe']= true;
	}
	if ($contexte['argent'])
		$contexte['argent'] = association_formater_nombre($contexte['argent']);
	if ($contexte['valeur'])
		$contexte['valeur'] = association_formater_nombre($contexte['valeur']);
	$contexte['readonly'] = $editable ? '' : ' readonly="readonly"';
	return $contexte;
}

function formulaires_editer_asso_don_verifier_dist($id_don=0) {
	$erreurs = array();

	if ($erreur = association_verifier_montant('argent') )
		$erreurs['argent'] = $erreur;
	if ($erreur = association_verifier_montant('valeur') )
		$erreurs['valeur'] = $erreur;
	if ($erreur = association_verifier_membre('id_auteur') )
		$erreurs['id_auteur'] = $erreur;
	if ($erreur = association_verifier_destinations('argent') )
		$erreurs['destinations'] = $erreur;
	if ($erreur = association_verifier_date('date_don') )
		$erreurs['date_don'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_don_traiter($id_don=0) {
	return formulaires_editer_objet_traiter('asso_don', $id_don, '', '',  generer_url_ecrire('dons'), '');
}

?>