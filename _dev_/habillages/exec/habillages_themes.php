<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('inc/actions');
include_spip('inc/habillages_plugins');
include_spip('inc/habillages_presentation');

// http://doc.spip.org/@exec_admin_plugin
function exec_habillages_themes() {
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
	
	if (_request('changer_plugin')=='oui'){
		lire_metas();
		$lire_meta_squelettes = array($GLOBALS['meta']['habillages_squelettes']);
		ecrire_plugin_actifs($lire_meta_squelettes,'',$operation='enleve');
		ecrire_meta('habillages_themes', 'defaut');
		ecrire_metas;
		lire_metas();
		$lire_meta_squelettes_modifs = array(_request('statusplug'));
		ecrire_plugin_actifs($lire_meta_squelettes_modifs,'',$operation='ajoute');
		ecrire_meta('habillages_squelettes', _request('statusplug'));
		ecrire_metas;
		lire_metas();
		echo $GLOBALS['meta']['plugin'];
	}

	if (isset($_GET['surligne']))
		$surligne = $_GET['surligne'];
	global $couleur_claire;
	global $couleur_foncee;
	debut_page(_T('habillages:icone_config_habillages'), "configuration", "habillages");
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
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_squelettes-48.png">';
	gros_titre(_T('habillages:icone_habillages_squelettes'));

	barre_onglets("habillages", "");
	
	debut_gauche();
	debut_boite_info();
	
	habillages_menu_navigation();
	
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	echo "<table border='0' cellspacing='0' cellpadding='5' width='100%'>";
	echo "<tr><td bgcolor='$couleur_foncee' background='' colspan='4'><b>";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
	echo _T('habillages:squelettes_titre')."</font></b></td></tr>";
	echo "<tr><td class='serif' colspan=4>";
	echo _T('habillages:squelettes_intro');
	
	echo generer_url_post_ecrire("habillages_themes");
	
		# Chercher les fichiers theme.xml.
		$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");
		
		# Pour chaque fichier theme.xml trouve, on releve le <type> et on ne garde que 
		# les squelettes pour les lister.
		foreach ($fichier_theme as $fichier){
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			$type_theme = trim(applatit_arbre($arbre['type']));
			$nom_dossier_theme = dirname ($fichier);
			$fichier_plugin_xml = $nom_dossier_theme."/plugin.xml";
			
				if (!is_file($fichier_plugin_xml)) {
					# Mettre dans la construction du dossier habillages-data (lorsque les themes se
					# telechargeront adopter le meme principe sur les dossiers telecharges) un refus
					# de telechargement/copie des dossiers qui n'ont pas de theme.xml *ni* de plugin.xml.
					# Ca evitera de mettre des gros pates dans les logs et on laissera l'ecriture dans 
					# ceux-ci aux etourdis qui personnaliseront leurs themes sans mettre de plugin.xml
					# dans le dossier de theme.
					spip_log("Le dossier ".$nom_dossier_theme." ne contient pas de fichier plugin.xml. Le plugin habillages ne peut pas gerer les elements de ce dossier.");
				}
				
				if ($type_theme=="squelettes" && is_file($fichier_plugin_xml)) {
					echo "<ul>";
					habillages_affichage_themes($fichier_plugin_xml);
					echo "</ul>";
				}
				
		}
	
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
