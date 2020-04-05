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

function action_modifier_comptes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_compte=$securiser_action();

	$date= _request('date');
	$recette= _request('recette');
	$depense= _request('depense');
	$justification= _request('justification');
	$journal= _request('journal');
	include_spip('base/association');
	sql_updateq('spip_asso_comptes', array(
		    'date' => $date,
		    'recette' => $recette,
		    'depense' => $depense,
		    'journal' => $journal,
		    'justification' => $justification),
		    "id_compte=$id_compte");
}
?>
