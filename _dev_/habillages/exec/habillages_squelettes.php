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

function exec_habillages_squelettes() {
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
		ecrire_meta('habillages_extras', '');
		ecrire_metas;
		lire_metas();
		$lire_meta_squelettes_modifs = array(_request('statusplug'));
		ecrire_plugin_actifs($lire_meta_squelettes_modifs,'',$operation='ajoute');
		ecrire_meta('habillages_squelettes', _request('statusplug'));
		ecrire_metas;
		lire_metas();
		
		# Si il y changement de squelettes, chercher si le squelette a des themes associes. si oui,
		# renseigner un champs meta pour afficher l'onglet "themes".
		if (_request('statusplug') == "dist") {
			ecrire_meta('habillages_is_themes', 'oui');
			ecrire_meta('habillages_is_extras', 'oui');
			ecrire_meta('habillages_prefixe_squel', 'dist');
			ecrire_metas;
		}
		else {
		lire_metas();
		$choix_squelettes = $GLOBALS['meta']['habillages_squelettes'];
		$xml_squelette = _DIR_PLUGINS.$choix_squelettes."/theme.xml";
		lire_fichier($xml_squelette, $texte_xml);
		$arbre_xml = parse_plugin_xml($texte_xml);
		$arbre_xml = $arbre_xml['theme'][0];
		$nom_theme = trim(applatit_arbre($arbre_xml['prefixe']));

		$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");
		
		foreach ($fichier_theme as $fichier){
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			$squelettes_theme = trim(applatit_arbre($arbre['squelettes']));
			$prefixe_theme = trim(applatit_arbre($arbre['prefixe']));
			$prefixe_type = trim(applatit_arbre($arbre['type']));
			
			# Si il y a des themes disponibles, on entre le squelettes (qui est aussi egal a son prefixe)
			# dans une liste de themes dispos afin de pouvoir mettre des icones sur la page squelettes.
			if ($prefixe_type == "themes") {
				lire_metas();
				$liste_themes_meta = $GLOBALS['meta']['habillages_liste_themes'];
				if (!eregi($squelettes_theme, $liste_themes_meta)) {
				ecrire_meta('habillages_liste_themes', $liste_themes_meta.",".$squelettes_theme);
				ecrire_metas;
				}
			}
			# Si il y a des extras disponibles, on entre le squelettes (qui est aussi egal a son prefixe)
			# dans une liste d'extras dispos afin de pouvoir mettre des icones sur la page squelettes.
			if ($prefixe_type == "extras") {
				lire_metas();
				$liste_extras_meta = $GLOBALS['meta']['habillages_liste_extras'];
				if (!eregi($squelettes_theme, $liste_extras_meta)) {
				ecrire_meta('habillages_liste_themes', $liste_extras_meta.",".$squelettes_theme);
				ecrire_metas;
				}
			}
			if ($nom_theme == $squelettes_theme) {
				ecrire_meta('habillages_is_themes', 'oui');
				ecrire_meta('habillages_is_extras', 'oui');
				ecrire_meta('habillages_prefixe_squel', $prefixe_theme);
				ecrire_metas;
			}
			else {
				ecrire_meta('habillages_is_themes', 'non');
				ecrire_meta('habillages_is_extras', 'non');
				ecrire_meta('habillages_prefixe_squel', '');
				ecrire_metas;
			}
		}
		}
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
	
	echo "<div class='intro_grotitre'>";
	echo _T('habillages:squelettes_titre_boitinfo')."</div><br />";
	
	echo "<div class='intro'>";
	echo _T('habillages:squelettes_intro')."<br /><br />";
	
	echo "<div class='intro_titre'>";
	echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/avance.png' />";
	echo _T('habillages:squelettes_avance_titre')."</div>";
	echo _T('habillages:squelettes_avance')."<br /><br />";
	
	echo "<div class='intro_titre'>";
	echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_themes-22.png' />";
	echo _T('habillages:squelettes_themes_titre')."</div>";
	echo _T('habillages:squelettes_themes')."<br /><br />";
	
	echo "<div class='intro_titre'>";
	echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_extras-22.png' />";
	echo _T('habillages:squelettes_extras_titre')."</div>";
	echo _T('habillages:squelettes_extras')."<br />";
	echo "</div>";
	
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	echo "<table border='0' cellspacing='0' cellpadding='5' width='100%'>";
	echo "<tr><td bgcolor='$couleur_foncee' background='' colspan='4'><b>";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
	echo _T('habillages:squelettes_titre')."</font></b></td></tr>";
	echo "<tr><td class='serif' colspan=4>";	
	echo generer_url_post_ecrire("habillages_squelettes");
		
		# Squelettes par defaut. On laisse la dist ou le squelette personnalise
		# de l'auteur du site prendre le dessus. Mettre une interface bateau, mais
		# essayer ensuite d'adapter l'interface au squelette qui est actif sur le
		# site par defaut, surtout si le squelette a ete personnalise.
		lire_metas();
		$squelettes = $GLOBALS['meta']['habillages_squelettes'];
		if ($squelettes == "" || $squelettes == "defaut") {
			$checked_defaut = " checked='checked'";
		}
		else if ($squelettes == "dist") {
			$checked_dist = " checked='checked'";
		}
		
		echo "<ul>";
		debut_boite_info();
		echo "<div style='background-color:$couleur_claire' class='titre_un'>";
		echo "<input type='radio' name='statusplug' value='defaut'$checked_defaut>";
		echo "<strong>"._T('habillages:squelettes_defaut_titre')."</strong><label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label><br /><br /></div>";
		echo "<small>"._T('habillages:squelettes_defaut_description')."</small><br /><br /><hr>";
		fin_boite_info();
		echo "<br />";
		# Attention : si l'utilisateur a personnalise un squelette (dans "squelettes" ou autre) ce ne sera
		# pas la dist qui s'affichera. A modifier donc pour que la dist s'affiche comme squelette par defaut
		# quand ce bouton est coche et si elle existe.
		debut_boite_info();
		echo "<div style='background-color:$couleur_claire' class='titre_un'>";
		echo "<input type='radio' name='statusplug' value='dist'$checked_dist>";
		echo "<strong>"._T('habillages:squelettes_dist_titre')."</strong><img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/avance.png' /> | <img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_themes-22.png' /> | <img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_extras-22.png' /><label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label><br /><br /></div>";
		echo '<div style="float:right";><img src="'._DIR_PLUGIN_HABILLAGES.'/../img_pack/capture_dist_bw.png" alt="" class="preview" /></div>';
		echo "<small>"._T('habillages:squelettes_dist_description')."</small><br /><br /><hr>";
		echo "<div class='auteur'>Collectif.<br />&copy; 2001 - 2006 - Distribue sous licence GNU/GPL</div><hr>";
		fin_boite_info();
		echo "</ul>";
	
		# Chercher les fichiers theme.xml.
		$fichier_theme = preg_files(_DIR_PLUGINS,"/theme[.]xml$");
		
		# Pour chaque fichier theme.xml trouve, on releve le <type> et on ne garde que 
		# les squelettes pour les lister.
		foreach ($fichier_theme as $fichier){
			lire_fichier($fichier, $texte);
			$arbre = parse_plugin_xml($texte);
			$arbre = $arbre['theme'][0];
			$nom_theme = applatit_arbre($arbre['nom']);
			$auteur_theme = applatit_arbre($arbre['auteur']);
			$etat_theme = applatit_arbre($arbre['etat']);
			$version_theme = applatit_arbre($arbre['version']);
			$description_theme = applatit_arbre($arbre['description']);
			$type_theme = trim(applatit_arbre($arbre['type']));
			$niveau_theme = trim(applatit_arbre($arbre['niveau']));
			$prefixe_theme = trim(applatit_arbre($arbre['prefixe']));
			
			$nom_dossier_theme = dirname ($fichier);
			$fichier_plugin_xml = $nom_dossier_theme."/plugin.xml";
			$chemin_plugin_complet = dirname($fichier_plugin_xml);
			$chemin_plugin_court = substr($chemin_plugin_complet, strlen(_DIR_PLUGINS));
			
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
					
					# Si le niveau de difficulte d'installation du squelette est renseigne, mettre les
					# icones de difficulte.
					if ($niveau_theme == "1") {
						$niveau = "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/avance.png' />";
					}
					
					if ($niveau_theme == "0" || $niveau_theme == "") {
						$niveau = "";
					}
					
					if (_request('exec')=='habillages_squelettes'){
						lire_metas();
						$lire_meta_habillages = array($GLOBALS['meta']['habillages_squelettes']);
					}
					
					if ($lire_meta_habillages[0] == $chemin_plugin_court) {
						$checked = " checked='checked'";
					}
					else {
						$checked = "";
					}
					
					debut_boite_info();
					echo "<div style='background-color:$couleur_claire' class='titre_un'>";
					echo "<input type='radio' name='statusplug' value='$chemin_plugin_court'$checked>";
					echo "<strong>".$nom_theme."</strong>(version ".$version_theme.") ";
					
					lire_metas();
					$liste_themes = $GLOBALS['meta']['habillages_liste_themes'];
					if (eregi($prefixe_theme, $liste_themes)) {
						echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_themes-22.png' /> | ";
					}
					$liste_extras = $GLOBALS['meta']['habillages_liste_extras'];
					if (eregi($prefixe_theme, $liste_extras)) {
						echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_extras-22.png' /> | ";
					}
					
					echo $niveau."<label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label></div>";
					# Laisser la possibilite de definir le nom et le chemin de la capure ecran
					# dans theme.xml.
					echo '<div style="float:right";>&nbsp;<br /><img src="'.$chemin_plugin_complet.'/capture.png" alt="" class="preview" /></div>';
					echo "<small>".propre($description_theme)."</small><br /><br /><hr>";
					echo "<div class='auteur'>".propre($auteur_theme)."</div><hr>";
					echo "<img src='"._DIR_PLUGIN_HABILLAGES."/../img_pack/".$etat.".png' />";
					echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
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
