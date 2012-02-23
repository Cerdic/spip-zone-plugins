<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_participations() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_evenement = $securiser_action();

	$nom = _request('nom');
	$id_membre = intval(_request('id_membre'));
	$membres = _request('membres');
	$non_membres = _request('non_membres');
	$inscrits = intval(_request('inscrits'));
	$montant = association_recupere_montant(_request('montant'));
	$date_paiement = _request('date_paiement');
	$journal = _request('journal');
	$statut = _request('statut');
	$commentaire = _request('commentaire');
#	$action = _request('action');
	$id_activite = intval(_request('id_activite'));
	sql_updateq('spip_asso_activites', array(
		'nom' => $nom,
		'id_adherent' => $id_membre,
		'membres' => $membres,
		'non_membres' => $non_membres,
		'inscrits' => $inscrits,
		'montant' => $montant,
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