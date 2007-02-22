<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('inc/actions');
include_spip('inc/iconifier');
include_spip('inc/habillages_plugins');
include_spip('inc/habillages_presentation');

// http://doc.spip.org/@exec_admin_plugin
function exec_habillages_logos() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	
	$surligne = "";

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	if (isset($_GET['surligne']))
		$surligne = $_GET['surligne'];
	global $couleur_claire;
	debut_page(_T('habillages:icone_habillages_images'), "configuration", "images");
	echo "<style type='text/css'>\n";
	echo <<<EOF
div.cadre-padding ul li {
	list-style:none ;
}
div.cadre-padding ul {
	padding-left:1em;
	margin:.5em 0 .5em 0;
}
div.cadre-padding ul ul {
	border-left:5px solid #DFDFDF;
}
div.cadre-padding ul li li {
	margin:0;
	padding:0 0 0.25em 0;
}
div.cadre-padding ul li li div.nomplugin, div.cadre-padding ul li li div.nomplugin_on {
	border:1px solid #AFAFAF;
	padding:.3em .3em .6em .3em;
	font-weight:normal;
}
div.cadre-padding ul li li div.nomplugin a, div.cadre-padding ul li li div.nomplugin_on a {
	outline:0;
	outline:0 !important;
	-moz-outline:0 !important;
}
div.cadre-padding ul li li div.nomplugin_on {
	background:$couleur_claire;
	font-weight:bold;
}
div.cadre-padding div.droite label {
	padding:.3em;
	background:#EFEFEF;
	border:1px dotted #95989F !important;
	border:1px solid #95989F;
	cursor:pointer;
	margin:.2em;
	display:block;
	width:10.1em;
}
div.cadre-padding input {
	cursor:pointer;
}
div.detailplugin {
	border-top:1px solid #B5BECF;
	padding:.6em;
	background:#F5F5F5;
}
div.detailplugin hr {
	border-top:1px solid #67707F;
	border-bottom:0;
	border-left:0;
	border-right:0;
	}
EOF;
	echo "</style>";

	echo "<br/><br/>";
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_images-48.png">';
	gros_titre(_T('habillages:icone_habillages_images'));

	echo barre_onglets("habillages", "");
	
	debut_gauche();
	debut_boite_info();
	habillages_menu_navigation();
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	echo generer_url_post_ecrire("habillages_images");
	global $couleur_foncee;
	
// 	$type_logos = array(
// 	'hierarchie' => 'rub',
// 	'rubriques' => 'rub',
// 	'articles' => 'art',
// 	'breves' => 'breve',
// 	'mots' => 'mot',
// 	'sites' => 'site',
// 	'auteurs' => 'aut'
// 	);

##################
	$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");
		
		# Pour chaque fichier theme.xml trouve, on releve le <type> et on ne garde que 
		# les images pour les lister.
		foreach ($fichier_theme as $fichier){
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			$type_theme = trim(applatit_arbre($arbre['type']));
			$nom_dossier_theme = dirname ($fichier);
			
				if ($type_theme=="logos_rubart") {
					$logos = preg_files(_DIR_PLUGINS.$nom_dossier_theme,"[.](gif|png|jpg)$");
					# AJouter gestion des tailles, bouton radio et mise en page.
					foreach ($logos as $logo) {
					echo "<img src='".$logo."' />";
					}
				}
				
		}
	
	# Dans un premier temps, on ne change que le logo du site, celui de toutes les rubriques
	# et celui de tous les articles. On verra pour sectoriser ensuite. On classe tout ca par 
	# themes sur une page d'accueil mais on laisse la possibilite de choisir logo de site,
	# logo de rubrique, et celui d'article separement.
	debut_boite_info();
	echo "<strong>"._T('habillages:titre_logos_themes')."</strong>";
	echo "<br /><br />";
	echo _T('habillages:texte_logos_themes');
	fin_boite_info();
	echo "<br /><br />";
	echo "Faire des pages separees<br />";
	echo "<strong>"._T('habillages:titre_logos_secteurs')."</strong>";
	echo "<br />";
	
	debut_boite_info();
	echo "<strong>"._T('habillages:titre_logos_site')."</strong>";
	echo "<br /><br />";
	echo _T('habillages:texte_logos_site');
	fin_boite_info();
	debut_boite_info();
	echo "<strong>"._T('habillages:titre_logos_rubrique')."</strong>";
	echo "<br /><br />";
	echo _T('habillages:texte_logos_rubrique');
	fin_boite_info();
	debut_boite_info();
	echo "<strong>"._T('habillages:titre_logos_article')."</strong>";
	echo "<br /><br />";
	echo _T('habillages:texte_logos_article');
	fin_boite_info();
	
	# Logo general du site siteon0.* et siteoff0.*
	//ecrire_meta('habillages_logo_site', 'siteon0');
	
	# Logo general de toutes les rubriques rubon0.* et ruboff0.*
	//ecrire_meta('habillages_logo_rubriques', 'rubon0');
	
	# Logo general de tous les articles arton0.* et artoff0.*
	//ecrire_meta('habillages_logo_articles', 'arton0');
	
	//ecrire_metas;
	
	
	echo "</table></div>\n";

	echo "\n<input type='hidden' name='id_auteur' value='$connect_id_auteur' />";
	echo "\n<input type='hidden' name='hash' value='" . calculer_action_auteur("valide_plugin") . "'>";
	echo "\n<input type='hidden' name='changer_plugin' value='oui'>";

	echo "\n<p>";

	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</div>";
	echo "</form></tr></table>\n";
	
	echo "<br />";

	fin_page();

}

?>
