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

function action_cotisation() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$date = _request('date');
	$journal = _request('journal');
	$montant =  association_recupere_montant(_request('montant'));

	$justification = _request('justification');
	$imputation = $GLOBALS['association_metas']['pc_cotisations'];
	$validite = _request('validite');

	cotisation_insert($id_auteur, $montant, $journal, $justification, $imputation, $date, $validite);

	return array($id_auteur, '');
}

function cotisation_insert($id_auteur, $montant, $journal, $justification, $imputation, $date, $validite)
{
	include_spip('base/association');
	if ($imputation != '') { /* si on a une imputation valide, on insere dans le livre de compte */
		include_spip('inc/association_comptabilite');
		association_ajouter_operation_comptable($date, $montant, 0, $justification, $imputation, $journal, $id_auteur);	
	}

	sql_updateq('spip_asso_membres', 
				   array(
					 "validite" => $validite,
					 "statut_interne" => 'ok'),
				   "id_auteur=$id_auteur");

}
?>
