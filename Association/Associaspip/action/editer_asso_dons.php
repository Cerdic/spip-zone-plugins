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


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_asso_dons() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don=$securiser_action();

	include_spip('base/association');
	include_spip('inc/association_comptabilite');

	$journal= _request('journal');
	$date_don = _request('date_don');

	$bienfaiteur = _request('bienfaiteur');
	$id_adherent = intval(_request('id_adherent'));

	if (!$bienfaiteur AND $id_adherent) {
		$nom_prenom = sql_fetsel('nom_famille, prenom', 'spip_asso_membres', "id_auteur=$id_adherent");
		$bienfaiteur = $nom_prenom['prenom'].' '.$nom_prenom['nom_famille'];
	}

	if ($id_adherent) {
		$bienfaiteur = "[$bienfaiteur" . "->membre$id_adherent]";
	}

	$argent = association_recupere_montant(_request('argent'));
	$colis = _request('colis');
	$valeur = association_recupere_montant(_request('valeur'));
	$contrepartie = _request('contrepartie');
	$commentaire = _request('commentaire');

	if ($id_don) { /* c'est une modification */
		$id_compte = _request('id_compte');

		// on modifie l'operation comptable associe au don
		association_modifier_operation_comptable($date_don, $argent, 0, "[->don$id_don] - $bienfaiteur", $GLOBALS['association_metas']['pc_dons'], $journal, '', $id_compte);

		sql_updateq('spip_asso_dons', array(
				'date_don' => $date_don,
				'bienfaiteur' => $bienfaiteur,
				'id_adherent' => $id_adherent,
				'argent' => $argent,
				'colis' => $colis,
				'valeur' => $valeur,
				'contrepartie' => $contrepartie,
				'commentaire' => $commentaire),
			    "id_don=$id_don");
	} else { /* c'est un ajout */
		$id_don = sql_insertq('spip_asso_dons', array(
			'date_don' => $date_don,
			'bienfaiteur' => $bienfaiteur,
			'id_adherent' => $id_adherent,
			'argent' => $argent,
			'colis' => $colis,
			'valeur' => $valeur,
			'contrepartie' => $contrepartie,
		 	'commentaire' => $commentaire));
	
		association_ajouter_operation_comptable($date_don, $argent, 0, "[->don$id_don] - $bienfaiteur", $GLOBALS['association_metas']['pc_dons'], $journal, $id_don);
	}

	return array($id_don, '');
}
?>
