<?php

// inc/raper_api_globales.php

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of RaPer.
	
	RaPer is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	RaPer is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with RaPer; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de RaPer. 
	
	RaPer est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	RaPer est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spe'cifique. Reportez-vous a' la Licence Publique Ge'ne'rale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/utils');
include_spip('inc/metas');

function raper_spip_est_inferieur_193 () {
	static $is_inf;
	if($is_inf === NULL) {
		$is_inf = version_compare($GLOBALS['spip_version_code'],'1.9300','<');
	}
	return($is_inf);
}

if(raper_spip_est_inferieur_193() && !defined("_COMPAT_CFG_192")) {
	include_spip('inc/raper_api_abstract_sql');
}

/*
 * Ecrire dans un fichier
 * @return 
 * @param $filename string
 * @param $data string
 */
function raper_file_write_contents ($filename, $data) {
	if ($f = @fopen($filename, 'w')) {
		$bytes = fwrite($f, $data);
		fclose($f);
	}
	return($bytes);
}

/*
 * Message dans le journal
 * @return 
 * @param $message string
 */
function raper_log ($message) {
	spip_log($message.$flag, _RAPER_PREFIX);
	return(true);
}

/*
 * Signaler les actions en log
 * @return 
 * @param $key Object
 * @param $action Object
 */
function raper_log_action_raccourci ($key, $action) {
	raper_log("id_raccourci #$key $action by id_auteur #".$GLOBALS['auteur_session']['id_auteur']);
}

/*
 * Les erreurs SQL dans le journal
 * @return 
 * @param $message string en general, le nom de la fonction appelante
 */
function raper_sql_err_log ($message) {
	spip_log($message . "DB ERROR: [" . spip_sql_errno() . "] " . spip_sql_error());
	return(true);
}

// 
function raper_ecrire_metas () {
	if(raper_spip_est_inferieur_193()) { 
		include_spip("inc/meta");
		ecrire_metas();
	}
	return(true);
}

/*
 * Lire les preferences enregistrees dans la table spip_meta
 * @return array les preferences
 * @param $forcer bool[optional] true pour forcer la lecture dans la base
 */
function raper_lire_preferences ($forcer = false) {
	static $prefs;

	if($forcer || ($prefs === null)) {
		global $meta;
		$prefs = ($p = $meta[_RAPER_META_PREFS]) ? unserialize($p) : array();
		$prefs['raccourcis'] = raper_lire_fichiers_lang_raper();
//raper_log("nb raccourcis lus " . count($prefs['raccourcis']));
		$raper_defaut = raper_preferences_defaut();
		if(!$prefs || (count($prefs) != count($raper_defaut))) {
			// si manquantes ou incompletes, corriger et enregistrer
			raper_log("correction preferences " . count($prefs) . " " . count($raper_defaut));
			$prefs = raper_ecrire_preferences ($prefs);
		}
	}
	return($prefs);
}

/*
 * Ecrire les preferences dans la table spip_meta
 * @return array
 * @param $cur_prefs array
 */
function raper_ecrire_preferences ($cur_prefs) {

	if(!$cur_prefs) $cur_prefs = array();
	$new_prefs = array();

	// enregistrer d'abord les local_* du raper
	if(count($cur_prefs['raccourcis'])) {
		if(is_writable(_DIR_RAPER_LANGUES)) {
			include_spip('inc/raper_api_prive');
			$langues_array = explode(",", raper_langues_selection());
			$is_multilingue = (count($langues_array) > 1);
			$version = raper_meta_plugin_version();
			$revision = ($ii = raper_plugin_revision()) ? "[$ii]" : "";
			foreach($langues_array as $lang) {
				// si multilingue, extraire les valeur respectives pour les fichiers local_*
				if($is_multilingue) {
					global $spip_lang;
					$tmp_lang = false;
					if($spip_lang != $lang) {
						$tmp_lang = $spip_lang;
						$spip_lang = $lang;
					}
					$raccourcis = array();
					foreach($cur_prefs['raccourcis'] as $key => $value) {
						$raccourcis[$key] = trim(extraire_multi($value));
					}
					if($tmp_lang) $spip_lang = $tmp_lang;
				}
				// si pas multilingue, juste recopier le tableau
				else {
					$raccourcis = $cur_prefs['raccourcis'];
					
				}
				// preparer le tableau pour le fichier lang_*
				$ii = "";
				ksort($raccourcis);
				foreach($raccourcis as $key => $value) {
					$ii .= "\n'$key'=>\"$value\",";
				}
				$raccourcis = rtrim($ii, ",");
				
				// enregistrer le fichier local_*
				$filename = _DIR_RAPER_LANGUES . _RAPER_LANG_FILENAME_PREFIX . "_" . $lang . ".php";
				if(!raper_file_write_contents ($filename
					, ""
					. "<?php\n"
					. "// filename:" . $filename . "\n"
					. "// raper_version:" . $version . $revision . "\n"
					. "// modified: " . date("Y-m-d H:i:s") . "\n"
					. "if (!defined(\"_ECRIRE_INC_VERSION\")) return;\n"
					. "\$GLOBALS[\$GLOBALS['idx_lang']] = array("
					. $raccourcis
					. "\n);\n"
					. "?>"
				)) {
					raper_log("error: " . $filename . "not writable");
				}
			}
		}
		else raper_log("error: " . _DIR_RAPER_LANGUES . "not writable");
	}
	else {
		// pas de raccourcis ? Supprimer les fichiers local_* du raper
		raper_effacer_fichiers_raper_local();
	}
	
	// verifier les prefs. Placer celle par defaut si manquante.
	foreach(raper_preferences_defaut() as $key => $value) {
		if(($key == 'raccourcis')) {
			// ne pas enregistrer les raccourcis dans les metas
			$new_prefs[$key] = array();
			if(!isset($cur_prefs[$key])) $cur_prefs[$key] = array();
		} 
		else {
			$cur_prefs[$key] = $new_prefs[$key] = isset($cur_prefs[$key]) ? $cur_prefs[$key] : $value;
		}
	}
	ecrire_meta(_RAPER_META_PREFS, serialize($new_prefs));
	raper_ecrire_metas();
	
	return($cur_prefs);
}

