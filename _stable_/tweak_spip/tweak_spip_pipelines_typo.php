<?php
include_spip('tweak_spip');

function tweak_spip_nettoyer_raccourcis_typo($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['nettoyer_raccourcis_typo']);
	return $flux;
}

function tweak_spip_pre_propre($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['pre_propre']);
	return $flux;
}

function tweak_spip_pre_typo($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['pre_typo']);
	return $flux;
}

function tweak_spip_post_propre($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['post_propre']);
	return $flux;
}

function tweak_spip_post_typo($flux){
	global $tweaks_metas_pipes;
	eval($tweaks_metas_pipes['post_typo']);
	return $flux;
}


?>