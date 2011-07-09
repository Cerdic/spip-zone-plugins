<?php
/**
 * @name 		Recherche
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Formulaires
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_recherche_pubban_charger_dist(){
	return array(
			'action' => generer_url_ecrire('pubbanner'),
			'search_pubban' => _request('search_pubban')
		);
}
?>