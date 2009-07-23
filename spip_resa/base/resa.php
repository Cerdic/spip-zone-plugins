<?php

function resa_declarer_tables_principales($tables_principales)
{
	$spip_resa_calendrier = array(
		'id_calendrier'	=> 'int(11) NOT NULL AUTO_INCREMENT',
		'id_article' 	=> 'int(11) NOT NULL'
	) ;
	
	$spip_resa_calendrier_key = array(
		'PRIMARY KEY' 	=> 'id_calendrier'
	) ;
	
	$tables_principales['spip_resa_calendrier'] = array(
		'field' => &$spip_resa_calendrier,
		'key'	=> &$spip_resa_calendrier_key
	) ;
	
	$spip_resa_reservation = array(
		'id_reservation'	=> 'int(11) NOT NULL AUTO_INCREMENT',
		'id_calendrier'		=> 'int(11) NOT NULL',
		'ts'				=> 'int(10) NOT NULL'
	) ;
	
	$spip_resa_reservation_key = array(
		'PRIMARY KEY'	=> 'id_reservation',
	) ;
	
	$tables_principales['spip_resa_reservation'] = array(
		'field'	=> &$spip_resa_reservation,
		'key'	=> &$spip_resa_reservation_key
	) ;
		
	return $tables_principales ;
}

?>
