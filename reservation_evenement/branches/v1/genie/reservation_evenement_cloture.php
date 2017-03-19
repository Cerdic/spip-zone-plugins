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

//Cloturer un évènement
function genie_reservation_evenement_cloture_dist ($t) {
	$date=date('Y-m-d G:i:s');

	include_spip('action/editer_objet');

	//Sélection des détails de réservation d'événements passé  qui ont action_cloture activé
	$sql=sql_select(
		'id_reservations_detail,t1.id_evenement,date_fin',
		'spip_reservations_details t1,spip_evenements t2',
		't1.statut="accepte" AND
			t1.id_evenement=t2.id_evenement AND
			t2.date_fin <="'.$date.'" AND
			t2.action_cloture =1'
		);

	$id_evenement=array();
	while($data=sql_fetch($sql)){
		if(!$date_fin=sql_getfetsel('date_fin','spip_evenements','id_evenement_source='.$data['id_evenement'],'','date_fin DESC','1'))
		$date_fin=$data['date_fin'];

		spip_log('cron évènement cloturé I date fin:'.strtotime($date_fin).', date:'.strtotime($date).' id_evenement:'.$data['id_evenement'].', id_reservations_detail:'.$data['id_reservations_detail'],'reservation_evenement');

		//Déclencher le changement de statut et les actions qui en dépendent
		if(strtotime($date_fin)<=strtotime($date)){
			spip_log('cron évènement cloturé II date fin:'.$date_fin.', date:'.$date.' id_evenement:'.$data['id_evenement'].', id_reservations_detail:'.$data['id_reservations_detail'],'reservation_evenement');
			set_request('envoi_separe_actif','oui'); //Nécessaire pour permettre l'envoi du mail
			objet_instituer('reservations_detail',$data['id_reservations_detail'],array('statut'=>'cloture'));
			$id_evenement[]	= $data['id_evenement'];
		}
	};

	if (count($id_evenement)>0) sql_updateq('spip_evenements',array('action_cloture'=>3),'id_evenement IN ('.implode(',',$id_evenement).')');

	return 1;
}