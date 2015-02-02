<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

require_once _DIR_ACS.'inc/composant/composants_liste.php';

/**
 * Inclut les fichiers de langue des composants actifs
 * Include components lang files
 *
 * Ordre de recherche:
 * 1. langue courante depuis dossier(s) squelettes_over_acs
 * 2. langue courante depuis dossier modèle acs actif
 * 3. langue par défaut depuis dossier(s) squelettes_over_acs
 * 4. langue par défaut depuis dossier modèle acs actif
 * 
 * @params : $dir (="" ou "ecrire").
 */
function composants_ajouter_langue($dir='') {
  foreach (composants_liste() as $c => $composant) {
  	// On teste si au moins une instance du composant est active
    if (!composant_actif($composant)) continue;

    // Cherche le fichier de langue du composant :
    $langfile = find_in_path("composants/$c/".($dir ? $dir.'/' : '')."lang/$c".'_'.($dir ? $dir.'_' : '').$GLOBALS['spip_lang'].'.php');
    if (!$langfile)
      $langfile = find_in_path("composants/$c/".($dir ? $dir.'/' : '')."lang/$c".'_'.($dir ? $dir.'_' : '').'fr.php');
    if (!$langfile)
    	continue;

    $idx = $GLOBALS['idx_lang'];
    $idx_tmp = $idx.'_tmp';
    $GLOBALS['idx_lang'] = $idx_tmp;
    // Charge le fichier de langue
    require_once($langfile);
    // Affecte les traductions chargée au composant :
    if (is_array($GLOBALS[$idx_tmp])) {
      $cla = array();
      foreach($GLOBALS[$idx_tmp] as $k => $v) {
        $cla[$c.'_'.$k] = $v;
      }
      $GLOBALS[$idx] = array_merge($GLOBALS[$idx], $cla);
    }
    else
    	acs_log('ERROR in composants_ajouter_langue() : $GLOBALS[\''.$idx.'\'] from lang file "'.$langfile.'" is not an array.');
    // On efface le tableau temporaire et on restaure la valeur originale de $GLOBALS['idx_lang'] :
    unset($GLOBALS[$idx_tmp]);
    $GLOBALS['idx_lang'] = $idx;
  }  
}

/**
 * Ajoute un fichier de langue à la langue en cours
 * @param $langfile : chemin d'un fichier de langue à partir d'un path SPIP (chemins où SPIP cherche ses includes)
 */
function acs_addLang($langfile) {
	$current = $GLOBALS[$GLOBALS['idx_lang']];
	if (!is_array($current))
		$current = array();
	include_spip($langfile);
	if ($current != $GLOBALS[$GLOBALS['idx_lang']])
		$GLOBALS[$GLOBALS['idx_lang']] = array_merge($current, $GLOBALS[$GLOBALS['idx_lang']]);
}

/**
 * Utiliser la langue fournie en parametre
 * @ param $lang (fr, en, ...)
 */
function acs_langue($lang) {
  // Lang file is build with components lang files
  if (_DIR_RESTREINT != '') {
    if (_request('action') == 'crayons_html') { // On ajoute les traductions pour les crayons
      acs_addLang('lang/acs-variables_'.$lang);
      composants_ajouter_langue('ecrire');
    }
  	acs_addLang('lang/acs-upload_'.$lang);
    // Ajoute les fichiers de langue des composants (partie publique)
  	composants_ajouter_langue();
  }
  else {
  	// Traductions de l'espace ecrire d'ACS
  	acs_addLang('lang/acs-ecrire_'.$lang);
  	// Traductions génériques inclues dans ACS
    acs_addLang('lang/acs-variables_'.$lang);
    // Traductions des composants
    composants_ajouter_langue('ecrire');
  }
}

?>
