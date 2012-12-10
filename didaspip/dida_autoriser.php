<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

//Pour déclarer le pipeline
function dida_autoriser(){}

// autorisation des boutons
function autoriser_didaconfigurer_menu_dist($faire, $type, $id, $qui, $opt) {	
	return (($qui['statut'] == '0minirezo') OR ($GLOBALS['meta']['accesdida']!="non"));
}
function autoriser_configurerdida_menu_dist($faire, $type, $id, $qui, $opt) {	
	return ($qui['statut'] == '0minirezo');
}
?>
