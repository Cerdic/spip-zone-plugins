<?php
include_spip('tweak_spip');

function tweak_spip_nettoyer_raccourcis_typo($flux){
	return tweak_pipeline('nettoyer_raccourcis_typo', $flux);
}

function tweak_spip_pre_propre($flux){
	return tweak_pipeline('pre_propre', $flux);
}

function tweak_spip_pre_typo($flux){
	return tweak_pipeline('pre_typo', $flux);
}

function tweak_spip_post_propre($flux){
	return tweak_pipeline('post_propre', $flux);
}

function tweak_spip_post_typo($flux){
	return tweak_pipeline('post_typo', $flux);
}


?>