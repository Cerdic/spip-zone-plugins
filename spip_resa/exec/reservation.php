<?php

function exec_reservation()
{
	$idCal	= _request('id_cal') ;
	$ts		= _request('ts') ;
	if( !empty($idCal) && !empty($ts) )
	{	
		if(
			sql_insertq(
				'spip_resa_reservation',
				array(
					'id_calendrier' => (int) $idCal,
					'ts' => (int) $ts
				)
			)
		)
			echo 'Reservation effectuee !' ;
		else
			echo 'Erreur dans l\'enregistrement de la reservation' ;
	}
}
