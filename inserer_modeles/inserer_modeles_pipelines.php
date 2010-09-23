<?php

// On passe pour le moment via affiche_droite
// Mais envisager dans le futur une intיgration au porte plume
function inserer_modeles_affiche_droite($flux){
	if (in_array($flux['args']['exec'],array('articles_edit','breves_edit','rubriques_edit','mots_edit'))) {
		include_spip('inc/inserer_modeles');
		if (count(inserer_modeles_lister_formulaires_modeles())>0)
			$flux['data'] .= recuperer_fond('prive/inserer_modeles',$flux['args']);
	}
	return $flux;
}

?>