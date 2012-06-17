<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * 
 * @return
 * @param object $time
 */
function genie_spipmotion_file($time)  {
	spip_log('début de tache cron','spipmotion');
	$encoder = charger_fonction('spipmotion_encoder','action');
	$encoder();
	spip_log('fin de tache cron','spipmotion');
	return 1;
}
?>