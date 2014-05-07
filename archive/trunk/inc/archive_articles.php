<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');   // for spip presentation functions
include_spip('inc/config');   		// for spip config functions
include_spip('inc/layer');          // for spip layer functions
include_spip('inc/utils');          // for _request function
include_spip('inc/plugin');         // xml function

include_spip('base/abstract_sql');	//fonctions d'acces sql
include_spip('base/compat193');		//cr�� � la voler les fonctions sql pour 192

//ajoute un div de selection archive oui/non
function archive_ajout_option($id_article) {
	//ne fait rien si le plugin n'est pas initialis� ie n'a pas de version
	if (!isset($GLOBALS['meta']['archive_version'])) {
		return "";
		exit;
	}

	//determine si l'artice est archiv� ou non
	$array_archive = sql_fetch(spip_query("SELECT archive FROM spip_articles WHERE id_article=$id_article"));
	$archive = $array_archive['archive'];

	//genere le div � inserer dans le flux
	$flux = "";
	$flux .= debut_cadre('e'); //MaRRocK : changement pour le style du cadre-e 
		$flux .= '<form action="'.generer_url_ecrire("archive_update", "objet_nature=article&id_objet=".$id_article).'" method="post">';
			$flux .= "<strong>Article archiv&eacute; : </strong>"; //MaRRocK : ajout de la balise <strong></strong>
			$flux .= bouton_radio("archiver", true,"oui",$archive,"
				if ($(this).attr('CHECKED')== 'CHECKED') {
					$('#valider_archive').css('visibility','hidden');
				} else {
					$('#valider_archive').css('visibility','visible');
				};"
			);
			$flux .= bouton_radio("archiver", false,"non",!$archive,"
				if ($(this).attr('CHECKED') == 'CHECKED') {
					$('#valider_archive').css('visibility','hidden'); 
				} else {
					$('#valider_archive').css('visibility','visible'); 
				};"
			);
			$flux .= "<span id=\"valider_archive\" class=\"visible_au_chargement\">";
			$flux .= "<input type=\"submit\" class=\"fondo\" value=\"Valider\"/>";
			$flux .= "</span>";
		$flux .= "</form>";
	$flux .= fin_cadre('e'); //MaRRocK : changement pour le style du cadre-e 

	return $flux;
}
?>

