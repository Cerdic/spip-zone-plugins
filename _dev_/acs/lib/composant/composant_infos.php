<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne les pages du squelette qui utilisent le composant $c
 */
function composant_infos() {
  $c = _request('c');

  include (_DIR_PLUGIN_ACS.'lib/composant/composant_get_infos.php');
  $r ='';

  include_spip('lib/composant/composants_liste');
  $choixComposants = array_keys(composants_liste());

  if (!is_array($choixComposants))
    ajax_retour($r.'<div class="alert">'._T('acs:config_not_found').'</div>');

  $ca = array();
  foreach($GLOBALS['meta'] as $k => $v) {
    if ((substr($k, 0, 3) == 'acs') && ($v == $c)) {
      if (in_array($v, $choixComposants)) array_push($ca, substr($k, 3));
    }
  }
  if (count($ca)) {
    include_spip('lib/composant/composants_variables');
    $lc = composants_variables();
    $r .= '<div class="onlinehelp">'.(count($ca) > 1 ? _T('acs:containers') : _T('acs:container')).'</div>';
    if (is_array($lc)) {
      foreach ($ca as $var) {
        $ci = $lc[$var]['composant'];
        if ($ci)
          $r .= '<a class="nompage" href="?exec=acs&onglet=composants&composant='.$ci.'" title="'._T('acs:variable').' '.$var.'">'.ucfirst($ci).'</a>';
      }
    }
    $r .= '<br /><br />';
  }
  $r .= liste_pages_composant(cGetPages($c), _T('acs:page'), _T('acs:pages'));
  $r .= liste_pages_composant(cGetPages($c, 'modeles'), _T('acs:modele'), _T('acs:modeles'));
  $r .= liste_pages_composant(cGetPages($c, 'formulaires'), _T('acs:formulaire'), _T('acs:formulaires'));
  $r .= liste_pages_composant(cGetPages($c, 'composants'), _T('acs:composant'), _T('acs:composants'));
  $cp = 'composants/'.$c.'/';
  $traductions = cGetTraductions($c,$cp.'lang',';.*[.]php$;iS');
  $r .= '<table width="100%"><tr><td colspan="2" class="onlinehelp">'.ucfirst(_T('spip:afficher_trad')).'</td></tr>';
  $r .= (count($traductions[0]) ? '<tr><td style="width:10%; vertical-align: top;" align="'.$GLOBALS['spip_lang_right'].'"> '._T('acs:public').' </td><td>'.liens_traductions($c, $traductions[0]).'</td></tr>' : '');
  $r .= (count($traductions[1]) ? '<tr><td style="width:10%; vertical-align: top;" align="'.$GLOBALS['spip_lang_right'].'"> '._T('acs:ecrire').' </td><td>'.liens_traductions($c, $traductions[1], 'ecrire').'</td></tr>' : '');
  $r .= '</table><br />';

  return $r;
}

function liens_traductions($c, $langs, $module='') {
/*this.href=\''.'?exec=acs_config&onglet=composants&composant='.$c.'&trcmp='.$lang.'&module='.$module.'\';"*/
  foreach($langs as $lang) {
    $url = '?exec=composant_get_trad&c='.$c.'&trcmp='.$lang.'&module='.$module;
    $r .= ' <a href="#cTrad" title="'.traduire_nom_langue($lang).'" onclick="AjaxSqueeze(\''.$url.'\',\'cTrad\');" ><img src="'._DIR_PLUGIN_ACS.'lang/flags/'.$lang.'.gif" alt="'.$lang.'" /></a> ';
  }
  return $r;
}

function liste_pages_composant($p, $singulier, $pluriel) {

  if (count($p['composant']) > 0) {
    $r = '<div class="onlinehelp">'.(count($p) > 1 ? $pluriel : $singulier).'</div>';
    foreach($p['composant'] as $page) {
      $r .= show_override($page).' ';
    }
    $r .= '<br />';
    $ok = true;
  }
  if (count($p['variables']) > 0) {
    $r .= '<i>';
    foreach($p['variables'] as $page=>$var) {
      $r .= show_override($page).' (';
      foreach($var as $v) {
        $r .= '<a title="'.$GLOBALS['meta'][$v].'">'.$v.'</a> ';
      }
      $r = rtrim($r);
      $r .= ')<br />';
    }
    $r .= '</i>';
    $ok = true;
  }
  if (isset($ok) && $ok) $r .= '<br />';
  return $r;
}

function show_override($page) {
  if (!isset($GLOBALS['meta']['acsSqueletteOverACS'])) return $page;
  if (is_readable('../'.$GLOBALS['meta']['acsSqueletteOverACS'].'/'.$page.'.html'))
    $r = '<u>'.$page.'</u>';
  else
    $r = $page;
  $r = '<a class="nompage" href="?exec=acs&onglet=pages&pg='.$page.'">'.$r.'</a>';
  return $r;
}

?>