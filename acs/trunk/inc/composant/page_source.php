<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2012
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/composant/composants_variables');
include_spip('inc/composant/pages_liste');

/**
 * Analyse une page
 * Retourne un tableau des variables ACS, des balises et des inclusions
 */
function analyse_page($page, $mode_source) {
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
  if ($modeles_regexp)
      $reg[] = array('MODELE', 3, '(('.$modeles_regexp.')('.$reg_entre_accolades.'|'.$reg_filtre_spip.')*)');
  $reg[] = array('INCLURE', 3, '((?:<|#)INCLU[R|D]E[\s]*\{fond=([^\}]*)\}((?:[\s]*'.$reg_entre_accolades.')*)>?)');
  $reg[] = array('BOUCLE', 4, '(<BOUCLE[_]?([^(]*)\(([^)]*)\)((?:>=|[^\>])*)>)');
  $reg[] = array('FIN_BOUCLE', 2, '(<\/BOUCLE[_]?([^>]*)>)');
  if ($mode_source) {
    $reg[] = array('B', 1, '(<B_[^>]*>)');
    $reg[] = array('FIN_B', 1, '(<\/B_[^>]*>)');
    $reg[] = array('TRAD', 1, '(<:[\w]*:>)');
  }
  $reg[] = array('BALISE', 3, '(('.$balises.')('.$reg_entre_accolades.'|'.$reg_filtre_spip.')*)');

  $def = array();
  foreach ($reg as $rx) {
    $def[] = array('spip_tag' => $rx[0], 'nb' => $rx[1]);
    $regexp .= $rx[2].'|';
  }

  // La structure $analyse détermine l'analyse recursive avec regexp
  $analyse = array(
    'regexp' => '/'.substr($regexp, 0, -1).'/s',
    'regdef' => $def,
    'mode_source' => $mode_source
    );
  return page_includes($page, $analyse);
}

