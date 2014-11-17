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
		spip_reservations_details.id_evenement=spip_evenements.id_evenement AND
		spip_evenements.date_fin <="'.$date.'" AND
		spip_evenements.action_cloture =1' );
		
	while($data=sql_fetch($sql)){
		reservations_detail_instituer($data['id_reservations_detail'],array('statut'=>'cloture'));	
	};
	

	return 1;
}