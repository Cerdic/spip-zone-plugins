<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Les autorisations de Signalement
 *
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

// declarer la fonction du pipeline
function signalement_autoriser(){}

/**
 * Moderer le signalement
 * 
 */
function autoriser_moderersignalement_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('moderer', 'signalement', 0, $qui, $opt);
}

/**
 * Autorise a changer le statut d'un signalement :
 * seulement l'auteur du signalement lui même
 * ou un admin
 */
function autoriser_signalement_instituer_dist($faire, $type, $id, $qui, $opt){
	if (!intval($id)) return autoriser('moderer','signalement');
	$row = sql_fetsel('id_auteur','spip_signalements','id_signalement='.intval($id));
	return ($qui['statut'] == '0minirezo') OR $row?($row['id_auteur'] == $GLOBALS['visiteur_session']['id_auteur']):false;
}

function autoriser_signalement_moderer_dist($faire, $type, $id, $qui, $opt){
	// admins uniquement
	return $qui['statut']=='0minirezo'; 
}

/**
 * Modifier un signalement
 * Jamais
 */
function autoriser_signalement_modifier_dist($faire, $type, $id, $qui, $opt) {
	return false;
}

?>
