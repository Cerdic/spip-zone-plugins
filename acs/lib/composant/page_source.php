<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('lib/composant/composants_variables');
include_spip('lib/composant/pages_liste');

/**
 * Analyse une page
 * Retourne un tableau des variables ACS, des balise et des inclusions
 */
function analyse_page($page, $mode_source) {
  // Construit regexp pour chercher les variables de composants
  // Build regexp for searching components variables

  $vars = composants_variables();

  if (is_array($vars) && (count($vars) > 0)) {
    foreach ($vars as $v=>$c) {
      $vars_regexp .= 'acs'.$v.'|';
    }
    $vars_regexp = '('.substr($vars_regexp, 0, -1).')';
  }

  $modeles = pages_liste();
  $modeles = $modeles['modeles'];
  if (is_array($modeles) && (count($modeles) > 0)) {
    foreach ($modeles as $m=>$src) {
      $modeles_regexp .= '#'.strtoupper($m).'|';
    }
    $modeles_regexp = substr($modeles_regexp, 0, -1);
  }

  $balises = explode(', ', liste_balises());
  usort($balises, "compare_taille");
  $balises = '#'.implode('|#', $balises);


  // Quelques définitions de regexp :
  // capture tout entre accolades jusqu'à trois niveaux d'imbrication
  $reg_entre_accolades = '(?:\{(?:(?:[^\}]*\{(?:[^\}]*\{[^\}]*\})[\s]*\})*[\s]*|(?:[^\}]*\{[^\}]*\})*[\s]*|[^}]*)*\})';
  // filtre spip
  $reg_filtre_spip = '(?:\|(?:==|!=|\?)*[\w]*(?:'.$reg_entre_accolades.')*)';

// Les types de tags spip repérés en mode schéma
  $reg[] = array('REM', 2, '(\[\(#REM\)([^\]]*)\])');
  if ($vars_regexp)
    $reg[] = array('VAR', 1, $vars_regexp);
  if ($modeles_regexp)
      $reg[] = array('MODELE', 3, '(('.$modeles_regexp.')('.$reg_entre_accolades.'|'.$reg_filtre_spip.')*)');
  $reg[] = array('INCLURE', 3, '((?:<|#)INCLU[R|D]E[\s]*\{fond=([^\}]*)\}((?:[\s]*'.$reg_entre_accolades.')*)>?)');
  $reg[] = array('BOUCLE', 4, '(<BOUCLE[_]?([^(]*)\(([^)]*)\)((?:>=|[^\>])*)>)');
  $reg[] = array('FIN_BOUCLE', 2, '(<\/BOUCLE[_]?([^>]*)>)');
  if ($mode_source) {
    $reg[] = array('B', 1, '(<B_[^>]*>)');
    $reg[] = array('FIN_B', 1, '(<\/B_[^>]*>)');
    $reg[] = array('TRAD', 1, '(<:[\w]*:>)');
    $reg[] = array('BALISE', 3, '(('.$balises.')('.$reg_entre_accolades.'|'.$reg_filtre_spip.')*)');
  }
  //$reg[] = array(,,);

  $def = array();
  foreach ($reg as $rx) {
    $def[] = array('spip_tag' => $rx[0], 'nb' => $rx[1]);
    $regexp .= $rx[2].'|';
  }

  // La structure $analyse détermine l'analyse recursive avec regexp
  $analyse = array(
    'vars' => $vars,
    'regexp' => '/'.substr($regexp, 0, -1).'/s',
    'regdef' => $def,
    'mode_source' => $mode_source
    );
  return page_includes($page, $analyse);
}

// Analyse séquentiellement la page, et retourne un tableau constitué
// d'un tableau des variables ACS trouveés
// et d'un tableau des contenus avec leurs positions.de début et de fin
function page_includes(
                $texte,       // Texte à analyser
                &$analyse,    // Structure d'analyse
                &$includes=array(
                  'tags' => array(),
                  'vars' => array()
                  ),
                $offset=0,     // offset depuis le début de $pg
                $tagoffset=0   // offset depuis le début du tag
                ) {
  static $k;
  if ($k > 9999) return $includes;
  $k++;

  if (@preg_match($analyse['regexp'], $texte, $matches, PREG_OFFSET_CAPTURE, $offset)) {
//print_r($matches);
    $indice = 1;
    foreach($analyse['regdef'] as $capture) {
      if ($matches[$indice][0]) {
        $args = array();
//echo "<br>".$capture['f']."<br>";
        $matched = $matches[$indice][0];
        $debut = $matches[$indice][1];
        $fin = $debut + strlen($matched);
        for ($i = $indice; $i < $indice + $capture['nb']; $i++) {
//echo " [".($i)."][0]=".htmlspecialchars($matches[$i][0])."<br>";
          $args[] = $matches[$i][0];
        }
        if (is_callable('pi_'.$capture['spip_tag']))
          $contenu = call_user_func('pi_'.$capture['spip_tag'], array($args, &$analyse, &$includes));
        $includes['tags'][$tagoffset + $debut] = array(
          'fin' => $tagoffset + $fin,
          'contenu' => $contenu,
          'type' => $capture['spip_tag']
        );
        page_includes($matched, $analyse, $includes, 1, $tagoffset + $debut); // Recherche des imbrications
      }
      $indice = $indice + $capture['nb'];
    }
    page_includes($texte, $analyse, $includes, $fin, $tagoffset); // Va au tag suivant
  }
  return $includes;
}

