<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2012
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne les traductions disponibles d'un composant (avec cache ACS)
 */
function composant_traduction($c='fond', $l='fr', $context='') {
  include_spip('inc/acs_cache');
  $r = cache('affiche_composant_traduction', 'ct_'.$GLOBALS['meta']['acsSet'].'_'.$c.'_'.(($context != '') ? $context.'_' : '').$l, array($c, $l, $context));
  return $r[0];
}

/**
 * Affiche un tableau des traductions d'un composant, triées
 */
function affiche_composant_traduction($c, $l, $context) {
  $tableau = lecture_composant_traduction($c, $l, $context);
  ksort($tableau);
  $nb = count($tableau);
  $r = '<div class="acs_box">';
  $r .= '<img src="'._DIR_PLUGIN_ACS.'lang/flags/'.$l.'.gif" alt="'.$l.'" align="right" style="margin: 5px;" title="'.traduire_nom_langue($l).'" />';
  if ($context != 'ecrire')
  	$r .= "<div class='arial2 onlinehelp' style='padding-left: 2px'>"._T('acs:si_composant_actif').' : '._T('module_texte_explicatif')."</div>";
  $r .= "\n<table cellpadding='0' cellspacing='3px' border='0' style='width: 100%; border:0;'>";
  $r .= "\n<tr style='background: ".$GLOBALS['couleur_foncee']."'>".
  	"<th class='verdana1'>"._T('module_raccourci')."</th>\n<th class='verdana1'>"._T('module_texte_affiche')." ($nb)</th>\n".
  	"</tr>\n";

  $aff_nom_cadre = 'acs:'.$c.'_';
  foreach ($tableau as $raccourci => $val) {
    if (substr($raccourci, strlen($raccourci)-3, 3) == '_on') continue;
    if (substr($raccourci, strlen($raccourci)-4, 4) == '_off') continue;
    $bgcolor = ($i = ($i==0)) ? '#eeeeee' : 'white';
    $r .= "\n<tr style='background-color: $bgcolor; vertical-align: top;'><td class='verdana2' style='padding: 2px;'><b>";
    if ($context != 'ecrire')
    	$r .= "&lt;:$aff_nom_cadre";
    $r .= $raccourci;
    if ($context != 'ecrire') $r .= ":&gt;";
    // la classe crayon_$c_$raccourci  sert à donner un id unique, et type_traduction donne l'image du crayon.
    $r .= '</b></td><td class="arial2"><table  cellpadding="2px;" cellspacing="0" style="table-layout: fixed; width: 100%; overflow: hidden"><tr><td class="crayon crayon_'.$c.'_'.$raccourci.' type_traduction traduction-'.$c.'_'.$raccourci.'-0 lang_'.$l.($context ? '_'.$context : '').'">'.$val."</td></tr></table></td></tr>";
  }

  $r .= "</table><br /></div>";
  return $r;
}

/**
 * Retourne un tableau des traductions d'un composant
 */
function lecture_composant_traduction($c, $l, $context) {
  $langfile = 'composants/'.$c.(($context != '') ? '/'.$context : '').'/lang/'.$c.(($context != '') ? '_'.$context : '').'_'.$l.'.php';
  $idx = $GLOBALS['idx_lang'];
  $GLOBALS['idx_lang'] = 'i18n_acs_'.$c.$context.'_'.$l;
  $f = find_in_path($langfile);
  if (!is_file($f))
  	return array();
  include($f);
  $tableau = $GLOBALS['i18n_acs_'.$c.$context.'_'.$l];
  $GLOBALS['idx_lang'] = $idx;
  if (!is_array($tableau))
    $tableau = array();
  return $tableau;
}