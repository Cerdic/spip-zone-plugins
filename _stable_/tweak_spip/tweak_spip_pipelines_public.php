<?php
include_spip('tweak_spip_init');

function tweak_spip_affichage_final($flux){
	global $tweaks_metas_pipes;
	if (isset($tweaks_metas_pipes['affichage_final']))
		eval($tweaks_metas_pipes['affichage_final']);
	return $flux;
}

function tweak_spip_insert_head($flux){
	global $tweaks_metas_pipes;
	if (isset($tweaks_metas_pipes['insert_head']))
		eval($tweaks_metas_pipes['insert_head']);
	if (isset($tweaks_metas_pipes['header']))
		$flux .=  "\n<!-- Debut header Tweak-SPIP -->\n" . join("\n", $tweaks_metas_pipes['header']) . "<!-- Fin header Tweak-SPIP -->\n\n";
		else $flux .=  "\n<!-- Rien pour Tweak-SPIP -->\n";
	return $flux;
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