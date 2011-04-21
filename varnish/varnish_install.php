<?php

/* verifier le besoin d'installer statjs et le signaler */
function varnish_install($test) {
	if ($GLOBALS['meta']['activer_statistiques'] == 'oui') {
		$plugins = unserialize($GLOBALS['meta']['plugin']);

		if (!isset($plugins['STATSJS'])) {
			include_spip('inc/presentation');
			echo debut_boite_info(true);
			echo $deja = _L("Pour utiliser les statistiques de SPIP avec Varnish, il est recommandé d'installer le plugin <a href='http://www.spip-contrib.net/3753' class='spip_out'>StatsJS</a>.");
			echo fin_boite_info(true);
		}
	}
	return true;
}

?>