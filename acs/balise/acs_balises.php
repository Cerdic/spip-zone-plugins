<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

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
	return acs_release();
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

function balise_ACS_DERNIERE_MODIF($p) {
  $p->code = '$GLOBALS["meta"]["acsDerniereModif"]';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

// Retourne le chemin d'une ressource ACS
// ou vide si la ressource n'est pas accessible au moins en lecture. 
function balise_ACS_CHEMIN($p) {
  $arg = interprete_argument_balise(1,$p);
  $p->statut = 'php';
  $p->interdire_scripts = false;
  $p->code = $arg ? '_DIR_RACINE.$GLOBALS["ACS_CHEMIN"]."/".'.$arg : '_DIR_RACINE.$GLOBALS["ACS_CHEMIN"]."/"';
  eval('$path = '.$p->code.';');
  if (!is_readable($path) || !is_file($path))
  	$p->code = '""';
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
 * Retourne les css ou les javascripts des composants
 */
function balise_HEADER_COMPOSANTS($p) {
  $typeh = interprete_argument_balise(1,$p);
  $typeh = substr($typeh, 1, strlen($typeh)-2);
  $p->code = 'composants_head_cache("'.$typeh.'")';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p; 
}

// "Cache" de la fonction composants_head. On force le recalcul car de toute façon SPIP met en cache
// ça ferait donc double-emploi, avec de sérieux soucis de debug liés a un double cache css sinon.
// TODO : garder la mise en cache pour les "vieux" Spip ?
function composants_head_cache($type) {
  $r = cache('composants_head', 'head_'.$GLOBALS['meta']['acsModel'].'_'.$type, array("$type"), true);
  return $r[0];
}

// Retourne les css ou javascripts des composants, concaténés
function composants_head($type) {
  if (is_array(composants_liste())) {
    // composants_liste() est statique,  mise en cache,
    // et tient compte de l'override éventuel
    $done = array();
    $jslibs = array();
    foreach (composants_liste() as $class=>$cp) {
    	foreach($cp['instances'] as $nic=>$c) {
    		if ($c['on'] != 'oui') continue;
    		if (!in_array($class, $done)) {
          $filepath = 'composants/'.$class.'/'.((strtolower($type) == 'css') ? $class.'.css': "$type/$class.js");
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
      // on fait la liste des librairies javascripts a inclure (declarees dans chaque composant, dans moncomposant_balises.php,
      // sous la forme d'une fonction moncomposant_jslib() qui retourne un tableau des librairies js a inclure pour ce composant)
      if(strtolower($type) == 'javascript') {
        if (is_callable($class.'_jslib')) {
        	$c_jslibs = $class.'_jslib';
        	foreach($c_jslibs() as $lib) {
        		$jslibs[$lib] = true;
        	}
        }
      }
    }
    // on recupere les librairies js requises pour tous les composants, une seule fois chacune
    foreach($jslibs as $jslib => $ok) {
      $file = find_in_path($jslib.'.html');
      if (!$file) {
        $file = find_in_path($jslib);
        if ($file)
          $libs .= file_get_contents($file)."\r";
      }
      else {
        $libs .= recuperer_fond($jslib, array('X-Spip-Cache' => 0))."\r";
      }
    }
  }
  return $libs.$r;
}
/* usage avec les groupes acs ou pour droits sur le site public */
function balise_ACS_AUTORISE($p) {
  $rs = interprete_argument_balise(1,$p);
  $p->code = 'acs_autorise('.$rs.')';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
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
