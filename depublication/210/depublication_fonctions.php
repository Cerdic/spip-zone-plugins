<?php
/*
 * Plugin Depublication
 * (c) 2010 Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */
 
function traduire_mois($num_mois) {
	if ($num_mois > 0 and $num_mois < 13) {
		return _T('date_mois_' . $num_mois);
	}
	return '';
}

function deux_digits($num) {
	$n = strlen($num);
	if ($n == 0) {
		return '00';
	}
	if ($n == 1) {
		return '0'.$num;
	}
	return $num;
}

function liste_des_statuts() {
	return array_map('_T', array_flip($GLOBALS['liste_des_etats']));
}

?>
