<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/composant/composants_liste');

/**
 * Inclut les balises spip définies par les composants actifs
 * Include components defined spip-tags
 */
function composants_ajouter_balises() {
  foreach (composants_liste() as $c =>$composant) {
   	// On teste si au moins une instance du composant est active
    if (!composant_actif($composant)) continue;
  	$bc= find_in_path('composants/'."$c/$c".'_balises.php');
    if ($bc)
      include($bc); // Les erreurs ne doivent JAMAIS être masquées, ici
  }
}
// On ajoute les balises de chaque composant actif - We add tags for every active component
composants_ajouter_balises();

function balise_ACS_VERSION($p) {
  $p->code = 'calcule_balise_acs_version()';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}
function calcule_balise_acs_version() {
	include_spip('inc/acs_version');
	return acs_version();
}
function balise_ACS_RELEASE($p) {
  $p->code = 'calcule_balise_acs_release()';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}
function calcule_balise_acs_release() {
	include_spip('inc/acs_version');
	$r = acs_release(); // fonction SPIP version_svn_courante(_DIR_ACS) retourne 0 si bug ou niet
	if ($r != 0)
		return $r;
	return '';
}

function calculer_balise_pinceau($composant, $nic) {
  $nic = $nic ? $nic : '0';
  return  'crayon composant-'.$composant.'-'.$nic.' type_pinceau';
}

function balise_PINCEAU($p) {
  $composant = interprete_argument_balise(1,$p);
  $nic = interprete_argument_balise(2,$p);
  $nic = $nic ? $nic : "'0'";
  $p->code = 'calculer_balise_pinceau('.$composant.', '.$nic.')';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function acs_chemin($path='', $type=false) {
	$path = _DIR_RACINE.$GLOBALS["ACS_CHEMIN"].'/'.$path;
  // On retourne vide si la ressource n'est pas lisible
  if (!is_readable($path))
  	return '';
  // Sinon si la ressource n'est pas un fichier on retourne vide
  // sauf si on recherchait un sous-dossier ou la racine des ressources ACS
  elseif (($path != '') && ($type != 'dir') && is_dir($path))
  	return '';
	return $path;
}
/**
 * Retourne le chemin d'une ressource ACS ou vide si la ressource n'est pas
 * accessible au moins en lecture.
 * Usages: #ACS_CHEMIN, #ACS_CHEMIN{chemin_fichier}, #ACS_CHEMIN{chemin_dossier,dir}
 */
function balise_ACS_CHEMIN($p) {
  $arg = interprete_argument_balise(1,$p);
  $type = interprete_argument_balise(2,$p);
  $p->statut = 'php';
  $p->interdire_scripts = false;
  if (is_null($arg))
  	$p->code = "acs_chemin()";
  elseif (is_null($type))
  	$p->code = "acs_chemin($arg)";
	else
  	$p->code = "acs_chemin($arg, $type)";
  return $p;
}

/** VAR = balise CONFIG de SPIP etendue, qui remplaçe les variables ACS par leur valeur, récursivement.
 * admet deux arguments : nom de variable, valeur par defaut si vide
 * syntaxe : #VAR{acsComposantVariable} ou #VAR{acsComposantVariable, valeur_par_defaut}
 * Lorsque la valeur d'une variable commence par le signe "=",
 * cette valeur est interprétée comme une référence récursive à une autre variable.
 */

function balise_VAR($p) {
	$var = interprete_argument_balise(1,$p);
	$sinon = interprete_argument_balise(2,$p);
	if (!$var) {
		$p->code = '""'; // cas de #VAR sans argument
	} else {
		$p->code = 'meta_recursive($GLOBALS["meta"], '.$var.')';
		if ($sinon)
			$p->code = 'sinon('.$p->code.','.$sinon.')';
		else
			$p->code = '('.$p->code.')';
	}
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Retourne headers, css ou javascripts de tous les composants
 */
function balise_COMPOSANTS_CODE($p) {
  $typeh = interprete_argument_balise(1,$p);
  $typeh = substr($typeh, 1, strlen($typeh)-2);
  $p->code = 'composants_code("'.$typeh.'")';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p; 
}

/**
 * Retourne headers, css ou javascripts de tous les composants actifs du set,
 * concaténés.
 * @param string $type : css, javascript
 * @return string
 */
function composants_code($type) {
	// le retour de composants_liste() est statique,  mis en cache,
	// et tient compte de l'override éventuel.
  if (is_array(composants_liste())) {
    $done = array();
    foreach (composants_liste() as $class=>$cp) {
    	foreach($cp['instances'] as $nic=>$c) {
    		if ($c['on'] != 'oui') continue;
    		if (!in_array($class, $done)) {
    			$type = strtolower($type);
    			switch ($type) {
    				case 'css' :
    					$f = $class.'.css';
    					break;
    				case 'javascript':
    					$f = "javascript/$class.js";
    					break;
    				default:
    					$f = $class.'_'.$type;
    			}
          $filepath = 'composants/'.$class.'/'.$f;
          $file = find_in_path($filepath.'.html');
          if (!$file) {
            $file = find_in_path($filepath);
            if ($file)
              $r .= file_get_contents($file);
          }
          else {
            $r .= recuperer_fond($filepath, array('X-Spip-Cache' => 0))."\r";
          }
          $done[] = $class; 
    		}
        // On cherche aussi les css d'instances de composants
        if (strtolower($type) == 'css') {
        	$filepath = 'composants/'.$class.'/'.$class.'_instances.css';
        	$file = find_in_path($filepath.'.html');
        	if ($file)
       			$r .= recuperer_fond($filepath, array('nic' => $nic, 'X-Spip-Cache' => 0))."\r";
        }
      }
    }
  }
  return $r;
}

/* Overide de la balise CACHE : permet de passer un paramètre à la balise SPIP
 * Les 2 paramètres de la balise #CACHE sont interprétés et passés à la balise
 * #CACHE de la dist.
 */
function balise_CACHE($p) {
  if ($GLOBALS['contexte']['cache']) {
    $cache = explode(',', $GLOBALS['contexte']['cache']);
    $p->param[0][1][0]->texte = $cache[0];
    if (isset($cache[1]))
      $p->param[0][1][1]->texte = $cache[1];
  }
  return balise_CACHE_dist($p);
}