function pi_REM($args) {
  if ($args[1]['mode_source']) return $args[0][0];

  return '<div class="spip_params onlinehelp pliable">'.nl2br($args[0][1]).'</div>';
}

function pi_MODELE($args) {
  if ($args[1]['mode_source']) return $args[0][0];

  $m = $args[0][1];
  $p = $args[0][2];

  $r = indent($args[1]['indentation']).'<span class="col_MODELE" title="'._T('acs:modele').' '.strtolower(substr($m, 1)).($p ? ' '.htmlspecialchars($p) : '').'" style="color:#4f00af">'.$m;
  if ($p)
    $r .= ' <span class="spip_params pliable">'.indent($args[1]['indentation']).str_replace('}{', '} {', htmlspecialchars($p)).'</span>';
  $r .= '</span>';
  return $r;
}

function pi_INCLURE($args) {
  if ($args[1]['mode_source']) return $args[0][0];

  $include = $args[0][1];
  $param = $args[0][2];

  if (preg_match('/{nic=(\d*)\}/', $args[0][0], $matches)) {
    $nic = $matches[1];
  }
  if (substr($include, 0, 11) == 'composants/') {
    $r = affiche_widgy($include, $param, $args[1]['vars'], indent($args[1]['indentation']), $nic);
  }
  else {
    if (find_in_path($include.'.html'))
      $r = indent($args[1]['indentation']).'<a class="'.get_widget_class($include, 'widgy').' lien_page" style="background: none" href="?exec=acs&onglet=page&pg='.$include.'" onclick=\'$("#page_infos").empty();
        AjaxSqueeze("?exec=acs_page_get_infos&pg=" + this.text, "page_infos");
        return false;\' title="'.$param.'">'.$include.'</a>';
    else
      $r = indent($args[1]['indentation']).'<a class="'.get_widget_class($include, 'widgy').' lien_page" style="background: #efefef; border-style: solid; color: red; text-decoration: blink; " title="'._T('acs:err_fichier_absent', array('file' => $include)).'">'.$include.'</a>';
  }
  $r = $r;
  if ($param)
    $r .= '<div class="spip_params pliable">'.indent($args[1]['indentation']).str_replace('}{', '} {', $param).'</div>';
  return $r;
}

function pi_BOUCLE($args) {
  if ($args[1]['mode_source']) return $args[0][0];

  $boucle = $args[0][1];
  $type = $args[0][2];
  $param = $args[0][3];

  $r = '<div class="col_BOUCLE" title="'._T('acs:boucle').' '.strtolower($type).' '.$param.'">'.indent($args[1]['indentation']).$boucle.' ('.$type.')';
  if ($param)
    $r .= indent($args[1]['indentation']).' <span class="spip_params pliable">'.indent($args[1]['indentation']).str_replace('}{', '} {', $param).'</span>';
  $r .= '</div>';
  $args[1]['indentation']++;
  return $r;
}

function pi_FIN_BOUCLE($args) {
  if ($args[1]['mode_source']) return $args[0][0];

  $args[1]['indentation']--;
  return '<div class="col_FIN_BOUCLE">'.indent($args[1]['indentation']).'/'.$args[0][1].'</div>';
}

function pi_VAR($args) {
  $var = $args[0][0];
  $vars = $args[1]['vars'];
  $v = $vars[substr($var, 3)];

  $html = '<a class="col_VAR" href="?exec=acs&onglet=composants&composant='.$v['composant'].'" title="'._T('acs:composant').' '.ucfirst(str_replace('_', ' ', $v['composant'])).'">'.$var.'</a>';
  $args[2]['vars'][$var] = $html;

  if ($args[1]['mode_source']) return $html;
  //return $html;
}

function affiche_widgy($include, $param, &$vars, $indentation, $nic) {
  $include = substr($include, 11);
  if ($pos = strpos($include, '/')) {
    if (substr($include, 0, $pos) == substr($include, $pos + 1)) {
      $label = substr($include, 0, $pos);
    }
    else {
      $label = $include;
    }
    $composant = substr($include, 0, $pos);
  }
  return widgy($composant, $param, $vars, $label, $indentation, $nic);
}

