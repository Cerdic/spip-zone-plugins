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

function action_supprimer_dons() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don= $securiser_action();

	sql_delete('spip_asso_comptes', "id_journal=$id_don AND imputation=".sql_quote($GLOBALS['association_metas']['pc_dons']));  
	sql_delete('spip_asso_dons', "id_don=$id_don");
}

?>
