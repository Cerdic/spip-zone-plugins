<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

function licence_affiche($id_licence){
	include_spip('inc/licence');
	$licence = $GLOBALS['licence_licences'][$id_licence];
	if (isset($licence['icon']))
		$licence['icon'] = "img_pack/".$licence['icon'];
	return recuperer_fond('licence/licence',$licence);
}

?>