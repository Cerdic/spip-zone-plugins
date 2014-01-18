<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer un jeu de donnée a des objets listes sous forme
 * array($objet=>$id_objets,...)
 * $id_objets peut lui meme etre un scalaire ou un tableau pour une liste d'objets du meme type
 *
 * on peut passer optionnellement une qualification du (des) lien(s) qui sera
 * alors appliquee dans la foulee.
 * En cas de lot de liens, c'est la meme qualification qui est appliquee a tous
 *
 * Exemples:
 * infographies_data_associer(3, array('infographie'=>2));
 * 
 * @param int $id_infographies_data
 * @param array $objets
 * @param array $qualif
 * @return string
 */
function infographies_data_associer($id_infographies_data,$objets, $qualif = null){
	include_spip('action/editer_liens');
	return objet_associer(array('infographies_data'=>$id_infographies_data), $objets, $qualif);
}

?>