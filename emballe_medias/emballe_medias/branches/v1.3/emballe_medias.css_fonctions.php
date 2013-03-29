<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Fonctions spécifiques au squelette emballe_medias.css.html
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function em_tous_les_fonds($dir,$pattern){
	$liste = find_all_in_path($dir,$pattern);
	foreach($liste as $k=>$v)
		$liste[$k] = $dir . basename($v,'.html');
	return $liste;
}
?>