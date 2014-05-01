<?php

/**
 * Utilisation de pipelines
 * 
 * @package SPIP\Formidable\Pipelines
**/

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Afficher les formulaires utilises par un objet
 * @param $flux
 * @return mixed
 */
 //formulaire_edit&id_formulaire=6&configurer=champs
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