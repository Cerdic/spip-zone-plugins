<?php
include_spip('tweak_spip');

function tweak_spip_affichage_final($flux){
	return tweak_pipeline('affichage_final', $flux);
}

function tweak_spip_insert_head($flux){
	return tweak_pipeline('insert_head', $flux);
}

function tweak_spip_rendu_evenement($flux){
	return tweak_pipeline('rendu_evenement', $flux);
}

?>