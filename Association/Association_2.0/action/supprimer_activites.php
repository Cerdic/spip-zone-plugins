<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_activites() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$w = sql_in('id_activite', $_REQUEST['drop']);
	sql_delete('spip_asso_activites', $w);
	$w = sql_in('id_journal', $_REQUEST['drop']);
	sql_delete('spip_asso_comptes', $w . " AND imputation=".sql_quote($GLOBALS['asso_metas']['pc_activites']));
}

?>
