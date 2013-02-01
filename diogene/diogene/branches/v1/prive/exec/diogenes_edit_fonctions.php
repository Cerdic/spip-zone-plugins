<?php 

/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 * 
 * Fonctions PHP du squelette diogene_edit.html
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function diogene_form_logo($id_diogene){
	include_spip('inc/presentation');
	$editable = false;
	if(autoriser('iconifier', 'diogene', $id_diogene)){
		$editable = true;
	}
	$iconifier = charger_fonction('iconifier', 'inc');
	$icone = $iconifier('id_diogene', $id_diogene,'diogenes', false, $editable);
	return $icone;
}
?>