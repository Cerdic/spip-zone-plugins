<?php

#   +----------------------------------+
#    Nom du Filtre : licence   
#   +----------------------------------+
#    date : 11/04/2007
#    auteur :  fanouch - lesguppies@free.fr
#    version: 0.1
#    licence: GPL
#   +-------------------------------------+
#    Fonctions de ce filtre :
#	permet de lier une licence à un article 
#   +-------------------------------------+
# Pour toute suggestion, remarque, proposition d ajout
# reportez-vous au forum de l article :
# http://www.spip-contrib.net/fr_article2147.html
#   +-------------------------------------+

function licence_formulaire_article ($id_article, $id_licence){
	global $licence_licences;
	include_spip('inc/presentation');
	include_spip('inc/pv_base');
	
	$article = spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article=$id_article"));

	$out = "";

	$out .= debut_cadre_relief("../"._DIR_PLUGIN_LICENCE."/img_pack/licence_logo24.png", true, "");

	$out .= "<form action=\"".generer_url_ecrire(licence_ajouter)."\" method=\"POST\">
				<input type=\"hidden\" name=\"id_article\" value=\"".$id_article."\">
				<div align=\"center\"><b>Choix de la licence :</b>&nbsp;";
				
	$out .= "<select name=\"id_licence\" size=\"1\" class=\"fondl\" onchange=\"this.nextSibling.nextSibling.src='../dist/images/' + puce_statut(options[selectedIndex].value); setvisibility('valider_licence', 'visible');\">";
	$out .= "<option value=\"0\"".(($article["id_licence"] == 0)? "selected=\"selected\"":"")." style=\"background-color: white;\">Aucune licence</option>";
	foreach ($licence_licences as $key => $value)
	{
		$out .= "<option value=\"".$value["id"]."\"".(($article["id_licence"] == $value["id"])? "selected=\"selected\"":"")." style=\"background-color: white;\">".$value["name"]."</option>";
	}
	$out .= "</select> &nbsp; <img src=\"../dist/images/puce-verte.gif\" alt=\"\" class=\"puce\">  &nbsp;
				<span class=\"visible_au_chargement\" id=\"valider_licence\"><input value=\"Valider\" class=\"fondo\" type=\"submit\"></span></form></div>";
	
	$out .= fin_cadre_relief(true);
	
	return $out;
}

?>