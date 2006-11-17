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
# Changer les fonctions xml (cf. inc/xml).
include_spip('inc/vieilles_defs');

function exec_habillages_icones() {
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
	
	// mettre a jour le theme prive pour en profiter tout de suite
	  if (($c=_request('theme'))!==NULL){
	  	include_spip('inc/meta');
	  	ecrire_meta('habillages_icones',$c);
	  	ecrire_metas();
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
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_icones_prive-48.png">';
	gros_titre(_T('habillages:icone_habillages_icones'));

	echo barre_onglets("habillages", "");
	
	debut_gauche();
	debut_boite_info();
	echo "<div class='intro_grotitre'>";
	echo gros_titre(_T('habillages:icones_infos_titre'))."</div><br />";
	
	echo "<div class='intro'>";
	echo _T('habillages:icones_infos')."<br />";
	echo "</div>";
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;

	echo '<form action="'.generer_url_ecrire('habillages_icones').'" method="post">';
 	 
	echo "<ul>";
	debut_boite_info();
	echo "<table border='0' cellpadding='0' cellspacing='0' id='plaintab'>";
	echo "<tr><td style='background-color:$couleur_claire' class='titre_un habinput'>";
 	echo '<INPUT type=radio name="theme" value=""';
 	if ($GLOBALS['meta']['habillages_icones'] == "")
	 		echo "checked='checked'";
 	echo ">";
 	echo "</td><td style='background-color:$couleur_claire' class='titre_un'>";
 	echo "<strong>"._T('habillages:icones_defaut_titre')."</strong>";
 	echo "</td></tr>";
	echo "</table>";
 	fin_boite_info();
 	echo "<br />";

	# Chercher les fichiers theme.xml.
		$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");
		
		# Pour chaque fichier theme.xml trouve, on releve le <type> et on ne garde que 
		# les squelettes pour les lister.
		foreach ($fichier_theme as $fichier){
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			$type_theme = trim(applatit_arbre($arbre['type']));
			$nom_theme = applatit_arbre($arbre['nom']);
			$auteur_theme = applatit_arbre($arbre['auteur']);
			$version_theme = applatit_arbre($arbre['version']);
			$description_theme = applatit_arbre($arbre['description']);
			
			$c = dirname ($fichier)."/img_pack/";
			$cc  = dirname ($fichier);
				
				if ($type_theme=="icones_prive") {
					debut_boite_info();
					echo "<table border='0' cellpadding='0' cellspacing='0' id='plaintab'>";
					echo "<tr><td style='background-color:$couleur_claire' class='titre_un habinput'>";
    				echo '<INPUT type=radio name="theme" value="'.$c.'"';
		 			if ($GLOBALS['meta']['habillages_icones'] == $c)
			 		echo "checked='checked'";
    				echo ">";
    				echo "</td><td style='background-color:$couleur_claire' class='titre_un'>";
    				echo '<strong>'.$nom_theme.'</strong> version '.$theme_version;
    				echo "</td></tr>";
    				echo "<tr>";
					echo "<td colspan='2' class='cellule48' onmouseover='changestyle('bandeauaccueil', 'visibility', 'visible');'>";
					echo "<a href='#'>";
    				echo '<img src="'.$cc.'/capture.png" alt="" class="preview" />';
    				echo "</a>";
    				echo "</td></tr>";
					echo "</table>";
       				echo "<small>".propre($description_theme)."</small><br /><br /><hr>";
					echo "<div class='auteur'>".propre($auteur_theme)."</div><hr>";
				 	fin_boite_info();

				}
				
		}

	echo "</ul>";

	echo '<input type="submit" value="'._T('valider').'"/>';
	echo "</form>";

	fin_page();

}

?>
