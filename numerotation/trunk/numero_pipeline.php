<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function numero_affiche_droite($flux){
	if (in_array($flux['args']['exec'], array('rubriques', 'rubrique')) and autoriser('numeroter', 'rubrique', $flux['args']['id_rubrique'])){
		$out = "";
		$id_rubrique = $flux['args']['id_rubrique'];
		$out .= boite_ouvrir('', 'simple');
		$out .= "<h4 style='margin-bottom:0;'>Rubriques</h4>";
		$url = generer_action_auteur('renumeroter', "rubrique-$id_rubrique", self('&'));
		$out .= "<div><a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/numerote.gif' width='48' height='24' alt='Re-numeroter' /></a>";
		$url = generer_action_auteur('denumeroter', "rubrique-$id_rubrique", self('&'));
		$out .= "&nbsp;<a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/denumerote.gif' width='48' height='24' alt='Enlever la numerotation' /></a></div>";
		$out .= "<h4 style='margin-bottom:0;'>Articles</h4>";
		$url = generer_action_auteur('renumeroter', "article-$id_rubrique", self('&'));
		$out .= "<div><a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/numerote.gif' width='48' height='24' alt='Re-numeroter' /></a>";
		$url = generer_action_auteur('denumeroter', "article-$id_rubrique", self('&'));
		$out .= "&nbsp;<a href='$url'><img src='"._DIR_PLUGIN_NUMERO."img_pack/denumerote.gif' width='48' height='24' alt='Enlever la numerotation' /></a></div>";
		$out .= boite_fermer();
		$flux['data'].= $out;
	}
	return $flux;
}

?>