function widgy($composant, $param, &$vars, $label='', $indentation='', $nic = '') {
  $label = ucfirst(str_replace('_', ' ', $label));
  $r = '<table><tr valign="top"><td>'.$indentation.'</td><td><b><a class="'.get_widget_class('', 'oui', 'widgy').'" href="?exec=acs&onglet=composants&composant='.$composant.($nic ? '&nic='.$nic : '').'" title="'._T('acs:composant').' '.$label.($nic ? ' '.$nic : '').'">'.$label.($nic ? ' '.$nic : '').'</a></b></td><td>';

//todo: params
  if (is_array($vars)) {
    foreach($vars as $varname=>$v) {
      if (($v['composant'] == $composant) && ($v['type'] == 'widget')) {
        $var = 'acs'.$varname;
        if (isset($GLOBALS['meta'][$var]) && $GLOBALS['meta'][$var]) {
          $r .= '<table><tr><td><a class="nompage" href="?exec=acs&onglet=composants&composant='.$composant.($nic ? '&nic='.$nic : '').'" title="'._T('acs:variable').'">'.$varname.'</a> : </td><td>'. widgy($GLOBALS['meta'][$var], '', $vars, $GLOBALS['meta'][$var]).'</td></tr></table>';
        }
      }
    }
  }
  else
    $r .= _T('acs:variable');
  return $r.'</td></tr></table>';
}

function indent($l) {
  for($i = 0; $i < $l; $i++) {
    $r .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  }
  return $r;
}

function compare_taille($a, $b) {
  if (strlen($a) == strlen($b)) {
      return 0;
  }
  return (strlen($a) > strlen($b)) ? -1 : 1;
}

function liste_balises() {
  $balises_acs = 'INTRO, ACS_CHEMIN, ACS_DERNIERE_MODIF, PINCEAU';

  $balises_spip = 'ANCRE_PAGINATION, ARRAY, ANCRE_PAGINATION, ARRAY, BIO, CACHE, CHAPO, CHARSET, CHEMIN, COMPTEUR_BOUCLE, CONFIG, DATE, DATE_MODIF, DATE_NOUVEAUTES, DATE_REDAC, DEBUT_SURLIGNE, DESCRIPTIF, DESCRIPTIF_SITE_SPIP, DISTANT, DOSSIER_SQUELETTE, EDIT, EMAIL, EMAIL_WEBMASTER, EMBED_DOCUMENT, ENV, EVAL, EXPOSE, EXPOSER, FICHIER, FIN_SURLIGNE, FORMULAIRE_ADMIN, FORMULAIRE_ECRIRE_AUTEUR, FORMULAIRE_FORUM, FORMULAIRE_INSCRIPTION, FORMULAIRE_RECHERCHE, FORMULAIRE_SIGNATURE, FORMULAIRE_SITE, GET, GRAND_TOTAL, HAUTEUR, HTTP_HEADER, ID_ARTICLE, ID_AUTEUR, ID_BREVE, ID_DOCUMENT, ID_FORUM, ID_GROUPE, ID_MOT, ID_PARENT, ID_RUBRIQUE, ID_SECTEUR, ID_SIGNATURE, ID_SYNDIC, ID_SYNDIC_ARTICLE, ID_THREAD, INCLURE, INSERT_HEAD, INTRODUCTION, IP, LANG, LANG_DIR, LANG_LEFT, LANG_RIGHT, LARGEUR, LESAUTEURS, LOGIN_PRIVE, LOGIN_PUBLIC, LOGO_ARTICLE, LOGO_ARTICLE_NORMAL, LOGO_ARTICLE_RUBRIQUE, LOGO_ARTICLE_SURVOL, LOGO_AUTEUR, LOGO_AUTEUR_NORMAL, LOGO_AUTEUR_SURVOL, LOGO_BREVE, LOGO_BREVE_RUBRIQUE, LOGO_DOCUMENT, LOGO_MOT, LOGO_RUBRIQUE, LOGO_RUBRIQUE_NORMAL, LOGO_RUBRIQUE_SURVOL, LOGO_SITE, LOGO_SITE_SPIP, MENU_LANG, MENU_LANG_ECRIRE, MESSAGE, MIME_TYPE, MODELE, NOM, NOM_SITE, NOM_SITE_SPIP, NOTES, PAGINATION, PARAMETRES_FORUM, PETITION, PGP, PIPELINE, POINTS, POPULARITE, POPULARITE_ABSOLUE, POPULARITE_MAX, POPULARITE_SITE, PS, PUCE, RECHERCHE, SELF, SET, SOURCE, SOUSTITRE, SPIP_CRON, SPIP_VERSION, SQUELETTE, SURTITRE, TAGS, TAILLE, TEXTE, TITRE, TOTAL_BOUCLE, TOTAL_UNIQUE, TYPE, TYPE_DOCUMENT, URL_ACTION_AUTEUR, URL_ARTICLE, URL_AUTEUR, URL_BREVE, URL_DOCUMENT, URL_FORUM, URL_LOGOUT, URL_MOT, URL_PAGE, URL_RUBRIQUE, URL_SITE, URL_SITE_SPIP, URL_SOURCE, URL_SYNDIC, VISITES';
  return $balises_acs.', '.$balises_spip;
}
?>