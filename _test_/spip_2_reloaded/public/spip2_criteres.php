<?php
/**
 * Plugin Spip 2.0 Reloaded
 * Ce que vous ne trouverez pas dans Spip 2.0
 * (c) 2008 Cedric Morin
 * Licence GPL
 * 
 */

/* le critere {tableau ...} des boucles pour:POUR */
function critere_POUR_tableau_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0])){
		$table = calculer_liste($crit->param[0], array(), $boucles, $boucle->id_parent);
		$boucle->having[]=array("'tableau'",$table);
	}
}

/* le critere {si ...} des boucles condition:CONDITION */
function critere_CONDITION_si_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0])){
		$si = calculer_liste($crit->param[0], array(), $boucles, $boucle->id_parent);
		$boucle->having[]='($test='.$si.')?array(\'tableau\',\'1:1\'):\'\'';
	}
}

?>