<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_guestbook() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_message, $statut) = preg_split('/\W/', $arg);
	$id_message = intval($id_message);
	$row = sql_fetsel("*", "spip_guestbook", "id_message=$id_message");
	if (!$row) return;
	instituer_guestbook_message($statut,$row);
}
function instituer_guestbook_message($statut,$row){
	$id_message = $row['id_message'];
	$old = $row['statut'];
	sql_updateq("spip_guestbook", array("statut" => $statut), "id_message=$id_message");
}
?>