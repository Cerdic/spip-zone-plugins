<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_cotisation() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$date= $_POST['date'];
	$journal= $_POST['journal'];
	$montant= $_POST['montant'];
	$justification =$_POST['justification'];
	$imputation=$GLOBALS['association_metas']['pc_cotisations'];
	$validite =$_POST['validite'];

	cotisation_insert($id_auteur, $montant, $journal, $justification, $imputation, $date, $validite);
}

function cotisation_insert($id_auteur, $montant, $journal, $justification, $imputation, $date, $validite)
{
	include_spip('base/association');
	sql_insertq('spip_asso_comptes', array(
				       'date' => $date,
				       'journal' => $journal,
				       'recette' => $montant,
				       'justification' => $justification,
				       'imputation' => $imputation,
				       'id_journal' => $id_auteur)
		    );
	sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, 
				   array(
					 "validite" => $validite,
					 "statut_interne" => 'ok'),
				   "id_auteur=$id_auteur");

}
?>
