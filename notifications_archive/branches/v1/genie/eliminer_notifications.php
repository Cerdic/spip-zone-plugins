<?php
/**
 * Utilisations de pipelines par Archive notifications
 *
 * @plugin     Archive notifications
 * @copyright  2014-2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Notifications_archive\genie
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	
//Eliminer les notification après l'interval définit dans config
function genie_eliminer_notifications_dist ($t) {
 
	include_spip('inc/config');
	$config=lire_config('notifications_archive',array());
	
	foreach($config AS $notification=>$options){
		if(is_numeric($options['duree']) AND $options['duree']>0){
			$periode=$options['duree']*24*3600;
	
			$mydate = sql_quote(date("Y-m-d H:i:s", time() - $periode));   
			
			spip_log("genie pour $notification <:  $mydate ",'notifications_archive');         
			sql_delete('spip_notifications', 'type='.sql_quote($notification).' AND date < '.$mydate);
		}
	}
	return 1;
}


?>