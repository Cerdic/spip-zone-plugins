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
function exec_habillages_config() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	if (isset($_GET['surligne']))
	$surligne = $_GET['surligne'];
	global $couleur_claire;
	debut_page(_T('habillages:icone_habillages_accueil'), "configuration", "habillages");
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
	gros_titre(_T('habillages:icone_habillages_accueil'));

	echo barre_onglets("habillages", "");
	
	debut_gauche();
	debut_boite_info();
	fin_boite_info();

	debut_droite();
    
	echo generer_url_post_ecrire("habillages_config");
	
	debut_cadre_couleur(_DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_config-22.png");	

	global $couleur_foncee;
	
	debut_boite_info();
	echo gros_titre(_T('habillages:tdb_titre'));
	echo _T('habillages:accueil_general');
	fin_boite_info();
	echo "<br />";

	debut_boite_info();

	echo "<table border='0' cellpadding='0' cellspacing='3' id='subtab' align='center'>";
	
	# Si il existe au moins un des deux dossiers "dist" ou "squelettes", on affiche un rappel
	# que la dist et/ou squelettes sont la et contiennent des donnees.
	$presence_dist = find_in_path('dist');
	$presence_squelettes = find_in_path('squelettes');
	
	if ($presence_dist || $presence_squelettes) {
    	echo "<tr><td colspan='2' style='background-color:$couleur_claire' id='hab_inputxt' class='hab_stitre'>";
    	echo "Squelettes déjà présents";
    	echo "</td></tr>";
    	
    	if ($presence_dist) {
        	echo "<tr><td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
        	echo "Squelettes natifs SPIP (la Dist)</td>";
        	echo "<td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
        	echo $presence_dist;
        	echo "</td></tr>";
	    }
	    
	    if ($presence_squelettes) {
        	echo "<tr><td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
        	echo "Squelettes personnalises</td>";
        	echo "<td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
        	echo $presence_squelettes;
        	echo "</td></tr>";
	    }
	    
    	echo "<tr><td colspan='2'>&nbsp;</td></tr>";
    }

   	# Si il existe au moins un fichier theme.xml dans le repertoire plugin, on affiche la liste des
   	# squelettes disponibles et on donne la possibilite de definir un repertoire personnalise pour 
   	# chaque jeu de squelettes. On garde ainsi le squelette original intact fourni par le plugin mais 
   	# on donne la possibilite de personnaliser ces squelettes.
   	$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");

        if ($fichier_theme) {
        	echo "<tr><td colspan='2' style='background-color:$couleur_claire' id='hab_inputxt' class='hab_stitre'>";
        	echo "Squelettes fournis avec habillages (indiquer le chemin du dossier)";
        	echo "</td></tr>";
        	
        	foreach ($fichier_theme as $fichier){
    			lire_fichier($fichier, $texte);
    			$arbre = parse_plugin_xml($texte);
    			$arbre = $arbre['theme'][0];
    			$nom_theme = applatit_arbre($arbre['nom']);
    			$type_theme = trim(applatit_arbre($arbre['type']));
    			$prefixe_theme = trim(applatit_arbre($arbre['prefixe']));
    			
    			$nom_dossier_theme = dirname ($fichier);
    			$fichier_plugin_xml = $nom_dossier_theme."/plugin.xml";
    			$chemin_plugin_complet = dirname($fichier_plugin_xml);
    			$chemin_plugin_court = substr($chemin_plugin_complet, strlen(_DIR_PLUGINS));
    			
            	echo "<tr><td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
            	echo $nom_theme."</td>";
            	echo "<td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
            	echo "Champ pour entrer le chemin des squelettes persos";
            	echo "</td></tr>";
    	    }
        
        	echo "<tr><td colspan='2'>&nbsp;</td></tr>";
    }
    
	echo "<tr><td colspan='2' style='background-color:$couleur_claire' id='hab_inputxt' class='hab_stitre'>";
	echo "Squelettes personnelles (indiquer le chemin du dossier)";
	echo "</td></tr>";
	
	echo "<tr><td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
	echo "Lister squelettes</td>";
	echo "<td style='background-color:$couleur_claire' id='hab_moitie' class='hab_fondclair'>";
	echo "Champ pour entrer le chemin des squelettes persos";
	echo "</td></tr>";

	echo "</table>";
	
	fin_boite_info();
	echo "<br />";
	
	echo "\n<input type='hidden' name='id_auteur' value='$connect_id_auteur' />";
	echo "\n<input type='hidden' name='hash' value='" . calculer_action_auteur("valide_plugin") . "'>";
	echo "\n<input type='hidden' name='changer_gestion' value='oui'>";

	echo "\n<p>";

	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</div>";
	echo "</form>";
	fin_cadre_couleur();
		
	fin_page();

}

?>
