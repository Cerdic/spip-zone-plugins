<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
function spip_visuels_affiche_milieu($flux){
	if ($flux["args"]["exec"] == "article") {
		$objet = $flux["args"]["exec"];
		$id_objet = $flux["args"]["id_article"];
		
		$flux["data"] .=  recuperer_fond('prive/objets/contenu/portfolio_visuel',array('objet'=>$objet,'id_objet'=>$id_objet));
	}
	if ($flux["args"]["exec"] == "rubrique") {
		$objet = $flux["args"]["exec"];
		$id_objet = $flux["args"]["id_rubrique"];
		
		$flux["data"] .=  recuperer_fond('prive/objets/contenu/portfolio_visuel',array('objet'=>$objet,'id_objet'=>$id_objet));
	}
	return $flux;
}