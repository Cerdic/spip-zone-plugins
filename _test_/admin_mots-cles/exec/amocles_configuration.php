<?php 

	// exec/amocles_configuration.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Amocles est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_amocles_configuration () {

//spip_log("exec_amocles_configuration() --", _AMOCLES_PREFIX);

  global $connect_statut
  , $connect_toutes_rubriques
  , $connect_id_auteur
  , $connect_id_rubrique
  , $spip_lang_left
  , $spip_lang_right
  ;

	include_spip('inc/presentation');
	include_spip('inc/meta');
	include_spip('inc/urls');
	include_spip('inc/utils');
	include_spip('inc/acces');
	include_spip('inc/amocles_api');
	
	if (!(($connect_statut == '0minirezo') && $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
	////////////////////////////////////
	// initialise les variables postées par le formulaire
	foreach(array(
			'btn_valider_admin', 'ajouter_admins', 'ajouter_auteurs'
		  , 'btn_valider_import', 'id_groupe'
		  , 'btn_valider_export', 'groupes_export'
		  , 'cherche_auteur', 'ids'
		) as $key) {
		$$key = _request($key);
	}
	$id_groupe = intval($id_groupe);
	
	$rubrique = "configuration";
	
	$separateur = "\t";

	$message_gauche = $message_erreur = "";
	
	////////////////////////////////////
	// valider un groupe entier (admins ou auteurs)
	if($btn_valider_admin) {
		$sql_where = "";
		if($ajouter_admins) {
			$sql_where = "statut='0minirezo'";
		}
		if($ajouter_auteurs) {
			$sql_where .= (!empty($sql_where) ? " OR " : "")."statut='1comite'";
		}
		if(!empty($sql_where)) {
			$sql_result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE $sql_where ORDER BY id_auteur");
			if(sql_result) {
				$ids = array();
				while($row = spip_fetch_array($sql_result)) {
					$ids[] = intval($row['id_auteur']);
				}
				if(count($ids)) {
					if(
						($ii = __plugin_lire_serialized_meta(_AMOCLES_META_PREFERENCES))
						&& isset($ii['admins_groupes_mots_ids'])
						) {
						$ii = array_values($ii['admins_groupes_mots_ids']);
						$ids = array_merge($ids, $ii);
						sort($ids);
						$ids = array_unique($ids);
					}
					__plugin_ecrire_key_in_serialized_meta('admins_groupes_mots_ids', $ids, _AMOCLES_META_PREFERENCES);
					__ecrire_metas();
					spip_log($ii." KEYWORDS ID(s) ADDED BY ID_AUTEUR #".$connect_id_auteur, _AMOCLES_PREFIX);
				}
			}
		}
	}

	////////////////////////////////////
	// importation ?
	if($btn_valider_import) {
		$message_gauche .= ""
			. _T(_AMOCLES_LANG."importation_");
			;
		$flag_fichier_ok = (count($_FILES) && is_array($fichier_import = $_FILES['fichier_import']) 
			&& !$fichier_import['error'] && is_readable($fichier_import['tmp_name']));
		$flag_destination_ok = ($id_groupe > 0);
		if($flag_fichier_ok && $flag_destination_ok) {

			$titre_groupe = amocles_titre_groupe_get($id_groupe);

			// récupère les mots-clés existants pour éviter les doublons
			$mots_cles_actuels = array();
			$sql_query = "SELECT titre FROM spip_mots";
			$sql_result = spip_query($sql_query);
			while($row = spip_fetch_array($sql_result)) {
				$ii = $mots_cles_actuels[] = strtolower($row['titre']);
			}
			
			// importe le fichier
			$contenu_fichier = file($fichier_import['tmp_name']);
			$nb_lignes = count($contenu_fichier);
			$sql_values = "";
			$nb_mots_trouves = $nb_mots_ajoutes = 0;
			for($ii = 0; $ii < $nb_lignes; $ii++) {
				$nouveau_mot = trim($contenu_fichier[$ii]);
				if(!empty($nouveau_mot) && !ereg("^[/#]", $nouveau_mot)) {
					list($titre, $descriptif, $texte) = explode($separateur, $nouveau_mot);
					if(!empty($titre)) {
						$nb_mots_trouves++;
						if(!in_array(strtolower($titre), $mots_cles_actuels)) {
							$nb_mots_ajoutes++;
							$sql_values .= "("._q($titre).","._q($descriptif).","._q($texte).",".$id_groupe.","._q($titre_groupe)."),";
						}
					} // end if
				}
			}
			$ok = "";
			if(!empty($sql_values)) {
				$sql_values = rtrim($sql_values, ",");
				$sql_query = "INSERT INTO spip_mots (titre,descriptif,texte,id_groupe,type) VALUES $sql_values";
//spip_log($sql_query, _AMOCLES_PREFIX);
				$ok = (spip_query($sql_query)) ? "" : _T('erreur').": ";
			}
			$total_si_different = ($nb_mots_ajoutes != $nb_mots_trouves) ? "/$nb_mots_trouves" : "";
			$message_gauche .= ""
				. "<ul style='margin:0;padding-left:0;list-style: square inside;'>\n"
				. "<li class='verdana2'>$titre_groupe... $ok<strong>$nb_mots_ajoutes</strong>$total_si_different</li>\n"
				. "</ul>\n"
				;
		}
		else {
			if(!$flag_fichier_ok) {
			// A oublié de sélectionner un fichier à importer
				$message_erreur .= _T(_AMOCLES_LANG."selectionnez_fichier_import");
			}
			if(!$flag_destination_ok) {
			// A oublié de sélectionner un groupe de destination
				$message_erreur .= _T(_AMOCLES_LANG."selectionnez_groupe_destination");
			}
			$message_gauche .= " <strong>"._T('erreur')."</strong>.";
		}
	}

	////////////////////////////////////
	// exportation ?
	if($btn_valider_export) {
		$message_gauche .= ""
			. _T(_AMOCLES_LANG."exportation_");
			;
		if($groupes_export && is_array($groupes_export) && count($groupes_export)) {
			$str_export = "";
			$total_mots = 0;
			$message_gauche .= "<ul style='margin:0;padding-left:0;list-style: square inside;'>\n";
			$strip_string = array("\r\n", "\n", "\r", $separateur);
			$sql_select = "titre,descriptif,texte";
			$items = explode(",", $sql_select);
			foreach($groupes_export as $id_groupe) {
				$titre_groupe = amocles_titre_groupe_get($id_groupe);
				if($sql_result = spip_query("SELECT titre,descriptif,texte FROM spip_mots WHERE id_groupe=$id_groupe")) {
					if(($ii = spip_num_rows($sql_result)) > 0) {
						while($row = spip_fetch_array($sql_result)) {
							foreach($items as $key) {
								// supprime les sauts de ligne (cas des <multi> ou texte trop long)
								$$key = str_replace($strip_string, "", $row[$key]);
							}
							$str_export .= "$titre$separateur$descriptif$separateur$texte\n";
						}
						$ok = ((empty($str_export)) ? " ?" : _T('pass_ok'));
						$total_mots += $ii;
					}
					else {
						$ok = _T('texte_vide');
					}
				}
				else {
					$ok = _T('erreur');
				}
				$message_gauche .= "<li class='verdana2'>$titre_groupe... <strong>$ok</strong></li>\n";
			}
			$message_gauche .= "</ul><br />\n";
			
			if(!empty($str_export)) {
				$date = date('Y-m-d_His');
				$str_export = ""
					. "# "._T(_AMOCLES_LANG."export_mots")."\n"
					. "# "._T('info_site').": ".$GLOBALS['meta']['nom_site']."\n"
					. "# "._T('forum_url')." ".$GLOBALS['meta']['adresse_site']."\n"
					. "# "._T('date').": $date\n"
					. "# "._T(_AMOCLES_LANG."total_export").$total_mots."\n"
					. "# charset: ".$GLOBALS['meta']['charset']."\n\n"
					. $str_export
					;
				$export_id=0;
				// envoie le fichier
				$filename = "mots_export-$date.txt";
				$content_type = "text/plain";
				if(
				// si trop gros, le compresse
					(strlen($str_export) > _AMOCLES_FILEMAXSIZE)
					&& function_exists('gzencode')
					) {
					$content_type = "application/x-gzip";
					$filename .= ".gz";
					$str_export = gzencode($str_export);
				}
				ob_clean();
				header("Content-type: $content_type");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				echo($str_export);
				exit(0);
			}
			else {
				$message_gauche .= _T(_AMOCLES_LANG."groupe_vide");
			}
		}
		else {
			$message_gauche .= _T(_AMOCLES_LANG."pas_de_groupe");
		}
	}
	
	if(!empty($message_gauche)) {
		$message_gauche = "<div class='verdana2' style='margin-top:1em;'>$message_gauche</div>\n";
	}

	if(!empty($message_erreur)) {
		$message_erreur = "<br />".__boite_alerte($message_erreur, true);
	}
	
	////////////////////////////////////
	// fin traitements

	
	spip_log("CONFIGURE ID_AUTEUR #$connect_id_auteur <<", _AMOCLES_PREFIX);
	
	$commencer_page = charger_fonction('commencer_page', 'inc');

	$groupes_mots = array();
	$sql_query = "SELECT id_groupe,titre,descriptif FROM spip_groupes_mots";
	if($sql_result = spip_query($sql_query)) {
		while($row = spip_fetch_array($sql_result)) {
			$groupes_mots[] = array(
				'id_groupe' => $row['id_groupe']
				, 'titre' => $row['titre']
				, 'descriptif' => $row['descriptif']
			);
		}
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////
	
	$page_result = ""
		. $commencer_page(_T(_AMOCLES_LANG."administration_mots_cles"), _AMOCLES_PREFIX)
		. "<br /><br /><br />\n"
		. gros_titre(_T(_AMOCLES_LANG."administration_mots_cles"), "", false)
		. barre_onglets($rubrique, _AMOCLES_PREFIX)
		. debut_gauche($rubrique, true)
		. __plugin_boite_meta_info(_AMOCLES_PREFIX, true)
		. $message_gauche
		;
	
	if (autoriser('creer','groupemots')) {
		$page_result .= ""
			. bloc_des_raccourcis(icone_horizontale(_T('icone_creation_groupe_mots')
				, generer_url_ecrire("mots_type","new=oui")
				, _DIR_PLUGIN_AMOCLES_IMG_PACK."groupe-mot-24.png", "creer.gif", false)
				)
			;
	}

	$page_result .= ""
		. creer_colonne_droite($rubrique, true)
		. debut_droite($rubrique, true)
		;

	////////////////////////////////////
	// Boite délégation admin des mots-clés
	if (($connect_statut == "0minirezo") && $connect_toutes_rubriques) {
		include_spip('inc/amocles_administrateurs_liste');
		$flag_editable = true;

		$page_result .= ""
			. debut_cadre_trait_couleur(_DIR_PLUGIN_AMOCLES_IMG_PACK."redacteurs-admin-24.png", true, "", _T(_AMOCLES_LANG."deleguer_admin"))
			. amocles_liste_admins_groupes_mots('auteur', 1, $flag_editable, $cherche_auteur, $ids, _T(_AMOCLES_LANG."administrateurs_mots_cles"), "amocles_configuration")
			. "<div  style='text-align: $spip_lang_left;' class='verdana2'>\n"
			. "<form name='form_adm_ajout' id='form_adm_ajout' method='post' action=''>\n"
			. "<fieldset style='margin-top:0.75em;border:1px solid gray;'>\n"
			. "<legend>"._T(_AMOCLES_LANG."options_")."</legend>\n"
			. "<div  style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
			. _T(_AMOCLES_LANG."options_texte")
			. "</div>\n"
			. "<div  style='text-align: $spip_lang_left;font-style: italic;margin-top:0.25em;' class='verdana2'>\n"
			. "<input type='checkbox' name='ajouter_admins' id='ajouter_admins' value='checkbox' />\n"
			. "<label for='ajouter_admins'>"._T(_AMOCLES_LANG."ajouter_tous_admins")."</label>\n"
			. "</div>\n"
			. "<div  style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
			. "<input type='checkbox' name='ajouter_auteurs' id='ajouter_auteurs' value='checkbox' />\n"
			. "<label for='ajouter_auteurs'>"._T(_AMOCLES_LANG."ajouter_tous_auteurs")."</label>\n"
			. "</div>\n"
			. "</fieldset>\n"
			// bouton valider
			. "<div style='text-align:$spip_lang_right;margin-top:0.5em;'>\n"
			. "<input type='submit' name='btn_valider_admin' value='"._T('bouton_valider')."' class='fondo' />\n"
			. "</div>\n"
			. "</form>\n"
			. "</div>\n"
			. fin_cadre_trait_couleur(true)
			;
	}
	
	////////////////////////////////////
	// Boite importation des mots-clés
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_AMOCLES_IMG_PACK."groupe-mot-24.png", true, _DIR_PLUGIN_AMOCLES_IMG_PACK."fleches-jaune-gauche-24.png", _T(_AMOCLES_LANG."importer_mots_cles"))
		. "<div  style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
		. _T(_AMOCLES_LANG."info_importer_mots_cles")
		. "</div>\n"
		. $message_erreur
		. "<div  style='text-align: $spip_lang_left;' class='verdana2'>\n"
		. "<form name='form_mots_import' id='form_mots_import' method='post' action='' enctype='multipart/form-data'>\n"
		. "<fieldset style='margin-top:0.75em;border:1px solid gray;'>\n"
		. "<legend>"._T(_AMOCLES_LANG."vos_groupes_")."</legend>\n"
		;
	foreach($groupes_mots as $value) {
		$descriptif = $value['descriptif'];
		$descriptif = (!empty($descriptif)) ? " ($descriptif)" : "";
		$titre = $value['titre'].$descriptif;
		$page_result .= ""
			. "<label title=\"$titre\" style='display:block;'>\n"
			. "<input type='radio' name='id_groupe' value='".$value['id_groupe']."' />\n"
			. $value['titre']
			. "</label>\n"
			; 
	}
	$page_result .= ""
		. "</fieldset>\n"
		. "<div style='text-align: center;'>\n"
		. "<input type='file' size='40' name='fichier_import' class='forml' style='margin:0.75em;' accept='text/*' />\n"
		. "</div>\n"
		// bouton valider
		. "<div style='text-align:$spip_lang_right'>\n"
		. "<input type='submit' name='btn_valider_import' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</form>\n"
		. "</div>\n"
		. fin_cadre_trait_couleur(true)
		;
	
	////////////////////////////////////
	// Boite exportation des mots-clés
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_AMOCLES_IMG_PACK."groupe-mot-24.png", true, _DIR_PLUGIN_AMOCLES_IMG_PACK."fleches-jaune-droite-24.png", _T(_AMOCLES_LANG."exporter_mots_cles"))
		. "<div  style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
		. _T(_AMOCLES_LANG."info_exporter_mots_cles")
		. "</div>\n"
		. "<div  style='text-align: $spip_lang_left;' class='verdana2'>\n"
		. "<form name='form_groupes_export' id='form_groupes_export' method='post' action=''>\n"
		. "<fieldset style='margin-top:0.75em;border:1px solid gray;'>\n"
		. "<legend>"._T(_AMOCLES_LANG."vos_groupes_")."</legend>\n"
		;
	foreach($groupes_mots as $value) {
		$descriptif = $value['descriptif'];
		$descriptif = (!empty($descriptif)) ? " ($descriptif)" : "";
		$titre = $value['titre'].$descriptif;
		$page_result .= ""
			. "<label title=\"$titre\" style='display:block;'>\n"
			. "<input type='checkbox' name='groupes_export[]' value='".$value['id_groupe']."' />\n"
			. $value['titre']
			. "</label>\n"
			; 
	}
	$page_result .= ""
		. "</fieldset>\n"
//		. "</div>\n"
		// bouton valider
		. "<div style='text-align:$spip_lang_right;margin-top:0.5em;'>\n"
		. "<input type='submit' name='btn_valider_export' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</form>\n"
		. "</div>\n"
		. fin_cadre_trait_couleur(true)
		;
	
	echo($page_result);
	echo __plugin_html_signature(_AMOCLES_PREFIX, true), fin_gauche(), fin_page();
	return(true);
}


?>