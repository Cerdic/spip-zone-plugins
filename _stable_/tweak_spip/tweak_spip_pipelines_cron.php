<?php
include_spip('tweak_spip');

function tweak_spip_pre_indexation($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['pre_indexation']);
	return $flux;
}

function tweak_spip_pre_syndication($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['pre_syndication']);
	return $flux;
}

function tweak_spip_post_syndication($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['post_syndication']);
	return $flux;
}

function tweak_spip_taches_generales_cron($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['taches_generales_cron']);
	return $flux;
}

?>