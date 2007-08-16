<?php
// =======================================================================================================================================
// Balise : #PLUGIN
// =======================================================================================================================================
// Auteur: SarkASmeL, James
// Fonction : retourne une info d'un plugin donne
// =======================================================================================================================================
//

function balise_PLUGIN_dist($p) {
	$plugin = interprete_argument_balise(1,$p);
	$plugin = isset($plugin) ? str_replace('\'', '"', $plugin) : '""';
	$type_info = interprete_argument_balise(2,$p);
	$type_info = isset($type_info) ? str_replace('\'', '"', $type_info) : '"est_actif"';

	$p->code = 'calcul_info_plugin('.$plugin.', '.$type_info.')';
	return $p;
}

?>