/*
 * Les preferences par defaut
 * @return array
 */
function raper_preferences_defaut () {
	static $defaut;
	if($defaut === null) $defaut = unserialize(_RAPER_DEFAULT_VALUES_ARRAY);
	return($defaut);
}

/*
 * Effacer les local_* du raper (desinstaller plug-in ou prefs raccourcis vides)
 * @return 
 */
function raper_effacer_fichiers_raper_local () {
	if(is_writable(_DIR_RAPER_LANGUES)) {
		if ($dh = opendir(_DIR_RAPER_LANGUES)) {
			$unlink_files = array();
			while (($file = readdir($dh)) !== false) {
				if(preg_match("|"._RAPER_LANG_FILENAME_PREFIX."_([a-z]+)\.php|", $file, $matches)) {
					$unlink_files[] = _DIR_RAPER_LANGUES . $file;
				}
			}
			closedir($dh);
			foreach($unlink_files as $filename) {
				raper_log("unlink $filename : " . (unlink($filename) ? "DONE" : "ERROR"));
			}
		}
	}
	else {
		raper_log("error: " . _DIR_RAPER_LANGUES . "not writable");
		return(false);
	}
	return(true);
}

/*
 * Charger les raccourcis des langues du raper si existent
 * @return array
 * @param $prefs array
 */
function raper_lire_fichiers_lang_raper () {
//raper_log("raper_lire_fichiers_lang_raper()");

	if(file_exists($dir = _DIR_RAPER_LANGUES) && is_dir($dir)) {
		
		if ($dh = opendir($dir)) {
			
			// les langues du site
			$langues_selection = explode(",", raper_langues_selection());
			
			// sauvegarder les langues deja chargees
			$idx_lang_normal = $GLOBALS['idx_lang'];
			
			// creer un index temp pour charger les trads du raper
			$idx_lang_surcharge = $GLOBALS['idx_lang'].'_temporaire';
			$GLOBALS['idx_lang'] = $idx_lang_surcharge;
			
			$raccourcis = array();
			
			while (($file = readdir($dh)) !== false) {
				if(preg_match("|"._RAPER_LANG_FILENAME_PREFIX."_([a-z]+)\.php|", $file, $matches)) {
					
					$lang = $matches[1];
					// ne charger que les langues souhaitees
					if(in_array($lang, $langues_selection)) {
						
						include(_DIR_RAPER_LANGUES . $file);
						$raccourcis[$lang] = $GLOBALS[$GLOBALS['idx_lang']];
					}
				}
			}
			closedir($dh);
			
			if(count($raccourcis)) {
				// si multilingue, envelopper les raccourcis en 'multi'
				if(count($langues_selection) > 1) {
					$ii = array();
					foreach($langues_selection as $lang) {
						// si pas de traduction (cas d'une langue ajoutee en cours de vie du site)
						// rajouter la version par defaut fr
						if(!isset($raccourcis[$lang])) $raccourcis[$lang] = $raccourcis['fr'];
						
						ksort($raccourcis[$lang]);
						foreach($raccourcis[$lang] as $key => $value) {
							$ii[$key] = 
								// placer les precedentes trad recuperees par foreach
								(isset($ii[$key]) ? $ii[$key] : "") 
								// empiler nouvelle trad
								. "\n[$lang]" . $value;
						}
					}
					ksort($ii);
					// envelopper 'multi' pour l'edition en formulaire
					$raccourcis = array();
					foreach($ii as $key => $value) {
						$raccourcis[$key] = "<multi>" . $value . "\n</multi>";
					}
				}
				// si pas multilingue, recopier tel que
				else {
					$raccourcis = $raccourcis[$lang];
				}
			}
			
			// restituer les langues
			unset ($GLOBALS[$idx_lang_surcharge]);
			$GLOBALS['idx_lang'] = $idx_lang_normal;
		}
		else raper_log("Erreur: " . _DIR_RAPER_LANGUES . " not readable!");
	}
	else raper_log("Erreur: " . _DIR_RAPER_LANGUES . " not found! Please re-install " . _T('raper:raper'));

	return($raccourcis);
}

