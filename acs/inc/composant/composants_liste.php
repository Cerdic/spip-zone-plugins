<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Lit la liste des composants disponibles - Get available components list
 * Utilise le cache ACS - Use ACS cache
 * 
 * Retourne un tableau avec la liste des composants et de leurs instances
 * Return Array (
    [articles] => Array
        (
            [instances] => Array
                (
                    [0] => Array
                        (
                            [on] => oui
                        )
                )
        )
    [cadre] => Array
        (
            [over] => squelettes_netoyens
            [instances] => Array
                (
                    [1] => Array
                        (
                            [on] => oui
                        )

                    [3] => Array
                        (
                            [on] => oui
                        )
                )
        )
)

 */
function composants_liste(){
  static $cl=array();

  if (count($cl) > 0)
    return $cl; // Return result if done once

  require_once _DIR_ACS.'inc/acs_cache.php';
  $model = (isset($GLOBALS['meta']['acsModel']) ? $GLOBALS['meta']['acsModel'] : 'cat');  
  $cl = cache('lecture_composants_liste', 'a_'.$GLOBALS['meta']['acsModel'].'_cl');
  $cl = $cl[0];
  return $cl;
}

function lecture_composants_liste() {
  // Liste des composants du modèle ACS actif
  $dirc = _DIR_PLUGIN_ACS.'models/'.$GLOBALS['meta']['acsModel'].'/composants';
  $cl = lit_liste_composants($dirc);

  // Si override, il faut ajouter la liste des composants du ou des dossiers d'override
  if (isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
    $tas = explode(':', $GLOBALS['meta']['acsSqueletteOverACS']);
    foreach($tas as $dir) {
      $dirc = _ACS_DIR_SITE_ROOT.$dir.'/composants';
      $cl = array_merge($cl, lit_liste_composants($dirc, $dir));
    }
  }
  ksort($cl);
  return $cl;
}

// Retourne la liste des composants du dossier $dirc et de leurs instances
function lit_liste_composants($dirc, $tag=''){
  $lc = array();

  if (!(@is_dir($dirc) AND @is_readable($dirc) AND $d = @opendir($dirc)))
    return $lc;

  while (($f = readdir($d)) !== false && ($nb<1000)) {
    if ($f[0] != '.' # ignorer . .. .svn etc
    AND $f != 'CVS'
    AND $f != 'remove.txt'
    AND @is_readable($p = $dirc."/$f/ecrire/composant.xml")) {
      if (is_file($p)) {
      	$instances = composant_instances($f);
      	if (count($instances)) {
					if ($tag)
       			$lc[$f]['over'] = $tag;
      		foreach($instances as $nic) {
      			$lc[$f]['instances'][$nic]['on'] = $GLOBALS['meta']['acs'.ucfirst($f).$nic.'Use'];
      		}
      	}
      	else {
					if ($tag)
						$lc[$f]['over'] = $tag;
      		$lc[$f]['instances'][0]['on'] = $GLOBALS['meta']['acs'.ucfirst($f).'Use'];
      	}
      }
    }
    $nb++;
  }
  return $lc;
}

/**
 * Retourne les instances d'un composant
 */
function composant_instances($c) {
  static $ci = array();

  if (count($ci[$c]) > 0)
    return $ci[$c];
    
  $ci[$c] = array();
  $metas = $GLOBALS['meta'];
  $reg = '/acs'.ucfirst($c).'(\d+)*Use/';
  foreach ($metas as $meta=>$val) {
    if (preg_match($reg, $meta, $matches))
      $ci[$c][] = $matches[1];
  }
  sort($ci[$c]);
  return $ci[$c];
}

/**
 * Renvoit true si un composant a au moins une instance active
 * Le paramètre $composant est celui d'une boucle foreach (composants_liste() as $class => $composant)
 */
function composant_actif($composant) {
	$actif = false;

	// Si on upgrade depuis une vieille version, il n'y a pas d'instances de composants. Ce test pourra être supprimé ulterieurement
	if (!is_array($composant['instances']))
		return false;
		
	foreach($composant['instances'] as $nic=>$cp) {
		if ($cp['on'] == 'oui') {
			$actif = true;
			break;
		}
	}
  return $actif;
}

?>