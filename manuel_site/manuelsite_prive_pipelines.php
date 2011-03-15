<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function manuelsite_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="'.url_absolue(generer_url_public('manuelsite.css')).'" type="text/css" media="all" />' . "\n";
	return $flux;
}

function manuelsite_insert_head($flux){
	return $flux;
}

function manuelsite_body_prive(&$flux){

	$conf_manuelsite = lire_config('manuelsite');
	if($conf_manuelsite["id_article"] && (!isset($conf_manuelsite["afficher_bord_gauche"]) || $conf_manuelsite["afficher_bord_gauche"])) {
		$flux .= recuperer_fond('prive/manuelsite',array('id_article'=>$conf_manuelsite["id_article"])); 
	}
   return $flux;
}

function manuelsite_affiche_droite(&$flux){
	$conf_manuelsite = lire_config('manuelsite');
	//$flux['data'] .= "afficher_bord_gauche : ".$conf_manuelsite["afficher_bord_gauche"];
	if($conf_manuelsite["id_article"] && !$conf_manuelsite["afficher_bord_gauche"]) {
		$bouton = bouton_block_depliable(_T('manuelsite:titre_manuel'), false, "manuelsite_col");

		$cadre .= debut_cadre('r', find_in_path('images/manuelsite-24.png'), '', $bouton, '', '', false);
		$cadre .= debut_block_depliable(false,"manuelsite_col") 
			. '<div id="manuelsite_contenu">'
			. recuperer_fond('prive/inclure/manuelsite_article',array('id_article'=>$conf_manuelsite["id_article"]))
			. '</div>'
			. fin_block();
		$cadre .= fin_cadre_relief(true);

		$flux['data'] .= $cadre;

	}

   return $flux;

}
?>