// Analyse séquentiellement la page, et retourne un tableau constitué
// d'un tableau des variables ACS trouveés
// et d'un tableau des contenus avec leurs positions.de début et de fin
// (fonction récursive)
function page_includes(
                $texte,       // Texte à analyser
                &$analyse,    // Structure d'analyse
                &$includes=array(
                  'tags' => array(),
                  'vars' => array()
                  ),					 // tableau des tages et des variables trouvés
                $offset=0,     // offset depuis le début de $pg
                $tagoffset=0   // offset depuis le début du tag
                ) {
  static $k;
  if ($k > 9999) return $includes;
  $k++;

  if (preg_match($analyse['regexp'], $texte, $matches, PREG_OFFSET_CAPTURE, $offset)) {
//print_r($matches);
    $indice = 1;
    foreach($analyse['regdef'] as $capture) {
      if ($matches[$indice][0]) {
        $args = array();
        $contenu = '';
//echo "<br>".$capture['f']."<br>";
        $matched = $matches[$indice][0];
        $debut = $matches[$indice][1];
        $fin = $debut + strlen($matched);
        for ($i = $indice; $i < $indice + $capture['nb']; $i++) {
//echo " [".($i)."][0]=".htmlspecialchars($matches[$i][0])."<br>";
          $args[] = $matches[$i][0];
        }
        if (is_callable('pi_'.$capture['spip_tag'])) {
          $contenu = call_user_func('pi_'.$capture['spip_tag'], array($args, &$analyse, &$includes));
          if (is_array($contenu)) {
          	if ($contenu[1])
          		$includes['vars'][] = $contenu[1];
          	$contenu = $contenu[0];
          }
        }
        if ($contenu)
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
  // Inclusion d'un composant
  if (substr($include, 0, 11) == 'composants/') {
    $r = affiche_widgy($include, $param, $args[1]['indentation'], $nic);
  }
  else {
    // inclusion classique
    if (find_in_path($include.'.html'))
      $r = indent($args[1]['indentation']).'<a class="'.get_widget_class($include, $param['on'], 'widgy').' lien_page" style="background: none" href="?exec=acs&onglet=page&pg='.$include.'" onclick=\'$("#page_infos").empty();
        AjaxSqueeze("?exec=acs_page_get_infos&pg=" + this.text, "page_infos");
        return false;\' title="'.$param.'">'.$include.'</a>';
    else
      $r = indent($args[1]['indentation']).'<a class="'.get_widget_class($include, $param['on'], 'widgy').' lien_page" style="background: #efefef; border-style: solid; color: red; text-decoration: blink; " title="'._T('acs:err_fichier_absent', array('file' => $include)).'">'.$include.'</a>';
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

function pi_BALISE($args) {
	$balise = substr($args[0][1], 1);
	// on a trouve une variable ACS presumee
	if ($balise == 'VAR') {
		// On lit le contenu entre accolades apres #VAR
		if (preg_match('/{acs(\w*)\}/', $args[0][2], $matches)) {
			$var = $matches[1];
			// on verifie que c'est bien une variable ACS :
			$lv = liste_variables();
			if (in_array($var, array_keys($lv))) {
				$c = $lv[$var]['c'];
				$html = '<a class="col_VAR" href="?exec=acs&amp;onglet=composants&amp;composant='.$c.($lv['c']['nic'] ? '&amp;nic='.$lv['c']['nic']: '').'" title="'._T('acs:composant').' '.ucfirst(str_replace('_', ' ', $c)).'">acs'.$var.'</a>';	
			}
		}
	}	
	return array(($args[1]['mode_source'] ? $args[0][0] : false), $html);
}

function affiche_widgy($include, $param, $indentation, $nic) {
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
  return widgy($composant, $param, $label, $indentation, $nic);
}

function widgy($composant, $param, $label='', $indentation=0, $nic = '', $recursive_indent = 0, $in_horizontal = false) {
  $label = ucfirst(str_replace('_', ' ', $label));
  
  $horizontal = ($GLOBALS['meta']['acs'.ucfirst($composant).$nic.'Orientation'] == "horizontal") ? true : false;
  $content .= $horizontal ? '<tr>' : '';  
  
  // On recherche ce que contient le widgy, recursivement
  $cv = composants_variables();

  if (is_array($cv) && is_array($cv[$composant]) && is_array($cv[$composant]['vars'])) {
    foreach($cv[$composant]['vars'] as $varname=>$v) {
    	if ($v['type'] != 'widget')
    		continue;
      $var = 'acs'.ucfirst($composant).$nic.ucfirst($varname);
      if (isset($GLOBALS['meta'][$var]) && $GLOBALS['meta'][$var]) {
      	$ci = explode('-', $GLOBALS['meta'][$var]);
      	$cinom = $ci[0];
      	$cinic = $ci[1];
      	$cilabel = $cinom;
      	$content .= (!$horizontal ? '<tr>' : '');
        $content .= '<td class="widgy_included_label"><a class="nompage" href="?exec=acs&onglet=composants&composant='.$composant.($nic ? '&nic='.$nic : '').'" title="acs'.$varname.'">'.substr($varname, strlen($composant.$nic)).'</a>'.widgy($cinom, '', $cilabel, $indentation, $cinic, 1, $horizontal).'</td>';
        $content .= (!$horizontal ? '</tr>' : '');
      }
    }
  }
  $content .= $horizontal ? '</tr>' : '';
 
	// On recupere le Nom du composant
	$cvn = 'acs'.ucfirst($composant).$nic.'Nom';
	if (isset($GLOBALS['meta'][$cvn])) {
		$lbl = $GLOBALS['meta'][$cvn];
		$title = $label.($nic ? ' '.$nic : ''). ' ('.$lbl.')';
		$lbl = str_replace(' ', '&nbsp;', couper(typo($lbl), 18));
	}
	else {
		$lbl =  $label.($nic ? '&nbsp;'.$nic : '');
		$title = _T('acs:composant').' '.$label.' '.$nic;
	}
	$titleHTML = ' title="'.$title.'"';
  // affichage du contenu du widgy avec l'indentation voulue
  $indentationHTML = $in_horizontal ? '' : '<td>'.indent($indentation + $recursive_indent).'</td>';
  $over = $cv[$composant]['over'];
  $on = ($GLOBALS['meta']['acs'.ucfirst($composant).$nic.'Use'] == "oui") ? true : false;;
  $r = '<table><tr>'.$indentationHTML.'<td'.($recursive_indent ? ' class="widgy_included"' : '').'>';
  $r .= '<table><tr><th><a class="'.get_widget_class($over, $on, 'widgy').'" href="?exec=acs&onglet=composants&composant='.$composant.($nic ? '&nic='.$nic : '').'"'.$titleHTML.'>'.widget_icon($composant, $nic, 10).'&nbsp;'.$lbl.'</a></th></tr>';
  $r .= $content;
  $r .= '</table></td></tr></table>';
  return $r;
}

function indent($l) {
  for($i = 0; $i < $l; $i++) {
    $r .= '&nbsp;&nbsp;&nbsp;';
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
  $balises_acs = 'INTRO, ACS_CHEMIN, HEADER_COMPOSANTS, PINCEAU, VAR, ACS_VERSION, ACS_RELEASE';

  $balises_spip = 'ANCRE_PAGINATION, ARRAY, ANCRE_PAGINATION, ARRAY, BIO, CACHE, CHAPO, CHARSET, CHEMIN, COMPTEUR_BOUCLE, CONFIG, DATE, DATE_MODIF, DATE_NOUVEAUTES, DATE_REDAC, DEBUT_SURLIGNE, DESCRIPTIF, DESCRIPTIF_SITE_SPIP, DISTANT, DOSSIER_SQUELETTE, EDIT, EMAIL, EMAIL_WEBMASTER, EMBED_DOCUMENT, ENV, EVAL, EXPOSE, EXPOSER, FICHIER, FIN_SURLIGNE, FORMULAIRE_ADMIN, FORMULAIRE_ECRIRE_AUTEUR, FORMULAIRE_FORUM, FORMULAIRE_INSCRIPTION, FORMULAIRE_RECHERCHE, FORMULAIRE_SIGNATURE, FORMULAIRE_SITE, GET, GRAND_TOTAL, HAUTEUR, HTTP_HEADER, ID_ARTICLE, ID_AUTEUR, ID_BREVE, ID_DOCUMENT, ID_FORUM, ID_GROUPE, ID_MOT, ID_PARENT, ID_RUBRIQUE, ID_SECTEUR, ID_SIGNATURE, ID_SYNDIC, ID_SYNDIC_ARTICLE, ID_THREAD, INCLURE, INSERT_HEAD, INTRODUCTION, IP, LANG, LANG_DIR, LANG_LEFT, LANG_RIGHT, LARGEUR, LESAUTEURS, LOGIN_PRIVE, LOGIN_PUBLIC, LOGO_ARTICLE, LOGO_ARTICLE_NORMAL, LOGO_ARTICLE_RUBRIQUE, LOGO_ARTICLE_SURVOL, LOGO_AUTEUR, LOGO_AUTEUR_NORMAL, LOGO_AUTEUR_SURVOL, LOGO_BREVE, LOGO_BREVE_RUBRIQUE, LOGO_DOCUMENT, LOGO_MOT, LOGO_RUBRIQUE, LOGO_RUBRIQUE_NORMAL, LOGO_RUBRIQUE_SURVOL, LOGO_SITE, LOGO_SITE_SPIP, MENU_LANG, MENU_LANG_ECRIRE, MESSAGE, MIME_TYPE, MODELE, NOM, NOM_SITE, NOM_SITE_SPIP, NOTES, PAGINATION, PARAMETRES_FORUM, PETITION, PGP, PIPELINE, POINTS, POPULARITE, POPULARITE_ABSOLUE, POPULARITE_MAX, POPULARITE_SITE, PS, PUCE, RECHERCHE, SELF, SET, SOURCE, SOUSTITRE, SPIP_CRON, SPIP_VERSION, SQUELETTE, SURTITRE, TAGS, TAILLE, TEXTE, TITRE, TOTAL_BOUCLE, TOTAL_UNIQUE, TYPE, TYPE_DOCUMENT, URL_ACTION_AUTEUR, URL_ARTICLE, URL_AUTEUR, URL_BREVE, URL_DOCUMENT, URL_FORUM, URL_LOGOUT, URL_MOT, URL_PAGE, URL_RUBRIQUE, URL_SITE, URL_SITE_SPIP, URL_SOURCE, URL_SYNDIC, VISITES';
  return $balises_acs.', '.$balises_spip;
}
?>