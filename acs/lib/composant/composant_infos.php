<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne les pages du squelette qui utilisent l'instance $nic du composant $c
 */
function composant_infos($c, $nic) {
  include (_DIR_PLUGIN_ACS.'lib/composant/composant_get_infos.php');
  $r ='';

  include_spip('lib/composant/composants_liste');
  $choixComposants = array_keys(composants_liste());

  if (!is_array($choixComposants))
    ajax_retour($r.'<div class="alert">'._T('acs:config_not_found').'</div>');

  $ca = array();
  foreach($GLOBALS['meta'] as $k => $v) {
    if ((substr($k, 0, 3) == 'acs') && ($v == $c.$nic)) {
      if (in_array($v, $choixComposants)) array_push($ca, substr($k, 3));
    }
  }

  if (count($ca)) {
    include_spip('lib/composant/composants_variables');
    $lv = liste_variables();
    if (is_array($lv)) {
      $r .= '<br /><div class="onlinehelp">'.(count($ca) > 1 ? _T('acs:containers') : _T('acs:container')).'</div>';
      foreach ($ca as $var) {
        if (isset($lv[$var]['c']))
          $r .= '<a class="nompage" href="?exec=acs&onglet=composants&composant='.$lv[$var]['c'].($lv[$var]['nic'] ? '&nic='.$lv[$var]['nic'] : '').'" title="'._T('acs:variable').' '.$var.'">'.ucfirst($lv[$var]['c']).(isset($lv[$var]['nic']) ? $lv[$var]['nic'] : '').'</a> ';
      }
	    $r .= '<br />';
    }
  }
  $l = liste_pages_composant(cGetPages($c, $nic), _T('acs:page'), _T('acs:pages'));
  $l .= liste_pages_composant(cGetPages($c, $nic, 'modeles'), _T('acs:modele'), _T('acs:modeles'));
  $l .= liste_pages_composant(cGetPages($c, $nic, 'formulaires'), _T('acs:formulaire'), _T('acs:formulaires'));
  foreach ($choixComposants as $cc) {
  	$l .= liste_pages_composant(cGetPages($c, $nic, 'composants/'.$cc), _T('acs:composant'), _T('acs:composants'));
  }
  if ($l)
  	$r.= '<br /><div class="onlinehelp">'._T('acs:includes').'</div>'.$l;
  $cp = 'composants/'.$c.'/';
  $traductions = cGetTraductions($c,$cp.'lang',';.*[.]php$;iS');
  $r .= '<br /><table width="100%"><tr><td colspan="2" class="onlinehelp">'.ucfirst(_T('spip:afficher_trad')).'</td></tr>';
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
    $r = '<div class="onlinehelp">'.(count($p['composant']) > 1 ? $pluriel : $singulier).'</div>';  
    foreach($p['composant'] as $page) {
      $r .= show_override($p['chemin'], $page).' ';
    }
    $r .= '<br />';
  }
  if (count($p['variables']) > 0) {
    $r .= '<i>';
    foreach($p['variables'] as $page=>$var) {
      $r .= show_override($p['chemin'], $page).' (';
      foreach($var as $v) {
        $r .= '<a title="'.htmlentities($GLOBALS['meta'][$v]).'">'.$v.'</a> ';
      }
      $r = rtrim($r);
      $r .= ')<br />';
    }
    $r .= '</i>';
  }
  return $r;
}

function show_override($chemin, $page) {
	if ($chemin)
		$chemin .= '/';
  if (isset($GLOBALS['meta']['acsSqueletteOverACS']) && is_readable('../'.$GLOBALS['meta']['acsSqueletteOverACS'].'/'.$page.'.html'))
    $r = '<u>'.$page.'</u>';
  else
    $r = $page;
  $r = '<a class="nompage" href="?exec=acs&onglet=pages&pg='.$chemin.$page.'">'.$r.'</a>';
  return $r;
}

?>