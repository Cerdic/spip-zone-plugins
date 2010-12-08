<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos et son directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 *
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

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