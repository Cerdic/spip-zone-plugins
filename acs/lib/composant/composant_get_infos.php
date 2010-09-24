<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Composant - Méthode cGetPages: retourne un tableau des squelettes qui utilisent le composant ($c,$nic)
 * Non inclus comme méthode d'objet composant pour permettre usage sans création d'objet composant
 */

function cGetPages($c, $nic, $chemin='') {
  $pages = array();
  $pages['composant'] = array();
  $pages['variables'] = array();

  if (!$c) return $pages;

  if ($chemin == '')
    $dir = _DIR_PLUGIN_ACS.'models/'.$GLOBALS['meta']['acsModel'];
  else
    $dir = find_in_path($chemin);

  if (@is_dir($dir) AND @is_readable($dir) AND $d = @opendir($dir)) {
    $vars= cGetVars($c, $nic);
    if ($nic)
    	$cpreg = '/\{fond=composants\/'.$c.'\/[^\}]*\}.*\{nic='.$nic.'\}/';
   	else
   		$cpreg = '/\{fond=composants\/'.$c.'\/[^\}]*\}.*/';
    while (($f = readdir($d)) !== false && ($nbfiles<1000)) {
      if ($f[0] != '.' # ignorer . .. .svn etc
      AND $f != 'CVS'
      AND $f != 'remove.txt'
      AND @is_readable($p = "$dir/$f")) {
        if (is_file($p)) {
          if (preg_match(";.*[.]html$;iS", $f)) {
            $fic = @file_get_contents($p);
            if (preg_match($cpreg, $fic, $matches))
              $pages['composant'][] = substr($f, 0, -5);
            foreach($vars as $var) {
              if (strpos($fic, $var))
                $pages['variables'][substr($f, 0, -5)][] = $var;
            }
          }
        }
      }
      $nbfiles++;
    }
    $pages['chemin'] = $chemin;
  }
  return $pages;
}

/**
 * Composant - Méthode cGetTraductions: retourne un tableau des traductions d'un composant
 * Non inclus comme méthode d'objet composant pour permettre usage sans création d'objet composant
 */
function cGetTraductions($c) {
  $r[0] = cGetFiles($c, 'composants/'.$c.'/lang', $ext='php', strlen($c)+1);
  $r[1] = cGetFiles($c, 'composants/'.$c.'/ecrire/lang', $ext='php', strlen($c)+strlen('ecrire')+2);
  return $r;
}

/**
 * Composant - Méthode cGetFiles: retourne un tableau de fichiers d'un composant
 * qui vérifient l'expression régulière $expr (par défaut: les fichiers html)
 * Non inclus comme méthode d'objet composant pour permettre usage sans création d'objet composant
 */
function cGetFiles($c, $chemin='', $ext='html', $skip=0) {
  $files=array();
  if (!$c) return $files;
  $dir = find_in_path($chemin);
  if (@is_dir($dir) AND @is_readable($dir) AND $d = @opendir($dir)) {
    while (($f = readdir($d)) !== false && ($nbfiles<1000)) {
      if ($f[0] != '.' # ignorer . .. .svn etc
      AND $f != 'CVS'
      AND $f != 'remove.txt'
      AND @is_readable($p = "$dir/$f")) {
        if (is_file($p)) {
          if (preg_match(';.*[.]'.$ext.'$;iS', $f)) {
            $files[] = substr($f, $skip, -(strlen($ext)+1));
          }
        }
      }
      $nbfiles++;
    }
  }
  return $files;
}

/**
 * Fonction cGetVars: retourne un tableau des variables du composant
 * sans création d'objet composant
 */
function cGetVars($composant, $nic) {
  $r = array();
  // Lit les paramètres de configuration du composant
  include_once('inc/xml.php');
  $configfile = find_in_path('composants/'.$composant.'/ecrire/composant.xml');
  $config = spip_xml_load($configfile);
  $c = $config['composant'][0];
  if (is_array($c['param'])) {
    foreach($c['param'] as $param) {
      if (is_array($param['nom']) && $param['nom'][0] == 'optionnel' && (($param['valeur'][0] == 'oui') || ($param['valeur'][0] == 'true')))
        array_push($r, 'acs'.ucfirst($composant).'Use');
    }
  }

  if (is_array($c['variable'])) {
    foreach($c['variable'] as $k=>$var) {
      foreach($var as $varname=>$value) {
        if ($varname=='nom')
          array_push($r, 'acs'.ucfirst($composant).$nic.$value[0]);
      }
    }
  }
  return $r;
}
?>