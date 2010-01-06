<?php
/**
 * Les dates sont récupérées en JJ/MM/AAAA
 * Elles sont restituées sous forme AAAA-MM-JJ
 */
function formater_date_poursql($date){
	// On tolère différents séparateurs
	$date = ereg_replace("\.|/| ",'-',$date);
	list($jour,$mois,$annee) = explode('-',$date);
	$date = $annee.'-'.$mois.'-'.$jour;
	return $date;
}
/**
 * Les dates sont récupérées en AAAA-MM-JJ
 * Elles sont restituées sous forme JJ/MM/AAAA
 */
function restaurer_date($date){
	if(!$date) return ' ';
	list($annee,$mois,$jour) = explode('-',$date);
	$date = $jour.'-'.$mois.'-'.$annee;
	return $date;
}
