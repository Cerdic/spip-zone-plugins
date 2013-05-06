<?php
/**
affichage des horaires
*/
function heure_seance($date){
	// pour affichage sans zéro devant
	$h = 1*heures($date);
	$min = minutes($date);
	if ($min == '00')
		$min = '';
		
	return $h.'h'.$min;
}

/**
pour calculer l'heure de fin en fonction de la durée
**/
function heure_fin_seance($date, $duree = 0){
	$Y = annee($date);
	$m = mois($date);
	$d = jour($date);
	$h = heures($date);
	$min = minutes($date);
	$s = secondes($date);
	// si duree est au format 1h30 ou 1,30 ou 1.30 ou 1:30
	// sinon duree est en heure
	$duree = str_replace(array('h',',','.',':'), '|', $duree);
	$duree = preg_replace('`[^0-9\|]`', '', $duree);
	$tab_heure = explode('|',$duree);
	return date('Y-m-d H:i:s',mktime($h+$tab_heure[0], $min+$tab_heure[1], $s, $m, $d, $Y));
}
?>