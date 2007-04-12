<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('inc/actions');
include_spip('inc/habillages_presentation');

// http://doc.spip.org/@exec_admin_plugin
function exec_habillages_aide() {
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
	debut_page(_T('habillages:icone_habillages_icones'), "configuration", "icones");
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
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_aide-48.png">';
	gros_titre(_T('habillages:aide_habillages_icones'));

	echo barre_onglets("habillages", "");
	
	debut_gauche();
	debut_boite_info();
	echo "<div class='intro_grotitre'>";
	echo gros_titre(_T('habillages:aide_infos_titre'))."</div><br />";
	
	echo "<div class='intro'>";
	echo _T('habillages:aide_infos')."<br />";
	echo "</div>";
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	
	debut_boite_info();
	echo "<ul>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#INSTALLATION' target='_blank'>INSTALLATION</a></li>";
	echo "<br />";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#UTILISATION' target='_blank'>UTILISATION</a></li>";

	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Presentation-rapide-de-l-interface' target='_blank'> &nbsp;&nbsp;&nbsp;Pr&eacute;sentation rapide de l'interface</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Avant-de-demarrer-choix-des' target='_blank'> &nbsp;&nbsp;&nbsp;Avant de d&eacute;marrer : choix des gestionnaires (facultatif)</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Choisir-vos-squelettes' target='_blank'> &nbsp;&nbsp;&nbsp;Choisir vos squelettes</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Choisir-vos-themes' target='_blank'> &nbsp;&nbsp;&nbsp;Choisir vos th&egrave;mes</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Choisir-vos-extras' target='_blank'> &nbsp;&nbsp;&nbsp;Choisir vos extras</a></li>";

	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Choisir-les-icones-de-l-espace' target='_blank'> &nbsp;&nbsp;&nbsp;Choisir les ic&ocirc;nes de l'espace priv&eacute;</a></li>";
	echo "<br />";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#PERSONNALISATIONS' target='_blank'>PERSONNALISATIONS</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Principes-generaux-et-communs-et' target='_blank'> &nbsp;&nbsp;&nbsp;Principes g&eacute;n&eacute;raux et communs et mises en garde</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Personnaliser-vos-squelettes' target='_blank'> &nbsp;&nbsp;&nbsp;Personnaliser vos squelettes</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Personnaliser-vos-themes' target='_blank'> &nbsp;&nbsp;&nbsp;Personnaliser vos th&egrave;mes</a></li>";

	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Personnaliser-vos-extras' target='_blank'> &nbsp;&nbsp;&nbsp;Personnaliser vos extras</a></li>";
	echo "<li><a href='http://spip.graphismes.free.fr/spip.php?article14#Personnaliser-vos-icones' target='_blank'> &nbsp;&nbsp;&nbsp;Personnaliser vos ic&ocirc;nes</a></li>";
	echo "</ul>";
	fin_boite_info();

	fin_page();

}

?>
