<?php
/**
 * @name 		Demo Flash
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Balises
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/pubban_chargeur');

function balise_DEMO_FLASH($p) {
   return calculer_balise_dynamique($p, DEMO_FLASH, array());
}

function balise_DEMO_FLASH_dyn($p) {
	global $div; $div = '';
	foreach($GLOBALS['flash_demo'] as $key => $value){
		$div .= "<img src='".$value['src']."' title='".$value['titre']."' url='".$value['href']."' />";
	}
	echo($div);
}

?>