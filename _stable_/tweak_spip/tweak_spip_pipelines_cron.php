<?php
include_spip('tweak_spip_init');

function tweak_spip_pre_indexation($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['pre_indexation']))
		eval($cout_metas_pipelines['pre_indexation']);
	return $flux;
}

function tweak_spip_pre_syndication($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['pre_syndication']))
		eval($cout_metas_pipelines['pre_syndication']);
	return $flux;
}

function tweak_spip_post_syndication($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['post_syndication']))
		eval($cout_metas_pipelines['post_syndication']);
	return $flux;
}

function tweak_spip_taches_generales_cron($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['taches_generales_cron']))
		eval($cout_metas_pipelines['taches_generales_cron']);
	return $flux;
}

?>