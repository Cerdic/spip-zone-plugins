<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

require_once _DIR_ACS.'lib/composant/composants_ajouter_balises.php';
composants_ajouter_balises();

function balise_PINCEAU($p) {
  $composant = interprete_argument_balise(1,$p);
  $composant = substr($composant, 1, strlen($composant)-2);
  $p->code = '"crayon composant-'.$composant.'-1 type_pinceau"';
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

function balise_ACS_CHEMIN($p) {
  $path = interprete_argument_balise(1,$p);
  $path = substr($path, 1, strlen($path)-2);
  $p->code = '$GLOBALS["ACS_CHEMIN"]."/'.$path.'"';
  $p->statut = 'php';
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

// Retourne les css ou javascripts des composants, concaténées. Cache.
function composants_head_cache($type) {
  $r = cache('composants_head', 'head_'.$GLOBALS['meta']['acsModel'].'_'.$type, array("$type"));
  return $r[0];
}

// Retourne les css ou javascripts des composants, concaténées. Fonction.
function composants_head($type) {
  require_once _DIR_ACS.'lib/composant/composants_liste.php';
  if (is_array(composants_liste())) {
    // composants_liste() est statique,  mise en cache,
    // et tient compte de l'override éventuel
    foreach (composants_liste() as $c=>$tag) {
      if (!isUsed($c)) continue;
      $filepath = 'composants/'.$c.'/'.((strtolower($type) == 'css') ? $c.'.css': "$type/$c.js");
      $file = find_in_path($filepath.'.html');
      if (!$file) {
        $file = find_in_path($filepath);
        /*if (!$file)
          continue;*/
        $r .= file_get_contents($file);
      }
      else
        $r .= recuperer_fond($filepath)."\r";
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

// #INTRO{taille, suite}
// http://www.spip.net/@introduction
// http://doc.spip.org/@balise_INTRODUCTION_dist
function balise_INTRO ($p) {
  $type = $p->type_requete;
  $_texte = champ_sql('texte', $p);
  if ($type == 'articles') {
    $_chapo = champ_sql('chapo', $p);
    $_descriptif =  champ_sql('descriptif', $p);
  } else {
    $_chapo = "''";
    $_descriptif =  "''";
  }
  $taille = interprete_argument_balise(1,$p);
  $suite = interprete_argument_balise(2,$p);
  $p->code = "intro('$type', $_texte, $_chapo, $_descriptif, $taille, $suite)";

  return $p;
}

// fonction ACS de calcul de la balise #INTRO{taille}
// ressemble à function introduction($type,$texte,$chapo,$descriptif) {...}
// http://doc.spip.org/@calcul_introduction
function intro ($type, $texte, $chapo='', $descriptif='', $taille=600, $suite='...') {
  switch ($type) {
    case 'articles':
      # si descriptif contient juste des espaces ca produit une intro vide,
      # c'est une fonctionnalite, pas un bug
      if ($descriptif)
        return propre($descriptif);
      else if (substr($chapo, 0, 1) == '=') // article virtuel
        return '';
      else
        return coupe($chapo."\n\n\n".$texte, $taille, $suite);
      break;
    case 'breves':
      return coupe($texte, $taille/2, $suite);
      break;
    case 'forums':
      return coupe($texte, $taille, $suite);
      break;
    case 'rubriques':
      if ($descriptif)
        return propre($descriptif);
      else
        return coupe($texte, $taille, $suite);
      break;
  }
}

/**
 * Retourne un objet ou un tableau sous forme de tableau affichable en html
 */
function dbg($r) {
   if (is_object($r) or is_array($r)) {
        ob_start();
        print_r($r);
        $r = ob_get_contents();
        ob_end_clean();
        $r = htmlentities($r);
        $srch = array('/Array[\n\r]/', '/\s*[\(\)]+/', '/[\n\r]+/', '/ (?= )/s');
        $repl = array(''             , ''            , "\n"       , '&nbsp;');
        $r = preg_replace($srch, $repl, $r);
        $r = nl2br($r);
    }
    return $r;
}