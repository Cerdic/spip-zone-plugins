<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function licence_affiche($id_licence,$logo_non,$lien_non){
	include_spip('inc/licence');
	$licence = $GLOBALS['licence_licences'][$id_licence];
	if (isset($licence['icon']) AND $logo_non != 'non')
		$licence['icon'] = "img_pack/".$licence['icon'];
	if ($lien_non == 'non')
		$licence['link'] = '';
	return recuperer_fond('licence/licence',$licence);
}

?>