<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Fonctions génériques de signalements
 *
 **/

 if (!defined("_ECRIRE_INC_VERSION")) return;

function signalement_trouver($id_objet,$objet,$id_auteur){
	$row = false;
	if ($id_auteur=intval($id_auteur)
		AND $id_objet = intval($id_objet)
		AND preg_match(",^\w+$,",$objet)){
		$row = sql_fetsel("*","spip_signalements","id_auteur=".intval($id_auteur)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($objet)." AND statut IN ('publie','refuse')");
	}
	return $row;
}

?>