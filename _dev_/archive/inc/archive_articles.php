<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');   // for spip presentation functions
include_spip('inc/config');   		// for spip presentation functions
include_spip('inc/layer');          // for spip layer functions
include_spip('inc/utils');          // for _request function
include_spip('inc/plugin');         // xml function

include_spip('base/abstract_sql');	//fonctions d'acces sql
include_spip('base/compat193');		//cr‚‚ … la voler les fonctions sql pour 192

//ajoute un div de selection archive oui/non
function archive_ajout_option($id_article) {
	//ne fait rien si le plugin n'est pas initialisé ie n'a pas de version
	if (!isset($GLOBALS['meta']['archive_version'])) {
		return "";
		exit;
	}

	//determine si l'artice est archivé ou non
	$array_archive = sql_fetch(spip_query("SELECT archive FROM spip_articles WHERE id_article=$id_article"));
	$archive = $array_archive['archive'];

	//genere le div à inserer dans le flux
	$flux = "";
	$flux .= debut_cadre('r');
		$flux .= '<form action="'.generer_url_ecrire("archive_update", "archive_action=update&objet_nature=article&id_objet=".$id_article."&etat_precedent=".$archive).'" method="post">';
			$flux .= "Article archiv&eacute; : ";
			$flux .= bouton_radio("archiver", true,"oui",$archive,"setvisibility('valider_archive', 'visible');");
			$flux .= bouton_radio("archiver", false,"non",!$archive,"setvisibility('valider_archive', 'visible');");
			$flux .= "<span id=\"valider_archive\" class=\"visible_au_chargement\">";
			$flux .= "<input type=\"submit\" class=\"fondo\" value=\"Valider\"/>";
			$flux .= "</span>";
		$flux .= "</form>";
	$flux .= fin_cadre('r');

	//$flux .="<div style=\"height: 5px;\"></div>";
	//$flux .= fin_cadre_formulaire();
	// retourne le flux mis à jour
	return $flux;
}
?>

