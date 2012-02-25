<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_editer_asso_dons() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don = $securiser_action();
	$erreur = '';
	include_spip('inc/association_comptabilite');
	$id_compte = intval(_request('id_compte'));
	$journal = _request('journal');
	$date_don = _request('date_don');
	$bienfaiteur = _request('bienfaiteur');
	$id_adherent = intval(_request('id_adherent'));
	if (!$bienfaiteur AND $id_adherent) {
		$data =  sql_fetsel('sexe, nom_famille, prenom', 'spip_asso_membres', "id_auteur=$id_adherent");
		$bienfaiteur = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
	}
	$argent = association_recupere_montant(_request('argent'));
	$colis = _request('colis');
	$valeur = association_recupere_montant(_request('valeur'));
	$contrepartie = _request('contrepartie');
	$commentaire = _request('commentaire');
	if ($id_don) { /* c'est une modification */
		// on modifie les operations comptables associees au don
		association_modifier_operation_comptable($date_don, $argent, 0, "[don$id_don->don$id_don] -- ". ($id_adherent?"[$bienfaiteur->membre$id_adherent]":$bienfaiteur), $GLOBALS['association_metas']['pc_dons'], $journal, $id_don, $id_compte);
		$association_imputation = charger_fonction('association_imputation', 'inc');
		$critere = $association_imputation('pc_colis');
		$critere .= ($critere?' AND ':'') ."id_journal=$id_don";
		association_modifier_operation_comptable($date_don, $valeur, 0, "[colis$id_don->don$id_don] -- ". ($id_adherent?"[$bienfaiteur->membre$id_adherent]":$bienfaiteur), $GLOBALS['association_metas']['pc_colis'], $journal, $id_don, sql_getfetsel('id_compte', 'spip_asso_comptes', $critere) );
		// on modifie les informations relatives au don
		sql_updateq('spip_asso_dons', array(
			'date_don' => $date_don,
			'bienfaiteur' => $bienfaiteur,
			'id_adherent' => $id_adherent,
			'argent' => $argent,
			'colis' => $colis,
			'valeur' => $valeur,
			'contrepartie' => $contrepartie,
			'commentaire' => $commentaire,
		), "id_don=$id_don");
	} else { /* c'est un ajout */
		// on ajoute les informations relatives au don
		$id_don = sql_insertq('spip_asso_dons', array(
			'date_don' => $date_don,
			'bienfaiteur' => $bienfaiteur,
			'id_adherent' => $id_adherent,
			'argent' => $argent,
			'colis' => $colis,
			'valeur' => $valeur,
			'contrepartie' => $contrepartie,
		 	'commentaire' => $commentaire,
		));
		if (!$id_don) { // la suite serait aleatoire sans cette cle...
			$erreur = _T('Erreur_BdD_ou_SQL');
		} else { // on ajoute les operations comptables associees au don
			association_ajouter_operation_comptable($date_don, $argent, 0, "[don$id_don->don$id_don] -- ". ($id_adherent?"[$bienfaiteur->membre$id_adherent]":$bienfaiteur), $GLOBALS['association_metas']['pc_dons'], $journal, $id_don);
			association_ajouter_operation_comptable($date_don, $valeur, 0, "[colis$id_don->don$id_don] -- ". ($id_adherent?"[$bienfaiteur->membre$id_adherent]":$bienfaiteur), $GLOBALS['association_metas']['pc_colis'], $journal, $id_don);
		}
	}
	return array($id_don, $erreur);
}

?>