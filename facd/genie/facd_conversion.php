<?php
/**
 * FACD
 * File d'Attente de Conversion de Documents
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * 
 * @return
 * @param object $time
 */
function genie_facd_conversion($time)  {
	spip_log('début de tache cron','facd');
	$traiter = charger_fonction('facd_traiter_conversion','action');
	$traiter();
	spip_log('fin de tache cron','facd');
	return 1;
}

?>