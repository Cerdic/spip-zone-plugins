<?php
/**
 * @name 		Autorisations
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline
function pubban_autoriser() {}

// Creer un nouveau site ?
function autoriser_banniere_creer_dist($faire, $type, $id, $qui, $opt){
	return ($qui['statut']=='0minirezo');
}

function autoriser_banniere_modifier_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut']=='0minirezo');
}

function autoriser_banniere_voir_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut']=='0minirezo');
}


?>