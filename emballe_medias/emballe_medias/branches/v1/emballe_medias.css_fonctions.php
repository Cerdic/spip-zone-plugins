<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
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