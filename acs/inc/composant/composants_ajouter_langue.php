<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

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
 * @params : $dir est soit vide soit egal a "ecrire"
 */
function composants_ajouter_langue($dir='') {
  $idx = $GLOBALS['idx_lang'];
  $idx_tmp = $idx.'_tmp';
  $GLOBALS['idx_lang'] = $idx_tmp;

  foreach (composants_liste() as $c => $composant) {
  	// On teste si au moins une instance du composant est active
    if (!composant_actif($composant)) continue;

    $langfile = find_in_path("composants/$c/".($dir ? $dir.'/' : '')."lang/$c".'_'.($dir ? $dir.'_' : '').$GLOBALS['spip_lang'].'.php');
    if (!$langfile)
      $langfile = find_in_path("composants/$c/".($dir ? $dir.'/' : '')."lang/$c".'_'.($dir ? $dir.'_' : '').'fr.php');
    if (!$langfile)
      continue;
    require_once($langfile);
    if (is_array($GLOBALS[$idx_tmp])) {
      $cla = array();
      foreach($GLOBALS[$idx_tmp] as $k => $v) {
        $cla[$c.'_'.$k] = $v;
      }
      $GLOBALS[$idx] = array_merge($GLOBALS[$idx], $cla);
    }
    unset($GLOBALS[$idx_tmp]);
  }
  $GLOBALS['idx_lang'] = $idx;
}

/**
 * Ajoute un fichier de langue à la langue en cours
 * @param $langfile
 */
function acs_addLang($langfile) {
	$current = $GLOBALS[$GLOBALS['idx_lang']];
	if (!is_array($current))
		$current = array();
	include_spip($langfile);
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
      acs_addLang('lang/acs_variables_'.$lang);
      composants_ajouter_langue('ecrire');
    }
  	acs_addLang('lang/acs_upload_'.$lang);
    // Ajoute les fichiers de langue des composants (partie publique)
  	composants_ajouter_langue();
  }
  else {
  	// Traductions de l'espace ecrire d'ACS
  	acs_addLang('lang/acs_ecrire_'.$lang);
  	// Traductions génériques inclues dans ACS
    acs_addLang('lang/acs_variables_'.$lang);
    // Traductions des composants
    composants_ajouter_langue('ecrire');
  }
}

?>
