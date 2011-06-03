<?php

// action/fmp3_boite_son.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Fmp3.
	
	Fmp3 is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Fmp3 is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Fmp3; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Fmp3. 
	
	Fmp3 est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Fmp3 est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en même temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/fmp3_api_globales');
include_spip('inc/fmp3_api_prive');

/**
 * Boite d'édition mp3 pour les articles, rubriques et site de l'espace privé
 * @author Christian Paulus (paladin@quesaco.org)
 * @return AJAX content
 */
function action_fmp3_boite_son_dist () {
	
	global $connect_toutes_rubriques, $connect_login, $connect_statut, $spip_lang_rtl;
	
	if (!$connect_statut) {
		$auth = charger_fonction('auth', 'inc');
		$auth = $auth();
	}
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$args = $securiser_action();

	$supprimer = ($supprimer = _request('supprimer')) && ($supprimer == "oui");

	$autoriser_modifier = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;
	
	if (preg_match("/^(\w+),(\d+)$/", $args, $r)) 
	{
		$objet = $r[1];
		$id_objet = intval($r[2]);
		
		if(!$autoriser_modifier) {
			$autoriser_modifier = 
				($objet == 'rub')
				? autoriser('publierdans', 'rubrique', $id_objet)
				:	(
					($objet == 'art')
					? autoriser('modifier', 'article', $id_objet)
					: false
					)
				;
		}
	
		$preferences_default = unserialize(_FMP3_PREFERENCES_DEFAULT);
		$preferences_meta = fmp3_get_all_preferences();
		
		// Si les vars n'existent pas (erreur de config), prendre les valeurs par défaut 
		foreach($preferences_default as $key => $val) 
		{
			if(!isset($preferences_meta[$key])) {
				$preferences_meta[$key] = $val;
			}
		}
		
		$swap_couche = $result = $bouton_play = $error_msg = "";
		
		$son_dest = fmp3_chemin_son ($objet, $id_objet);
		
		$son_exists = file_exists($son_dest);
		
		if($supprimer && $autoriser_modifier) {
			unlink($son_dest);
			$son_exists = false;
		}
		
		if (!$son_exists && $autoriser_modifier)
		{
			/* propose formulaire de telechargement */
			
			// mettre en place le son si transmis par formulaire
			if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
			$source = (is_array($_FILES) ? array_pop($_FILES) : _request('source'));
			if($source) 
			{
				$sousaction2 = _request('sousaction2');
				if (!$source['size'])
				{
					$source = _request('source');
					$source_path = determine_upload() . $source;
					$source = array(
						'name' => $source
						, 'tmp_name' => $source_path
						, 'error' => file_exists($source_path)
						, 'size' => filesize($source_path)
						, 'type' => fmp3_mime_content_type($source_path)
					);
				}
				
				if($source['type'] == _FMP3_TYPEDOC)
				{
						
					$son_dest .= ".tmp";
					
					if ($sousaction2) 
					{
						// fichier dans upload/
						if(!$source = @copy($source_path, $son_dest)) {
							$error_msg .= 'error_ecriture';
						}
					}
					else {
						include_spip('inc/getdocument');
						// Intercepter une erreur a l'envoi
						if (check_upload_error($source['error'])) 
						{
							$source ="";
							$error_msg .= 'error_reception';
						}
						else {
							if(!deplacer_fichier_upload($source['tmp_name'], $son_dest)) {
								$error_msg .= 'error_ecriture';
							}
						}
					}
					if($source)
					{
						@rename ($son_dest, _DIR_LOGOS . $objet . $id_objet . '.mp3');
						if($source['size'] > 0) {
							echo("<html><head><title>-</title>"
							. "</head><body style='font-size:8pt; text-align:center;margin:0;padding:0'>
							" . _T('taille_octets', array('taille' => $source['size'])) . "
							</body></html>"
							);
						}
					}
					exit();
				}
				else {
					$error_msg .= 'error_format_incorrect'." (".$source['type'].") ";
				}
				if($error_msg) {
					echo("<script type='text/javascript'>
					//<![CDATA[
 					alert('"._T('fmp3:'.$error_msg)."');
					//]]>
					</script>
					");
					exit();
				}
			}
			else 
			{
				
				/* 
				 * construire la liste des fichiers dispo
				 */
				$reg = '[.](mp3)$';
				if ($GLOBALS['flag_upload']
					&& ($dir_ftp = determine_upload())
					&& ($fichiers = preg_files($dir_ftp, $reg))
				) {
					foreach ($fichiers as $f) {
						$f = substr($f, strlen($dir_ftp));
						$swap_couche .= "\n<option value='$f'>$f</option>";
					}
				}
			
				/* 
				 * si pas de fichier mp3 dans tmp/upload, rappel possibilité upload
				 */
				if (!$swap_couche) {
					if ($dir_ftp) {
						$swap_couche = _T('fmp3:info_installer_sons_dossier',
							array('upload' => '<strong>' . joli_repertoire($dir_ftp) . '</strong>'));
					}
				} 
				else {
					$swap_couche = "\n<div style='text-align: left'>" .
						_T('info_selectionner_fichier'
							, array('upload' => '<strong>' . joli_repertoire($dir_ftp) . '</strong>')) .
						":</div>" 
						. "\n<select name='source' class='verdana1 forml' size='1'>"
						. $swap_couche. "\n</select>\n" 
						. "\n<div align='" . $GLOBALS['spip_lang_right'] . "'>\n"
							. "<input name='sousaction2' type='submit' value='"
							. _T('bouton_choisir') 
							. "' class='fondo spip_xx-small' onclick='$(\"input[@name=fichier-son]\").val(\"\");' /></div>"
						;
				}
	
				/*
				 *  le premier champ : sélecteur de fichier
				 */
				$swap_couche = "\n" 
					. _T('fmp3:info_telecharger_nouveau_son') 
					. "<br />" 
					. "\n<input name='fichier-son' type='file' class='forml spip_xx-small' size='15' value='' />" 
					. "<div align='" .  $GLOBALS['spip_lang_right'] . "'>" 
					. "\n<input id='btn_telecharger' name='btn_telecharger' type='submit' value='" 
					. _T('bouton_telecharger')
					. "' class='fondo spip_xx-small' onclick='return($(this).son_valider_champ(\""._T('fmp3:error_fichier_manquant')."\"));' /></div>" 
					. $swap_couche
					;
			
			}
		}
		else if($son_exists)
		{
			/* 
			 * bouton ecouter son 
			 */
			$son_dest = url_absolue($son_dest);
			$bouton_play = fmp3_bouton_play ($son_dest);
			
			if($autoriser_modifier) 
			{
				$swap_couche .= ""
					. "<p class='verdana1 son-pied'>".basename($son_dest)."</p>"
					. "<p id='son-supprimer' class='son-pied'>[<a href='#' onclick='$(this).son_supprimer();'>" . _T('lien_supprimer') . "</a>]</p>\n"
					;
			}
		}
		
		if($autoriser_modifier)
		{
			$action_arg = $objet . "," . $id_objet;
			
			$action = generer_action_auteur("fmp3_boite_son", $action_arg);
	
			$swap_couche = ""
				. $bouton_play
				/*
				 * IFRAME pour permettre téléchargement
				 */
				. "<iframe id='hiddeniframe' name='hiddeniframe' src='about:blank'"
				. " frameborder='0' marginwidth='0' marginheight='0' scrolling='no'  "
				. ">"
				. "</iframe>\n"
				/*
				 * et le formulaire final
				 */
				. "<form action='$action' target='hiddeniframe' id='form-boite-son'"
				. " method='post' enctype='multipart/form-data' class='form_upload_icon' accept='"._FMP3_TYPEDOC."'"
				. " onsubmit='$(this).form_survey();'"
				. " style='display:none'>\n"
				. $swap_couche
				. "</form>\n"
				;

			$i1 = url_absolue(_DIR_IMG_PACK . "deplierhaut$spip_lang_rtl.gif");
			$t1 = _T('info_deplier');
			$i2 = url_absolue(_DIR_IMG_PACK . "deplierbas.gif");
			$t2 = _T('fmp3:info_replier');
			$triangle = ""
				. "<img id='triangle-son' src=\"" . $i1 . "\" alt=\"".$t1."\" title=\"".$t1."\" class='haut'
					onclick=\"$(this).swap_me('$i1', '$t1', '$i2', '$t2', '#form-boite-son');\" />"
				;
		
		}
		else {
			$triangle = "";
			$swap_couche = ""
				. $bouton_play
				;
		}
		
		switch($objet){
			case 'art': $info_titre = 'info_titre_article'; break;
			case 'rub': $info_titre = 'info_titre_rubrique'; break;
			case 'site': $info_titre = 'info_titre_site'; break;
			default: $info_titre = 'ERREUR !';
		}
		$info_titre = _T('fmp3:'.$info_titre);
		
		/*
		 * Mettre le tout dans sa boite
		 */	
		$result = ""
			. debut_cadre_relief(url_absolue(_DIR_FMP3_IMAGES)."fmp3-24.png", true)
			. "<div class='verdana1' style='text-align: center;'>\n"
			. "<img src='" . url_absolue(_DIR_IMG_PACK . "searching.gif") . "' alt='' id='son-loader' class='loader-visible' />\n"
			. "<div class='titre-boite-son'>"
			. $triangle
			. $info_titre
			. "</div>\n"
			. $swap_couche
			. "</div>"
			. fin_cadre_relief(true)
			;
		/*
		 * Envoyer le résultat
		 * */
		echo($result);
	} 
	else {
		fmp3_log("action_fmp3_boite_mots: $arg pas compris");
	}
	exit();
}

function fmp3_mime_content_type($f)
{
	if(function_exists("mime_content_type"))
	{
		return mime_content_type($f);
	}
	return(
		preg_match("/[.]mp3$/i", $f)
		? _FMP3_TYPEDOC
		: ""
	);
}
