<?php
 
if (!defined("_ECRIRE_INC_VERSION")) return;

//espace prive
//affiche les filleuls de l'auteur (sur auteur_infos)
function mafia_affiche_gauche($flux){
	if($flux['args']['exec'] == 'auteur_infos' && $id_auteur=$flux['args']['id_auteur']) {
		include_spip('inc/presentation');
		$flux['data'] .= recuperer_fond('prive/boite/selecteur_filleuls', array('page_envoi'=>'auteur_infos','id_auteur'=>$id_auteur), array('ajax'=>true));
	}
	
	return $flux;
}



?>
