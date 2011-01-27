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

function action_supprimer_ressources() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_ressource= $securiser_action();

	sql_delete('spip_asso_ressources', "id_ressource=$id_ressource" );
}

?>
