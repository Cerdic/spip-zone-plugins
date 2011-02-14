<?php 

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * Actualise la liste des plugins disponibles automatiquement
 * 
 * @return
 * @param object $time
 */
function genie_step_actualise($time)  {
	include_spip('inc/step');
	spip_log('Mise a jour des listes de plugins locaux et distants','step');
	step_update();
	return 1;
}

?>