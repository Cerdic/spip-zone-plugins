<?php
/**
 * Insertion dans le pipeline affiche_gauche
 * Affiche un bloc d'infos sur les statistiques d'utilisation des plugins et leur actualisation
 *
 * @param object $flux
 * @return $flux
 */
function svpstats_affiche_gauche($flux){
	if ($flux['args']['exec'] == 'depots_gerer') {
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/info_stats', array());
	}
	return $flux;
}

?>
