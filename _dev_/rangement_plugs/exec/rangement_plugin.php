<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_RANGEMENT_PLUGS',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('inc/actions');
include_spip('inc/rangement_flock');

// http://doc.spip.org/@exec_admin_plugin
function exec_rangement_plugin() {
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

	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les plugin
	if (_request('changer_plugin')=='oui'){
		
		if (_request('famille')=='') {
			enregistre_modif_plugin();
		}
		
		else if (_request('famille')!='') {
			if (_request('statusplug') != '') {
			$plugins_modifies = _request('statusplug');
			$lire_meta_plugin = isset($GLOBALS['meta']['plugin'])?$GLOBALS['meta']['plugin']:'';
  				if (strlen($lire_meta_plugin)>0){
				ecrire_meta('plugin',$lire_meta_plugin.','.$plugins_modifies);
				ecrire_metas();
			}
		}
	}
	}
	else
		//verif_plugin();
	if (isset($_GET['surligne']))
		$surligne = $_GET['surligne'];
	global $couleur_claire;
	debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
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
	
	echo '<img src="' . _DIR_PLUGIN_RANGEMENT_PLUGS. '/../img_pack/rangement-48.png">';
	gros_titre(_T('icone_admin_plugin'));
	// barre_onglets("configuration", "plugin"); // a creer dynamiquement en fonction des plugin charges qui utilisent une page admin ?
	
	debut_gauche();
	debut_boite_info();
//	barre_onglets("plugins", "accueil");
	barre_onglets("plugins", "");
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	echo "<table border='0' cellspacing='0' cellpadding='5' width='100%'>";
	
	if (_request('famille') == "") {
		echo "<tr><td bgcolor='$couleur_foncee' background='' colspan='4'><b>";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
		echo _T('rangement:plugins_liste')."</font></b></td></tr>";
	
		echo "<tr><td class='serif' colspan=4>";
		echo _T('rangement:texte_presente_plugin');
	
		if (_request('famille')=='') {
			echo generer_url_post_ecrire("rangement_plugin");
		}
		
		else if (_request('famille')!='') {
			echo generer_url_post_ecrire("rangement_plugin", 'famille='._request('famille'));
		}
	
		echo "<ul>";
		
		# Ecrire les plugins deja actives.
		$meta_plugin = isset($GLOBALS['meta']['plugin'])?$GLOBALS['meta']['plugin']:'';
	  	if (strlen($meta_plugin)>0)
			$plugins_actifs = explode(",",$meta_plugin);
		else
			return array();
		
		if (is_array($plugins_actifs)){
		foreach ($plugins_actifs as $plugins_actives) {
	
			$fichier_xml = preg_files(_DIR_PLUGINS,"/$plugins_actives/plugin[.]xml$");
			foreach ($fichier_xml as $fichier){
				
				lire_fichier($fichier, $texte);
				$arbre = parse_plugin_xml($texte);
				$arbre = $arbre['plugin'][0];
				
				$nom_plugin = applatit_arbre($arbre['nom']);
				$auteur_plugin = applatit_arbre($arbre['auteur']);
				$etat_plugin = applatit_arbre($arbre['etat']);
				$version_plugin = applatit_arbre($arbre['version']);
				$description_plugin = applatit_arbre($arbre['description']);
				
		
					if (isset($etat_plugin))
					$etat = trim($etat_plugin);
					switch ($etat) {
						case 'experimental':
							$couleur_txt = "CA2F2F";
							$titre_etat = _T('rangement:plugin_etat_experimental');
							break;
						case 'test':
							$couleur_txt = "E85600";
							$titre_etat = _T('rangement:plugin_etat_test');
							break;
						case 'stable':
							$couleur_txt = "149E06";
							$titre_etat = _T('rangement:plugin_etat_stable');
							break;
						default:
							$couleur_txt = "900B06";
							$titre_etat = _T('rangement:plugin_etat_developpement');
							break;
					}
				
				debut_boite_info();
				echo "<input type='checkbox' name='statusplug_$plugins_actives' value='O' checked='checked'>";
				echo "<strong>".$nom_plugin."</strong>(version ".$version_plugin.")<label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label>";
				echo "<br /><hr>";
				echo "<small>".propre($description_plugin)."</small><br /><hr>";
				echo propre($auteur_plugin)."<br /><hr>";
				echo "<img src='"._DIR_PLUGIN_RANGEMENT_PLUGS."/../img_pack/".$etat.".png' />";
				echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
				fin_boite_info();
			}
		}
		}
			
		echo "</ul>";
	}
	
	else {
		echo "<tr><td bgcolor='$couleur_foncee' background='' colspan='4'><b>";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
		echo _T('rangement:plugins_liste_famille')."</font></b></td></tr>";
	
		echo "<tr><td class='serif' colspan=4>";
		echo _T('rangement:texte_presente_plugin_famille');
	
		if (_request('famille')=='') {
			echo generer_url_post_ecrire("rangement_plugin");
		}
		
		else if (_request('famille')!='') {
			echo generer_url_post_ecrire("rangement_plugin", 'famille='._request('famille'));
		}
	
		echo "<ul>";
		
		# Ecrire les plugins de la meme famille.
			
			$dossier_encours = _request('famille');
			$xml_encours = preg_files(_DIR_PLUGINS,"/$dossier_encours/plugin[.]xml$");
			$xml_racine = preg_files_plugs(_DIR_PLUGINS.$dossier_encours,"/plugin[.]xml$");
			
				if ($xml_encours) {
					$xml = $xml_encours;
				}
				else if ($xml_racine) {
					$xml = $xml_racine;
				}
			
			foreach ($xml as $fichier){
				$chemin_dossier = dirname ($fichier);
				if ($xml_encours) {
					$recherche_nom = eregi(_DIR_PLUGINS.'(.*)', $chemin_dossier, $nom_chemin);
					$dossier_plugin = $nom_chemin[1];
				}
				else if ($xml_racine) {
					$recherche_nom = eregi(_DIR_PLUGINS.'(.*)\/(.*)', $chemin_dossier, $nom_chemin);
					$dossier_plugin = $nom_chemin[1]."/".$nom_chemin[2];
				}
			
				lire_fichier($fichier, $texte);
				$arbre = parse_plugin_xml($texte);
				$arbre = $arbre['plugin'][0];
				
				$nom_plugin = applatit_arbre($arbre['nom']);
				$auteur_plugin = applatit_arbre($arbre['auteur']);
				$etat_plugin = applatit_arbre($arbre['etat']);
				$version_plugin = applatit_arbre($arbre['version']);
				$description_plugin = applatit_arbre($arbre['description']);
				
		
					if (isset($etat_plugin))
					$etat = trim($etat_plugin);
					switch ($etat) {
						case 'experimental':
							$couleur_txt = "CA2F2F";
							$titre_etat = _T('rangement:plugin_etat_experimental');
							break;
						case 'test':
							$couleur_txt = "E85600";
							$titre_etat = _T('rangement:plugin_etat_test');
							break;
						case 'stable':
							$couleur_txt = "149E06";
							$titre_etat = _T('rangement:plugin_etat_stable');
							break;
						default:
							$couleur_txt = "900B06";
							$titre_etat = _T('rangement:plugin_etat_developpement');
							break;
					}
					
					$plug_actif = "";
					$meta_plugin = isset($GLOBALS['meta']['plugin'])?$GLOBALS['meta']['plugin']:'';
					$plugin_actif = eregi($dossier_plugin, $meta_plugin, $plug_actif);
					
					if ($plug_actif[0] != "") {
						$checked = " value='$dossier_plugin' checked='checked'";
					}
					else {
						$checked = " value='$dossier_plugin'";
					}
				
				debut_boite_info();
				echo "<input type='checkbox' name='statusplug'$checked>";
				echo "<strong>".$nom_plugin."</strong>(version ".$version_plugin.")<label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label>";
				echo "<br /><hr>";
				echo "<small>".propre($description_plugin)."</small><br /><hr>";
				echo propre($auteur_plugin)."<br /><hr>";
				echo "<img src='"._DIR_PLUGIN_RANGEMENT_PLUGS."/../img_pack/".$etat.".png' />";
				echo "&nbsp;<small><strong><font COLOR='#".$couleur_txt."'>".$titre_etat."</font></strong></small><br />";
				fin_boite_info();
			}
			
		echo "</ul>";
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

function ergonomie_etat_plugin() {
	if (isset($etat_plugin))
	$etat = trim($etat_plugin);
	switch ($etat) {
		case 'experimental':
			$couleur_txt = "CA2F2F";
			$titre_etat = _T('rangement:plugin_etat_experimental');
			break;
		case 'test':
			$couleur_txt = "E85600";
			$titre_etat = _T('rangement:plugin_etat_test');
			break;
		case 'stable':
			$couleur_txt = "149E06";
			$titre_etat = _T('rangement:plugin_etat_stable');
			break;
		default:
			$couleur_txt = "900B06";
			$titre_etat = _T('rangement:plugin_etat_developpement');
			break;
	}
}

?>
