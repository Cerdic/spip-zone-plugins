<?php
include_spip('tweak_spip');

function tweak_spip_pre_indexation($flux){
	return tweak_pipeline('pre_indexation', $flux);
}

function tweak_spip_pre_syndication($flux){
	return tweak_pipeline('pre_syndication', $flux);
}

function tweak_spip_post_syndication($flux){
	return tweak_pipeline('post_syndication', $flux);
}

function tweak_spip_taches_generales_cron($flux){
	return tweak_pipeline('taches_generales_cron', $flux);
}


?>