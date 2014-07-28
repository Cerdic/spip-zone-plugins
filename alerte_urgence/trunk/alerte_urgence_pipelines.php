<?php
/*
 * Plugin Alerte Urgence
 * (c) 2010 Cedric
 * Distribue sous licence GPL
 *
 */

function alerte_urgence_affichage_final($flux) {
	if ($GLOBALS['html']
	  AND ($p=strpos($flux,'</body>'))!==false) {
		$flux = substr_replace($flux, recuperer_fond('inclure/alerte_urgence'), $p, 0);
	}
	return $flux;
}
?>