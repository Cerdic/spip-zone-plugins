<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

require_once _DIR_ACS.'lib/composant/composants_ajouter_balises.php';
composants_ajouter_balises();

function balise_PINCEAU($p) {
  $composant = interprete_argument_balise(1,$p);
  $instance = interprete_argument_balise(2,$p);
  $instance = $instance ? $instance : "'0'";
  $p->code = 'calculer_balise_pinceau('.$composant.', '.$instance.')';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function calculer_balise_pinceau($composant, $instance) {
    return  'crayon composant-'.$composant.'-'.$instance.' type_pinceau';
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
  $p->code = $arg ? '$GLOBALS["ACS_CHEMIN"]."/".'.$arg : '$GLOBALS["ACS_CHEMIN"]."/"';
  eval('$path = '.$p->code.';');
  if (!is_readable($path))
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
	$src = '$GLOBALS["meta"]';
	if (!$var) {
		// cas de #VAR sans argument : on retourne le serialize() du tableau
		// une belle fonction [(#VAR|affiche_env)] serait pratique
		$p->code = $src ? ('(is_array($a = ('.$src.')) ? serialize($a) : "")'): '@serialize($Pile[0])';
	} else {
		$meta = substr($var, 1, -1);
		$p->code = $src ? ('is_array($a = ('.$src.')) ? meta_recursive($a,"'.$meta.'") : ""') : ('meta_recursive(@$Pile[0],"'.$meta.'")');
		if ($sinon)
			$p->code = 'sinon('.$p->code.',$sinon)';
		else
			$p->code = '('.$p->code.')';
	}
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

// Cache de la fonction composants_head.
function composants_head_cache($type) {
  $r = cache('composants_head', 'head_'.$GLOBALS['meta']['acsModel'].'_'.$type, array("$type"));
  return $r[0];
}

// Retourne les css ou javascripts des composants, concaténés
function composants_head($type) {
  require_once _DIR_ACS.'lib/composant/composants_liste.php';
  if (is_array(composants_liste())) {
    // composants_liste() est statique,  mise en cache,
    // et tient compte de l'override éventuel
    $done = array();
    foreach (composants_liste() as $class=>$cp) {
    	foreach($cp['instances'] as $nic=>$c) {
    		if ($c['on'] != 'oui') continue;
    		if (!in_array($class, $done)) {
          $filepath = 'composants/'.$class.'/'.((strtolower($type) == 'css') ? $class.'.css': "$type/$class.js");
          $file = find_in_path($filepath.'.html');
          if (!$file) {
            $file = find_in_path($filepath);
            if (!$file)
              continue;
            $r .= file_get_contents($file);
          }
          else {
            $r .= recuperer_fond($filepath);
          }
          $done[] = $class; 
    		}
        // On cherche aussi les css d'instances de composants
        if (strtolower($type) == 'css') {
        	$filepath = 'composants/'.$class.'/'.$class.'_instances.css';
        	$file = find_in_path($filepath.'.html');
        	if ($file)
       			$r .= recuperer_fond($filepath, array('nic' => $nic));
        }
      }
    }
  }
  return $r;
}

/**
 * Indique si un composant optionnel est activé
 * Return true if an optionnal component is on
 */
function isUsed($c) {
  if ($GLOBALS['meta']['acs'.ucfirst($c).'Use'] == 'oui') return true;
  return false;
}

/**
 * Retourne un objet ou un tableau sous forme de tableau affichable en html
 */
function dbg($r, $html=false) {
   if (is_object($r) or is_array($r)) {
        ob_start();
        print_r($r);
        $r = ob_get_contents();
        ob_end_clean();
        if ($html)
        	$r = htmlentities($r);
        $srch = array('/Array[\n\r]/', '/\s*[\(\)]+/', '/[\n\r]+/', '/ (?= )/s');
        $repl = array(''             , ''            , "\n"       , ($html ? '&nbsp;' : ' '));
        $r = preg_replace($srch, $repl, $r);
        if ($html)
        	$r = nl2br($r);
    }
    return $r;
}