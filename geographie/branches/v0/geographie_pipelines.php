<?php

function geographie_rechercher_liste_des_champs($tables){
	
	$tables['geo_pay'] = array('nom' => 8);
	$tables['geo_region'] = array('nom' => 8);
	$tables['geo_departement'] = array('nom' => 8, 'abbr'=>4);
	$tables['geo_commune'] = array('nom' => 8, 'code_postal'=>4);

	return $tables;
}