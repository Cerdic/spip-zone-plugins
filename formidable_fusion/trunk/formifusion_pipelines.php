<?php

/**
 * Utilisation de pipelines
 * 
 * @package SPIP\Formidable_fusion\Pipelines
**/

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Afficher le formulaire d'import/fusion sur la page de configuration des champs
 * @param $flux
 * @return mixed
 */
function formifusion_affiche_droite($flux){
	if ($flux['args']['exec']=='formulaire_edit'
		&& $id_formulaire = intval($flux['args']['id_formulaire'])
		&& $flux['args']['configurer']=='champs'
		){

		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/formifusion',array('id_formulaire'=>$id_formulaire));
	}
	return $flux;
}

?>