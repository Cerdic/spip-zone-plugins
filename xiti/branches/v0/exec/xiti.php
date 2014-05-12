<?php
//==========================================================================================
//==                                   Plugin XITI                                     ==
//==                                     Version 0.1                                      ==
//==========================================================================================
?>
<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


// Définition du directory

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_XITI',(_DIR_PLUGINS.end($p)));

// Fichiers requis


// Module

function exec_xiti_dist() {
	global $connect_statut, $connect_toutes_rubriques;
	
	// Récupération des versions

	
	// Début de présentation
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('xiti:titre_xiti'), "xiti", "xiti");

	echo "<br /><br /><br />\n";
	echo gros_titre(sinon('', _L(_T('xiti:titre_xiti'))), '', false);
	debut_gauche();
		
	debut_droite();
	
	// Verification des droits d'accès
	if (($connect_statut != '0minirezo' && $connect_statut != '1comite')) {
		debut_cadre_relief ("../"._DIR_PLUGIN_PHPASSO."IMG/info.gif", '', '', _T('phpasso:message'),'','message');
		echo _T('avis_non_acces_page');
		fin_cadre_relief();
		exit;
	}
	
	if ($message<>"") {
		debut_cadre_relief ("../"._DIR_PLUGIN_PHPASSO."IMG/info.gif", '', '', _T('phpasso:message'),'','message');
		echo $message;
		fin_cadre_relief();
	}

	debut_cadre_trait_couleur("../"._DIR_PLUGIN_XITI."IMG/logo_xiti.gif", '', '', _T('xiti:intro_xiti'));
	
	echo "Vous &ecirc;tes sur la page de configuration du plugin XITI.<br>Si vous n'&ecirc;tes pas encore inscrit, il vous faut le faire sur le site de <a href='http://www.xiti.com/' target='_blank'>AT Internet</a>.<br>Il vous indiquer votre num&eacute;ro d'adh&eacute;rent &agrave; XITI free puis choisir le logo que vous d&eacute;sirez inclure.<br>N'oubliez pas d'inclure la balise #XITI dans vos pages. Vous pouvez ajouter le nom de la page {page} comme par exemple {#TITRE}";
	
	fin_cadre_trait_couleur();
	
	debut_cadre_trait_couleur("../"._DIR_PLUGIN_XITI."IMG/logo_xiti.gif", '', '', _T('xiti:form_xiti'));
	
	$inc_page=recuperer_fond('fonds/xiti');
	
	echo $inc_page;
	
	fin_cadre_trait_couleur();

	echo fin_gauche();
	echo fin_page(true); // On ferme la page pour avoir la version de spip bien placée

}
?>