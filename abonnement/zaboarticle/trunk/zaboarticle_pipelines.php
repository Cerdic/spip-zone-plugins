<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//espace prive
//affiche les articles et rubriques auxquels l'auteur est abonne (sur auteur_infos)
function zaboarticle_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {
		$legender_auteur_supp = recuperer_fond('prive/zaborubrique_fiche',array('id_auteur'=>$flux['args']['id_auteur']));
		$legender_auteur_supp .= recuperer_fond('prive/zaboarticle_fiche',array('id_auteur'=>$flux['args']['id_auteur']));
		$flux['data'] .= $legender_auteur_supp;
	}
	return $flux;
}

//affiche liste des articles et rubriques pour s'abonner dans le formulaire d'un auteur (un peu lourd...)
function zaboarticle_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		$zabo = recuperer_fond('prive/zaborubrique_fiche_modif',array('id_auteur'=>$flux['args']['id']));
		$zabo .= recuperer_fond('prive/zaboarticle_fiche_modif',array('id_auteur'=>$flux['args']['id']));

		$flux['data'] = preg_replace('%(<!--editer_abonnement-->)%is', '$1'."\n".$zabo, $flux['data']);
	}
	return $flux;
}

?>
