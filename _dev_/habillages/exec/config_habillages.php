<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('inc/actions');

// http://doc.spip.org/@exec_admin_plugin
function exec_config_habillages() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	
	$gestion_squelettes = trim(_T('habillages:squelettes_base'));
	$gestion_themes = trim(_T('habillages:themes_base'));
	$gestion_extras = trim(_T('habillages:extras_base'));
	$gestion_logos = trim(_T('habillages:logos_base'));
	$gestion_icones = trim(_T('habillages:icones_base'));
	
	$surligne = "";

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	lire_metas();
	if (_request('changer_gestion')=='oui'){
		if (_request($gestion_squelettes) != "") {
			ecrire_meta('habillages_squelettes_on', 'oui');
			ecrire_metas;
		}
		else {
			ecrire_meta('habillages_squelettes_on', 'non');
			ecrire_metas;
		}
		
		if (_request($gestion_themes) != "") {
			ecrire_meta('habillages_themes_on', 'oui');
			ecrire_metas;
		}
		else {
			ecrire_meta('habillages_themes_on', 'non');
			ecrire_metas;
		}
		
		if (_request($gestion_extras) != "") {
			ecrire_meta('habillages_extras_on', 'oui');
			ecrire_metas;
		}
		else {
			ecrire_meta('habillages_extras_on', 'non');
			ecrire_metas;
		}
		
		if (_request($gestion_logos) != "") {
			ecrire_meta('habillages_logos_on', 'oui');
			ecrire_metas;
		}
		else {
			ecrire_meta('habillages_logos_on', 'non');
			ecrire_metas;
		}
		
		if (_request($gestion_icones) != "") {
			ecrire_meta('habillages_icones_on', 'oui');
			ecrire_metas;
		}
		else {
			ecrire_meta('habillages_icones_on', 'non');
			ecrire_metas;
		}
	}

	if (isset($_GET['surligne']))
		$surligne = $_GET['surligne'];
	global $couleur_claire;
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
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_icone-48.png">';
	gros_titre(_T('habillages:icone_config_habillages'));

	barre_onglets("habillages", "");
	
	debut_gauche();
	debut_boite_info();
	echo _T('habillages:accueil_infos');
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	
	lire_metas();
	$habillages_squelettes = basename($GLOBALS['meta']['habillages_squelettes']);
	$habillages_styles = basename($GLOBALS['meta']['habillages_couleurs']);
	//$habillages_logos = basename($GLOBALS['meta']['habillages_logos']);
	
	echo _T('habillages:accueil_general');
	if ($habillages_squelettes != "") {
	echo "<br />";
	echo "<br />";
	echo _T('habillages:accueil_general_squelettes')." ".$habillages_squelettes." [Capture]";
	echo "&nbsp;<a href='".generer_url_ecrire('habillages_squelettes')."'>Modifier</a>";
	}
	if ($habillages_styles != "") {
	echo "<br />";
	echo "<br />";
	echo _T('habillages:accueil_general_styles')." ".$habillages_styles." [Capture]";
	echo "&nbsp;<a href='".generer_url_ecrire('habillages_styles')."'>Modifier</a>";
	}
	echo "<br />";
	echo "<br />";
	echo _T('habillages:accueil_general_logos');
	echo "<br />";
	echo "<br />";
	echo _T('habillages:accueil_general_maintenance');
	echo "<br />";
	echo "<br />";
	
	echo "<a href='".generer_url_ecrire('admin_lang', 'module=habillages')."'>Modifier les textes</a><br /><br />";
	
	# Etablir les cases qui sont checkees.
	lire_metas();
	if ($GLOBALS['meta']['habillages_squelettes_on'] == "non") {
	$checked_skel = "";
	$able_themes = " disabled";
	$able_extras = " disabled";
	}
	else {
	$checked_skel = " checked='checked'";
	}
	if ($GLOBALS['meta']['habillages_themes_on'] == "non") {
	$checked_themes = "";
	}
	else {
	$checked_themes = " checked='checked'";
	}
	if ($GLOBALS['meta']['habillages_extras_on'] == "non") {
	$checked_extras = "";
	}
	else {
	$checked_extras = " checked='checked'";
	}
	if ($GLOBALS['meta']['habillages_logos_on'] == "non") {
	$checked_logos = "";
	}
	else {
	$checked_logos = " checked='checked'";
	}
	if ($GLOBALS['meta']['habillages_icones_on'] == "non") {
	$checked_icones = "";
	}
	else {
	$checked_icones = " checked='checked'";
	}
	
	if ($GLOBALS['meta']['habillages_squelettes_on'] == "oui") {
	$checked_themes = " checked='checked'";
	$checked_extras = " checked='checked'";
	}
	
	echo generer_url_post_ecrire("config_habillages");
	debut_boite_info();
	echo "<div style='background-color:$couleur_claire' class='titre_un'>";
	echo _T('habillages:manager_plugin');
	echo "</div>";
	echo "<input type='checkbox' name='".$gestion_squelettes."' value='".$gestion_squelettes."'$checked_skel> "._T('habillages:squelettes_base_acc')."<br />";
	echo "<ul>";
	echo "<input type='checkbox' name='".$gestion_themes."' value='".$gestion_themes."'$checked_themes$able_themes> "._T('habillages:themes_base_acc')."<br />";
	echo "<input type='checkbox' name='".$gestion_extras."' value='".$gestion_extras."'$checked_extras$able_extras> "._T('habillages:extras_base_acc')."<br />";
	echo "</ul>";
	echo "<input type='checkbox' name='".$gestion_logos."' value='".$gestion_logos."'$checked_logos> "._T('habillages:logos_base_acc')."<br />";
	echo "<input type='checkbox' name='".$gestion_icones."' value='".$gestion_icones."'$checked_icones> "._T('habillages:icones_base_acc')."<br />";
	fin_boite_info();
	
	echo "\n<input type='hidden' name='id_auteur' value='$connect_id_auteur' />";
	echo "\n<input type='hidden' name='hash' value='" . calculer_action_auteur("valide_plugin") . "'>";
	echo "\n<input type='hidden' name='changer_gestion' value='oui'>";

	echo "\n<p>";

	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</div>";
	echo "</form>";
	fin_page();

}

?>
