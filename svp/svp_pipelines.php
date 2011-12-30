<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function svp_autoriser(){}

/**
 * Autorisation d'iconification d'un depot
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_depot_iconifier_dist($faire, $type, $id, $qui, $opt){
	return true;
}


/**
 * Ajout de l'onglet Ajouter les plugins dont l'url depend du l'existence ou pas d'un depot
 * de plugins
 *
 * @param array $flux
 * @return array
 */
function svp_ajouter_onglets($flux){
    if ($flux['args']=='plugins') {
		$compteurs = svp_compter('depot');
		$page = ($compteurs['depot'] == 0) ? 'depots' : 'charger_plugin';
        $flux['data']['charger_plugin']= new Bouton(
												find_in_theme('images/plugin-add-24.png'),
												'plugin_titre_automatique_ajouter',
												generer_url_ecrire($page));
	}
    return $flux;
}

?>
