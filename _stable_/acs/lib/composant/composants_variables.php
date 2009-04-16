<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne un tableau de toutes les variables de composants
 *
 * Utilise le cache ACS
 */
function composants_variables() {
  static $cv=array();

  if (count($cv) > 0)
    return $cv;

  include_spip('inc/acs_cache');
  $cv = cache('lecture_composants_variables', 'a_'.$GLOBALS['meta']['acsModel'].'_cv');
  $cv = $cv[0];

  return $cv;
}

// On profite de la lecture du fichier composant.xml pour récupérer en une seule fois toutes les informations utiles:
// lien variable<->composant, type de variable, actif/inactif
function lecture_composants_variables() {
  require_once _DIR_ACS.'lib/composant/composants_liste.php';

  include_spip('inc/xml');
  $r = array();
  foreach(composants_liste() as $composant=>$tag) {
    $config = find_in_path('composants/'.$composant.'/ecrire/composant.xml');
    $config = spip_xml_load($config); // Lit les paramètres de configuration du composant
    $c = $config['composant'][0];
    $r[$tag.ucfirst($composant).'Use'] = array('composant' => $composant, 'type' => 'use');

    if (is_array($c['variable'])) {
      foreach($c['variable'] as $k=>$var) {
      	$option = array();
      	$chemin = false;
        foreach($var as $xmltag=>$value) {
          if ($xmltag == 'nom')
            $nom = ucfirst($composant).$value[0];
          elseif ($xmltag == 'option')
            $r[$tag.$nom]['option'] = $value;
          else
            $r[$tag.$nom][$xmltag] = $value[0];
        }
      	$r[$tag.$nom]['composant'] = $composant;
      }
    }
  }
  return $r;
}

function composant_instances($c) {
  $r =array();
  $metas = $GLOBALS['meta'];
  $reg = '/acs'.ucfirst($c).'(\d+)Use/';
  foreach ($metas as $meta=>$val) {
    if (preg_match($reg, $meta, $matches))
      $r[] = $matches[1];
  }
  return $r;
}
?>