<?php
/**
 * Utilisations de pipelines par Réservation Événement Cloture
 *
 * @plugin     Réservation Événement Cloture
 * @copyright  2014
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement_cloture\Genie
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	
//Eliminer les notification après l'interval définit dans config
function genie_reservation_evenement_cloture_dist ($t) {
	$date=date('Y-m-d G:i:s');
 
	$sql=sql_select(
		'*',
		'spip_reservations_details,spip_evenements',
		'spip_reservations_details.statut="accepte" AND 
		spip_evenements.date_fin <="'.$date.'"');
		
	while($data=sql_fetch($sql)){
		spip_log($data,'teste');		
	};
	

	return 1;
}