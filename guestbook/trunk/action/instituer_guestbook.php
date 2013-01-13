<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2013
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_guestbook() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_guestmessage, $statut) = preg_split('/\W/', $arg);
	$id_guestmessage = intval($id_guestmessage);
	$row = sql_fetsel("*", "spip_guestmessages", "id_guestmessage=$id_guestmessage");
	if (!$row) return;
	instituer_guestmessage($statut,$row);
}

function instituer_guestmessage($statut,$row){
	$id_guestmessage = $row['id_guestmessage'];
	$old = $row['statut'];
	sql_updateq("spip_guestmessages", array("statut" => $statut), "id_guestmessage=$id_guestmessage");
}
?>