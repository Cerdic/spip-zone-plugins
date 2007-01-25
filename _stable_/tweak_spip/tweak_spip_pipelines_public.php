<?php
include_spip('tweak_spip');

function tweak_spip_affichage_final($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['affichage_final']);
	return $flux;
}

function tweak_spip_insert_head($flux){
	$flux .= tweak_insert_css();
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['insert_head']);
	return $flux;
}

function tweak_spip_rendu_evenement($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['rendu_evenement']);
	return $flux;
}

?>