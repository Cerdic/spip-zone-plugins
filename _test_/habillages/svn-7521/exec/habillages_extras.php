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

function exec_habillages_extras() {
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
		# Attendre un extra de plus pour gerer les choix multiples.
		$lire_meta_themes_modifs = array(_request('statusplug'));
		ecrire_plugin_actifs($lire_meta_themes_modifs,'',$operation='ajoute');
		ecrire_meta('habillages_extras', _request('statusplug'));
		ecrire_metas;
		lire_metas();
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
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_extras-48.png">';
	gros_titre(_T('habillages:icone_habillages_extras'));

	barre_onglets("habillages", "");
	
	debut_gauche();
	debut_boite_info();
	
	echo "<div class='intro_grotitre'>";
	echo gros_titre(_T('habillages:extras_titre_boitinfo'))."</div><br />";
	
	echo "<div class='intro'>";
	echo _T('habillages:extras_definition')."</div>";
	echo "</div>";
	
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	echo "<table border='0' cellspacing='0' cellpadding='5' width='100%'>";
	echo "<tr><td colspan='4' style='background-color:$couleur_foncee' class='bandeau_smooth'><b>";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
	echo _T('habillages:extras_titre')."</font></b></td></tr>";
	echo "<tr><td class='serif' colspan=4>";
	# Message d'introduction.
	echo _T('habillages:extras_intro')."<br /><br />";
	
	echo generer_url_post_ecrire("habillages_extras");
	
		# Chercher les fichiers theme.xml.
		$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");
		
		# Pour chaque fichier theme.xml trouve, on releve le <type> et on ne garde que 
		# les themes pour les lister.
		foreach ($fichier_theme as $fichier){
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			$nom_theme = applatit_arbre($arbre['nom']);
			$auteur_theme = applatit_arbre($arbre['auteur']);
			$etat_theme = trim(applatit_arbre($arbre['etat']));
			$version_theme = applatit_arbre($arbre['version']);
			$description_theme = applatit_arbre($arbre['description']);
			$type_theme = trim(applatit_arbre($arbre['type']));
			$niveau_theme = trim(applatit_arbre($arbre['niveau']));
			$squelettes_theme = trim(applatit_arbre($arbre['squelettes']));
			
			$nom_dossier_theme = dirname ($fichier);
			$fichier_plugin_xml = $nom_dossier_theme."/plugin.xml";
			$chemin_plugin_complet = dirname($fichier_plugin_xml);
			$chemin_plugin_court = substr($chemin_plugin_complet, strlen(_DIR_PLUGINS));
			
			lire_metas();
			$prefixe_squelettes = $GLOBALS['meta']['habillages_prefixe_squel'];
			
				if ($squelettes_theme == $prefixe_squelettes && !is_file($fichier_plugin_xml)) {
					# Mettre dans la construction du dossier habillages-data (lorsque les themes se
					# telechargeront adopter le meme principe sur les dossiers telecharges) un refus
					# de telechargement/copie des dossiers qui n'ont pas de theme.xml *ni* de plugin.xml.
					# Ca evitera de mettre des gros pates dans les logs et on laissera l'ecriture dans 
					# ceux-ci aux etourdis qui personnaliseront leurs themes sans mettre de plugin.xml
					# dans le dossier de theme.
					spip_log("Le dossier ".$nom_dossier_theme." ne contient pas de fichier plugin.xml. Le plugin habillages ne peut pas gerer les elements de ce dossier.");
				}
				
				if ($type_theme=="extras" && is_file($fichier_plugin_xml)) {
					echo "<ul>";
					
					# Si le niveau de difficulte d'installation du squelette est renseigne, mettre les
					# icones de difficulte.
					if ($niveau_theme == "1") {
						$niveau = "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/avance.png' /><img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/debutant.png' />";
					}
					
					if ($niveau_theme == "0" || $niveau_theme == "") {
						$niveau = "";
					}
					
					if (_request('exec')=='habillages_extras'){
						lire_metas();
						$lire_meta_habillages = array($GLOBALS['meta']['habillages_extras']);
					}
			
					if ($lire_meta_habillages[0] == $chemin_plugin_court) {
						$checked = " checked='checked'";
					}
					else {
						$checked = "";
					}

					if (isset($etat_theme))
				$etat = $etat_theme;
					switch ($etat) {
						case 'experimental':
							$couleur_txt = "CA2F2F";
							$titre_etat = _T('habillages:plugin_etat_experimental');
							break;
						case 'test':
							$couleur_txt = "E85600";
							$titre_etat = _T('habillages:plugin_etat_test');
							break;
						case 'stable':
							$couleur_txt = "149E06";
							$titre_etat = _T('habillages:plugin_etat_stable');
							break;
						default:
							$couleur_txt = "900B06";
							$titre_etat = _T('habillages:plugin_etat_developpement');
							break;
				}
				
					debut_boite_info();
					echo "<table border='0' cellpadding='0' cellspacing='0' id='plaintab'>";
					echo "<tr><td style='background-color:$couleur_claire' class='titre_un habinput'>";
					echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/".$etat.".png' />";
					echo "</td><td style='background-color:$couleur_claire' class='titre_un'>";
					echo "<input type='radio' name='statusplug' value='$chemin_plugin_court'$checked>";
					echo "</td><td style='background-color:$couleur_claire' class='titre_un'>";
					echo "<strong>".$nom_theme."</strong>(version ".$version_theme.") ";
					echo "</td><td style='background-color:$couleur_claire' class='titre_un habinput'>";
					echo $niveau."<label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label>";
					echo "</td></tr>";
					echo "</table>";
					# Laisser la possibilite de definir le nom et le chemin de la capure ecran
					# dans theme.xml.
					echo '<div style="float:right";><img src="'.$chemin_plugin_complet.'/capture.png" alt="" class="preview" /></div>';
					echo "<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br /><hr><br />";

					echo "<small>".propre($description_theme)."</small><br /><br /><hr>";
					echo "<div class='auteur'>".propre($auteur_theme)."</div><hr>";
					fin_boite_info();
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
