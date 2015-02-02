<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne un tableau de toutes les variables de composants
 *
 * Utilise le cache ACS
 */
function composants_variables() {
  static $cv=array();
  if (count($cv) > 0)
    return $cv;
  
  include_spip('inc/composant/composants_liste');
  include_spip('inc/acs_cache');
  $set = (isset($GLOBALS['meta']['acsSet']) ? $GLOBALS['meta']['acsSet'] : 'cat');
  $cv = cache('lecture_composants_variables', 'a_'.$set.'_cv');
  $cv = $cv[0];

  return $cv;
}

// On profite de la lecture du fichier composant.xml pour récupérer en une seule fois toutes les informations utiles:
// lien variable<->composant, type de variable, actif/inactif
function lecture_composants_variables() {
  require_once _DIR_ACS.'inc/composant/composants_liste.php';

  include_spip('inc/xml');
  $r = array();
  foreach(composants_liste() as $composant=>$tag) {
    $config = find_in_path('composants/'.$composant.'/ecrire/composant.xml');
    $config = spip_xml_load($config); // Lit les paramètres de configuration du composant
    $c = $config['composant'][0];
    $r[$composant]['vars']['Use'] = array('type' => 'use');
		if ($tag['over']) 
			$r[$composant]['over'] = $tag['over'];

		// Lecture des variables
    if (is_array($c['variable'])) {
      foreach($c['variable'] as $k=>$var) {
      	/*$option = array();
      	$chemin = false;*/
        foreach($var as $xmltag=>$value) {
          if ($xmltag == 'nom')
            $nom = $value[0];
          elseif (count($value) > 1)
            $r[$composant]['vars'][$nom][$xmltag] = $value;
          else
            $r[$composant]['vars'][$nom][$xmltag] = $value[0];
        }
      }
    }
  }
  return $r;
}

function liste_variables() {
	static $lv = array();
  if (count($lv) > 0)
    return $lv;
    
  $cv = composants_variables();
	foreach($cv as $c=>$p) {
		foreach($p['vars'] as $var=>$vp) {
			$lv[ucfirst($c).$var] = array('c' => $c, 'type' => $vp['type']);
		}
  	foreach(composant_instances($c) as $nic) {
  		foreach($p['vars'] as $var=>$vp) {
				$lv[ucfirst($c).$nic.$var] = array('c' => $c, 'type' => $vp['type'], 'nic' => $nic);
  		}
  	}
  }
  return $lv;
}

?>