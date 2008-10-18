<?php


/* 
 * Pipeline 'jquery_plugins' pour SPIP = 1.9.3 : ajouter simplement
 * les scripts a inserer au tableau de scripts passe dans le flux
 * cf. http://doc.spip.org/@f_jQuery
 */
function jqueryp_jquery_plugins($flux) {
	include_spip(_DIR_PLUGIN_JQUERYP.'jqueryp_fonctions'); // inclure les fonctions
	$flux = array_unique(array_merge($flux,(array)jqueryp_liste_plugins('actifs')));
	return $flux;
}

?>
