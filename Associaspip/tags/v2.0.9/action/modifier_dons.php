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

function action_modifier_dons() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don=$securiser_action();

	$journal= _request('journal');
	$date_don = _request('date_don');
	$bienfaiteur = _request('bienfaiteur');
	$id_adherent = _request('id_adherent');
	$argent = _request('argent');
	$colis = _request('colis');
	$valeur = _request('valeur');
	$contrepartie = _request('contrepartie');
	$commentaire = _request('commentaire');

	include_spip('base/association');
	sql_updateq('spip_asso_comptes', array(
		    'date' => $date_don,
		    'recette' => $argent,
		    'journal' => $journal,
		    'justification' => "[->don$id_don] [->membre$id_adherent]"),
		    "id_journal=$id_don AND imputation=".sql_quote($GLOBALS['association_metas']['pc_dons']));
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
}
?>
