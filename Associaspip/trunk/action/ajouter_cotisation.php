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

function action_ajouter_cotisation() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$erreur = '';
	$date = association_recuperer_date('date');
	$journal = _request('journal');
	$montant =  association_recuperer_montant('montant');
	$justification = _request('justification');
	$validite = association_recuperer_date('validite');

	include_spip('inc/association_comptabilite');
	if ($GLOBALS['association_metas']['comptes'] && $GLOBALS['association_metas']['pc_cotisations']) // on verifie que la gestion comptable est activee et que le pc_cotisation n'est pas vide pour inserer l'operation
		comptabilite_operation_ajouter($date, $montant, 0, $justification, $GLOBALS['association_metas']['pc_cotisations'], $journal, $id_auteur);
	sql_updateq(
		'spip_asso_membres',
		array(
			'date_validite' => $validite,
			'statut_interne' => strtotime($validite)>strtotime('-1 day')?'ok':'echu', // on verifie que la date entree soit aujourd'hui ou dans le futur pour attribuer le statut ok
		),
		"id_auteur=$id_auteur"
	);

	return array($id_auteur, $erreur);
}

?>