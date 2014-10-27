<?php
/**
 * Fonctions utiles au plugin Archive notifications
 *
 * @plugin     Archive notifications
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Notifications_archive\Inc
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function inc_archiver_notification_dist($o){
	include_spip('inc/config');
	$config=lire_config('notifications_archive',array());
	if(isset($config[$o['type']]['activer']) AND $config[$o['type']]['activer']=='on'){
		include_spip('inc/editer');    	
		foreach($o AS $champ=>$valeur){
			if(is_array($valeur))$valeur=implode(',',$valeur);
			set_request($champ,$valeur);
		}
		formulaires_editer_objet_traiter('notification','new');
	}   
}

?>