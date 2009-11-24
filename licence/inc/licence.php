<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

function licence_formulaire_article ($id_article){
	include_spip('inc/presentation');
	include_spip('base/abstract_sql');
	$article = sql_fetsel("*","spip_articles","id_article=".intval($id_article));

	$out = "";
	$out .= debut_cadre_relief("../"._DIR_PLUGIN_LICENCE."/img_pack/licence_logo24.png", true, "");

	$securiser_action = charger_fonction('securiser_action','inc');
	$action = $securiser_action('licence_ajouter',$id_article,self());

	$out .= "<form action=\"$action\" method=\"POST\">"
				.form_hidden($action)
				."<div align=\"center\"><b>Choix de la licence :</b>&nbsp;";
				
	$out .= "<select name=\"id_licence\" size=\"1\" class=\"fondl\" onchange=\"jQuery('#valider_licence').css('visibility','visible');\">";
	$out .= "<option value=\"0\"".(($article["id_licence"] == 0)? "selected=\"selected\"":"").">Aucune licence</option>";
	foreach ($GLOBALS['licence_licences'] as $key => $value)
	{
		$out .= "<option value=\"".$value["id"]."\"".(($article["id_licence"] == $value["id"])? "selected=\"selected\"":"").">".$value["name"]."</option>";
	}

	$out .= "</select> &nbsp;
				<span class=\"visible_au_chargement\" id=\"valider_licence\"><input value=\"Valider\" class=\"fondo\" type=\"submit\"></span></form></div>";
	
	$out .= fin_cadre_relief(true);
	
	return $out;
}


$GLOBALS['licence_licences'] = array (
			"1" 	=> array(
				# nom de la licence
				"name" 	=> "Copyright",
				# numero d'identifiacation de la licence
				"id"		=> "1",
				# nom de l'icone de la licence (optionnel)
				# l'icone devra être placé dans le répertoire img_pack du plugin
				"icon"		=> "copyright-24.png",
				# Lien documentaire vers la licence (optionnel)
				"link"		=> "",
				# Description un peu plus détaillée de la licence
				"description" 	=> "&copy; copyright auteur de l'article"),
			"2" 			=> array(
				"name" 	=> "Gnu GPL",
				"id"		=> "2",
				"icon"		=> "gnu-gpl.png",
				"link"		=> "http://www.gnu.org/copyleft/gpl.html",
				"description" => "licence GPL"),
			"3" 			=> array(
				"name" 	=> "CC by",
				"id"		=> "3",
				"icon"		=> "cc-by.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité"),
			"4" 		=> array(
				"name" 	=> "CC by-nd",
				"id"		=> "4",
				"icon"		=> "cc-by-nd.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité pas de modification"),
			"5" 	=> array(
				"name" 	=> "CC by-nc-nd",
				"id"		=> "5",
				"icon"		=> "cc-by-nc-nd.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Pas d'Utilisation Commerciale Pas de Modification"),
			"6" 		=> array(
				"name" 	=> "CC by-nc",
				"id"		=> "6",
				"icon"		=> "cc-by-nc.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Pas d'Utilisation Commerciale"),
			"7" 	=> array(
				"name" 	=> "CC by-nc-sa",
				"id"		=> "7",
				"icon"		=> "cc-by-nc-sa.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Pas d'Utilisation Commerciale Partage des Conditions Initiales à l'Identique"),
			"8" 		=> array(
				"name" 	=> "CC by-sa",
				"id"		=> "8",
				"icon"		=> "cc-by-sa.png",
				"link"		=> "http://fr.creativecommons.org/",
				"description" => "Creative Commons - Paternité Partage des Conditions Initiales à l'Identique")
);

?>