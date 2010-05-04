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

function action_supprimer_adherents() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	$where = sql_in('id_auteur', $_POST["drop"]);
	sql_delete(_ASSOCIATION_AUTEURS_ELARGIS, $where);
	sql_delete('spip_auteurs', $where);
}
?>
