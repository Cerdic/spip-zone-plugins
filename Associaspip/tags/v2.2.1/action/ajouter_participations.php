<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_ajouter_participations() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_evenement = $securiser_action();

	$nom = _request('nom');
	$id_auteur = association_recuperer_entier('id_auteur');
	$quantite = association_recuperer_montant('quantite');
	$montant = association_recuperer_montant('prix_unitaire');
	$date_paiement = association_recuperer_date('date_paiement');
	$journal = _request('journal');
	$statut = association_passeparam_statut();
	$commentaire = _request('commentaire');
#	$action = _request('action');
	$id_activite = association_recuperer_entier('id_activite');
	sql_updateq('spip_asso_activites', array(
		'nom' => $nom,
		'id_auteur' => $id_auteur,
		'quantite' => $quantite,
		'prix_unitaire' => $montant,
		'date_paiement' => $date_paiement,
		'statut' => $statut,
		'commentaire' => $commentaire,
	), "id_activite=$id_activite");
	$id = sql_insertq('spip_asso_comptes', array(
		'date' => $date_paiement,
		'journal' => $journal,
		'recette' => $montant,
		'justification' => _T('asso:activite_justification_compte_inscription',array('id_activite' => $id_activite, 'nom' => $nom)),
		'imputation' => $GLOBALS['association_metas']['pc_activites'],
		'id_journal' => $id_activite,
	));
	spip_log("participation_insert: $id",'associaspip');

	return $id;
}

?>