/*
 * Va chercher le numero de revision SVN dans le fichier svn.revision si present
 * @return string ou null si absent
 */
function raper_plugin_revision () {
	if(is_readable($f = _DIR_PLUGIN_RAPER."svn.revision")) {
		if($content = file_get_contents($f)) {
			if(preg_match("|<revision>(.*)</revision>|U", $content, $matches)) {
				return($matches[1]);
			}
		}
	}
	return($revision);
}

/*
  * Modification d'un raccourci. Enregistrer dans les prefs du raper.
  * @return bool
  * @param $key string
  * @param $value string ou bool. Si bool === null, suppprime le raccourci des prefs.
  */
function raper_raccourci_modifier ($key, $value) {

	if(!empty($key)) {
		$prefs = raper_lire_preferences();
		if(!isset($prefs['raccourcis'])) $prefs['raccourcis'] = array();
		if($value === null) {
			if(isset($prefs['raccourcis'][$key])) {
				unset($prefs['raccourcis'][$key]);
			}
		}
		else {
			$prefs['raccourcis'][$key] = trim($value);
		}
		$result = (raper_ecrire_preferences($prefs) ? true : false);
		if($result) {
			$action = ($value == null) ? "deleted" : "updated";
			raper_log_action_raccourci ($key, $action);
		}
	}
	return($result);
}

/*
 * Supprimer un raccourci
 * @return bool 
 * @param $key Object
 */
function raper_raccourci_supprimer ($key) {
//raper_log("raper_raccourci_supprimer ($key)");
	$prefs = raper_lire_preferences();
	if(!empty($key) && isset($prefs['raccourcis'][$key])) {
		unset($prefs['raccourcis'][$key]);
		raper_log_action_raccourci ($key, "deleted");
		$result = (raper_ecrire_preferences($prefs) ? true : false);
	}
	return($result);
}

/*
 * Nombre de langues gerees par le RaPer
 * @return bool
 */
function raper_site_langues_compter () {
	static $nb;
	if($nb === null) $nb = substr_count(raper_langues_selection(), ',') + 1;
	return($nb);
}

/*
 * Donne liste des langues utilisees ou celles definies via ?exec=lang_raccourcis
 * @return string, du style "fr,en,..."
 */
function raper_langues_selection () {
	static $langues_selection;
	if(!$langues_selection) {
		$prefs = raper_lire_preferences();
		$langues_selection = $GLOBALS['meta'][raper_type_langues($prefs['type_langues'])];
		if(strpos($langues_selection, ",")) {
			$langues_selection = explode(",", $langues_selection);
			sort($langues_selection);
			$langues_selection = implode(",", $langues_selection);
		}
	}
	return($langues_selection);
}

/*
 * Controler le choix des langues (utilisées OU multilingue)
 * @return 
 * @param $choix Object
 */
function raper_type_langues ($choix) {
	if($choix !=_RAPER_TYPE_LANGUES_MULTILINGUE) $choix = _RAPER_TYPE_LANGUES_UTILISEES;
	return($choix);
}

/*
 * Surcharger une langue sur GLOBALS idx_lang en cours
 * @return 
 * @param $lang string
 * @param $module string[optional]
 * @param $path_array array[optional]
 */
function raper_surcharger_langue ($lang, $module = _RAPER_LANG_FILENAME_PREFIX, $path_array = array("")) {
	if($lang) $lang = "_" . $lang;
	$fichier = $module.$lang;
	foreach($path_array as $path) {
		$fichier = $module.$lang.".php";
		if(file_exists($f = $path."lang/".$fichier) || file_exists($f = $path.$fichier)) {
			surcharger_langue($f);
			return(true);
		}
	}
	return(false);
}

