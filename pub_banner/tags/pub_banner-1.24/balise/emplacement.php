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

function balise_EMPLACEMENT($p) {
   return calculer_balise_dynamique($p, EMPLACEMENT, array());
}

function balise_EMPLACEMENT_dyn($id_empl) {
	$empl = pubban_recuperer_emplacement($id_empl);
	echo $empl['titre'];
}

?>