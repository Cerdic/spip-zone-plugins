<?php
include_spip('tweak_spip_init');

function tweak_spip_affichage_final($flux){
	global $tweaks_metas_pipes;
	if (isset($tweaks_metas_pipes['affichage_final']))
		eval($tweaks_metas_pipes['affichage_final']);
	return $flux;
}

function tweak_spip_insert_head($flux){
	$flux .= "\n<!-- Debut header TWEAKS -->\n"
		. tweak_insert_header('css')
		. tweak_insert_header('js');
	global $tweaks_metas_pipes;
	if (isset($tweaks_metas_pipes['insert_head']))
		eval($tweaks_metas_pipes['insert_head']);
	return $flux."\n<!-- Fin header TWEAKS -->\n\n";
}
/*
function tweak_spip_rendu_evenement($flux){
	global $tweaks_metas_pipes;
	if (isset($tweaks_metas_pipes['header_rendu_evenementprive']))
		eval($tweaks_metas_pipes['rendu_evenement']);
	return $flux;
}
*/
?>