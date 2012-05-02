<?php
/**
 * @name 		Banniere
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Balises
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_BANNIERE($p) {
   return calculer_balise_dynamique($p, BANNIERE, array());
}

function balise_BANNIERE_dyn($id_banniere) {
	$empl = pubban_recuperer_banniere($id_banniere);
	echo $empl['titre'];
}

?>