<?php
include_spip('tweak_spip_init');

function tweak_spip_affichage_final($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['affichage_final']))
		eval($cout_metas_pipelines['affichage_final']);
	return $flux;
}

function tweak_spip_insert_head($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['insert_head']))
		eval($cout_metas_pipelines['insert_head']);
	if (isset($cout_metas_pipelines['header']))
		$flux .=  "\n<!-- Debut header Tweak-SPIP -->\n" . join("\n", $cout_metas_pipelines['header']) . "<!-- Fin header Tweak-SPIP -->\n\n";
		else $flux .=  "\n<!-- Rien pour Tweak-SPIP -->\n";
	return $flux;
}
/*
function tweak_spip_rendu_evenement($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['header_rendu_evenementprive']))
		eval($cout_metas_pipelines['rendu_evenement']);
	return $flux;
}
*/
?>