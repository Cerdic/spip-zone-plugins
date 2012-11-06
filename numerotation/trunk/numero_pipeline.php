<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function numero_affiche_droite($flux){
	if (in_array($flux['args']['exec'], array('rubriques', 'rubrique')) and autoriser('numeroter', 'rubrique', $flux['args']['id_rubrique'])){
		$out = "";
		$id_rubrique = $flux['args']['id_rubrique'];
		$out .= boite_ouvrir('', 'simple');

		$out .= "<h4 style='overflow: hidden'>Rubriques<span style='float:right;'>";
		$out .= bouton_action(
			http_img_pack(find_in_theme("images/numerote-24.png"),"Re-numeroter"),
			generer_action_auteur('renumeroter', "rubrique-$id_rubrique", self('&')),
			"","","Re-numeroter"
		);
		$out .= bouton_action(
			http_img_pack(find_in_theme("images/denumerote-24.png"),"Enlever la numerotation"),
			generer_action_auteur('denumeroter', "rubrique-$id_rubrique", self('&')),
			"","","Enlever la numerotation"
		);

		$out .= "</span></h4><h4>Articles<span style='float:right;'>";
		$out .= bouton_action(
			http_img_pack(find_in_theme("images/numerote-24.png"),"Re-numeroter"),
			generer_action_auteur('renumeroter', "article-$id_rubrique", self('&')),
			"","","Re-numeroter"
		);
		$out .= bouton_action(
			http_img_pack(find_in_theme("images/denumerote-24.png"),"Enlever la numerotation"),
			generer_action_auteur('denumeroter', "article-$id_rubrique", self('&')),
			"","","Enlever la numerotation"
		);

		$out .= "</span></h4>";
		$out .= boite_fermer();
		$flux['data'].= $out;
	}
	return $flux;
}

?>
