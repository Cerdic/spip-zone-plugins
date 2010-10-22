<?php
/**
 * Insertion dans le pipeline affiche_enfants
 * Affiche un bloc avec la liste des paquets associes au depot affiche
 *
 * @param object $flux
 * @return $flux
 */
function svp_affiche_enfants($flux){
	if ($flux['args']['exec'] == 'depots') {
		$id_depot = $flux['args']['id_depot'];
		$flux['data'] .= recuperer_fond('prive/liste/paquets_plugin_depot', 
			array('id_depot'=>$id_depot, 
				'titre' => _T('svp:titre_boite_paquets_plugin_depot'), 
				'bloc' => '_paquets_plugin'));
		$flux['data'] .= recuperer_fond('prive/liste/paquets_autre_depot', 
			array('id_depot'=>$id_depot, 
				'titre' => _T('svp:titre_boite_paquets_autre_depot'), 
				'bloc' => '_paquets_autre'));
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_css
 * Permet d'inserer les css necessaires aux page publiques de SVP
 *
 * @param object $flux
 * @return $flux
 */
function svp_insert_head_css($flux) {
	if ($f = find_in_path('svp_habillage.css')) {
		$flux .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="all" />';
	}
	return $flux;
}

?>
