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

// http://doc.spip.org/@exec_admin_plugin
function exec_habillages_styles() {
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
		$lire_meta_styles = array($GLOBALS['meta']['habillages_styles']);
		ecrire_plugin_actifs($lire_meta_styles,'',$operation='enleve');
		//ecrire_meta('habillages_styles', '');
		ecrire_metas;
		lire_metas();
		$lire_meta_styles_modifs = _request('statusplug');
		ecrire_plugin_actifs($lire_meta_styles_modifs,'',$operation='ajoute');
		ecrire_meta('habillages_styles', _request('statusplug'));
		ecrire_metas;
	}

	if (isset($_GET['surligne']))
		$surligne = $_GET['surligne'];
	global $couleur_claire;
	debut_page(_T('habillages:icone_habillages_styles'), "configuration", "styles");
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
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_styles-48.png">';
	gros_titre(_T('habillages:icone_habillages_styles'));

	barre_onglets("habillages", "");
	
	debut_gauche();
	
	debut_boite_info();
	echo "<table><tr>";
	echo "<td colspan='2'>";
	echo _T('habillages:accueil_commentaire');
	echo "</td>";
	echo "<tr>";
	echo "<td colspan='2' class='bold_just'>";
	echo _T('habillages:accueil_squelettes');
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>";
	echo '<img src="'._DIR_PLUGIN_HABILLAGES.'/../img_pack/habillages_squelettes-22.png">';
	echo "</td>";
	echo "<td class='bold_just'>";
	echo "<a href='".generer_url_ecrire('habillages_squelettes')."'>"._T('habillages:lien_squelettes_on')."</a>";
	echo "</td>";
	echo "</tr>";
	echo "<td colspan='2' class='used'>";
	echo _T('habillages:accueil_styles');
	echo "</td>";
	echo "</tr>";
	echo "<td>";
	echo '<img src="'._DIR_PLUGIN_HABILLAGES.'/../img_pack/habillages_styles_bw-22.png">';
	echo "</td>";
	echo "<td class='used'>";
	echo _T('habillages:lien_styles_off');
	echo "</td>";
	echo "</tr>";
	echo "<td colspan='2' class='bold_just'>";
	echo _T('habillages:accueil_logos');
	echo "</td>";
	echo "</tr>";
	echo "<td>";
	echo '<img src="'._DIR_PLUGIN_HABILLAGES.'/../img_pack/habillages_images-22.png">';
	echo "</td>";
	echo "<td class='bold_just'>";
	echo "<a href='".generer_url_ecrire('habillages_images')."'>"._T('habillages:lien_logos_on')."</a>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	
	echo "<table border='0' cellspacing='0' cellpadding='5' width='100%'>";
	echo "<tr><td bgcolor='$couleur_foncee' background='' colspan='4'><b>";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
	echo _T('habillages:styles_titre')."</font></b></td></tr>";
	echo "<tr><td class='serif' colspan=4>";
	
		# Lire le squelette choisi.
		lire_metas();
		$lire_meta_squelettes = $GLOBALS['meta']['habillages_squelettes'];
		# Aller chercher le theme.xml du squelette selectionne, le lire...
		$theme_squelettes = _DIR_PLUGINS.$lire_meta_squelettes."/theme.xml";
		lire_fichier($theme_squelettes, $texte_squelettes);
		# ...et relever le prefixe.
		$arbre = parse_plugin_xml($texte_squelettes);
		$arbre = $arbre['theme'][0];
		$prefixe_theme = trim(applatit_arbre($arbre['prefixe']));
		
		# Chercher les fichiers theme.xml.
		$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");
			
			echo "<ul>";
			debut_boite_info();
			echo "<div style='background-color:$couleur_claire'>";
			echo "<input type='radio' name='statusplug' value=''";
			lire_metas();
			if ($GLOBALS['meta']['habillages_styles']=="defaut") {
				echo " checked='checked'";
				}
			echo ">";
			echo "<strong>Habillage par defaut</strong><label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label><br /><br /></div>";
			echo "<div style='float:right';><img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/capture.png' alt=description' class='preview' /></div>";
			# Ajouter : si theme.xml ne contient pas de theme.xml, on prend la description de plugin.xml. 
			# Il est necessaire que theme.xml puisse definir les caracteristiques d'un squelette, d'un style, 
			# d'un jeu d'images.
			echo "<small>Cet style est d'origine sur SPIP.</small><br /><br /><hr>";
			echo "<div class='auteur'>Collectif</div><hr>";
			echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/stable.png' />";
			echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
			fin_boite_info();
			echo "</ul>";
		
		# Pour chaque fichier theme.xml trouve, on releve le <type> et on ne garde que 
		# les styles pour les lister.
		foreach ($fichier_theme as $fichier){
			$arbre = "";
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			$type_theme = trim(applatit_arbre($arbre['type']));
			$squelettes_theme = array(trim(applatit_arbre($arbre['squelettes'])));
			$nom_dossier_theme = dirname ($fichier);
			$fichier_plugin_xml = $nom_dossier_theme."/plugin.xml";

			#! Mettre un affichage par defaut de style, ca plante le truc de ne pas le faire.
			
			echo generer_url_post_ecrire("habillages_styles");
			
				if (!is_file($fichier_plugin_xml)) {
					# Mettre dans la construction du dossier habillages-data (lorsque les themes se
					# telechargeront adopter le meme principe sur les dossiers telecharges) un refus
					# de telechargement/copie des dossiers qui n'ont pas de theme.xml *ni* de plugin.xml.
					# Ca evitera de mettre des gros pates dans les logs et on laissera l'ecriture dans 
					# ceux-ci aux etourdis qui personnaliseront leurs themes sans mettre de plugin.xml
					# dans le dossier de theme.
					spip_log("Le dossier ".$nom_dossier_theme." ne contient pas de fichier plugin.xml. Le plugin habillages ne peut pas gerer les elements de ce dossier.");
				}

				if ($type_theme=="styles" && $prefixe_theme == $squelettes_theme[0] && is_file($fichier_plugin_xml)) {
					echo "<ul>";
					habillages_affichage_styles($fichier_plugin_xml);
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
