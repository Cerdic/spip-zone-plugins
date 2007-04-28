<?php
include_spip('cout_lancement');

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
		$flux .=  "\n<!-- Debut header du Couteau Suisse -->\n" . join("\n", $cout_metas_pipelines['header']) . "<!-- Fin header du Couteau Suisse -->\n\n";
		else $flux .=  "\n<!-- Rien pour le Couteau Suisse -->\n";
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