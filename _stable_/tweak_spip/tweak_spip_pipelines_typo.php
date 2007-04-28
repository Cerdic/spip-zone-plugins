<?php
include_spip('cout_lancement');

function tweak_spip_nettoyer_raccourcis_typo($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['nettoyer_raccourcis_typo']))
		eval($cout_metas_pipelines['nettoyer_raccourcis_typo']);
	return $flux;
}

function tweak_spip_pre_propre($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['pre_propre']))
		eval($cout_metas_pipelines['pre_propre']);
	return $flux;
}

function tweak_spip_pre_typo($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['pre_typo']))
		eval($cout_metas_pipelines['pre_typo']);
	return $flux;
}

function tweak_spip_post_propre($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['post_propre']))
		eval($cout_metas_pipelines['post_propre']);
	return $flux;
}

function tweak_spip_post_typo($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['post_typo']))
		eval($cout_metas_pipelines['post_typo']);
	return $flux;
}


?>