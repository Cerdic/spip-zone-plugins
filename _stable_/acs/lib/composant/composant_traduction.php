<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne les traductions disponibles d'un composant
 */
function composant_traduction($c='fond', $l='fr', $module='') {
  include_spip('inc/acs_cache');
  $r = cache('lecture_composant_traduction', 'ct_'.$GLOBALS['meta']['acsModel'].'_'.$c.'_'.(($module != '') ? $module.'_' : '').$l, array($c, $l, $module));
  return $r[0];
}

function lecture_composant_traduction($c='fond', $l='fr', $module='') {
  $langfile = 'composants/'.$c.(($module != '') ? '/'.$module : '').'/lang/'.$c.(($module != '') ? '_'.$module : '').'_'.$l.'.php';
  $idx = $GLOBALS['idx_lang'];
  $GLOBALS['idx_lang'] = 'i18n_acs_'.$c.$module.'_'.$l;
  $f = find_in_path($langfile);
  include($f);
  $tableau = $GLOBALS['i18n_acs_'.$c.$module.'_'.$l];
  $GLOBALS['idx_lang'] = $idx;

  if (!is_array($tableau)) $tableau = array();

  ksort($tableau);
  $nb = count($tableau);
  $r = '<div class="acs_box">';
  if ($module != 'ecrire') $r .= "<div class='arial2 onlinehelp' style='padding-left: 2px'>"._T('acs:si_composant_actif').' : '._T('module_texte_explicatif')."</div>";
  $r .= "\n<table cellpadding='3' cellspacing='1' border='0' width=\"100%\">";
  $r .= "\n<tr style='background: ".$GLOBALS['couleur_foncee']."'><td class='verdana1'><b>"._T('module_raccourci')."</b></td>\n<td class='verdana2'><b>"._T('module_texte_affiche')."</b> ($nb)</td>\n<td>".'<img src="'._DIR_PLUGIN_ACS.'lang/flags/'.$l.'.gif" alt="'.$l.'" align="right" />'."</td></tr>\n";

  $aff_nom_module = 'acs:'.$c.'_';
  foreach ($tableau as $raccourci => $val) {
    if (substr($raccourci, strlen($raccourci)-3, 3) == '_on') continue;
    if (substr($raccourci, strlen($raccourci)-4, 4) == '_off') continue;
    $bgcolor = ($i = ($i==0)) ? '#eeeeee' : 'white';
    $r .= "\n<tr style='background-color: $bgcolor; vertical-align: top;'><td class='verdana2'><b>";
    if ($module != 'ecrire') $r .= "&lt;:$aff_nom_module";
    $r .= $raccourci;
    if ($module != 'ecrire') $r .= ":&gt;";
    $r .= "</b></td>\n<td class='arial2' colspan='2'>".$val."</td></tr>";
  }

  $r .= "</table><br /></div>";
  return $r;
}