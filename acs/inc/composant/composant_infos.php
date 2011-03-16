<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne les pages du squelette qui utilisent l'instance $nic du composant $c
 */
function composant_infos($c, $nic) {
  include (_DIR_PLUGIN_ACS.'inc/composant/composant_get_infos.php');
  include_spip('inc/composant/composants_liste');
  $r ='<br />';
  
  // On calcule la liste de toutes les instances de composants actifs
  $choixComposants = array();
  foreach(composants_liste() as $class=>$composant) {
  	foreach($composant['instances'] as $instance=>$params) {
  		if ($params['on'] == 'oui')
  			array_push($choixComposants, $class.($instance != 0 ? '-'.$instance :''));
  	}
  }

  if (count($choixComposants) == 0)
    ajax_retour($r.'<div class="alert">'._T('acs:config_not_found').'</div>');

  // On établit la liste de toutes les variables ACS ayant pour valeur le nom du composant $c.$nic
  $ca = array();
  foreach($GLOBALS['meta'] as $k => $v) {
    if ((substr($k, 0, 3) == 'acs') && ($v == $c.($nic ? '-'.$nic: ''))) {
      if (in_array($v, $choixComposants)) array_push($ca, substr($k, 3));
    }
  }

  // On retourne la liste de tous les composants qui contiennent ce composant
  if (count($ca)) {
    include_spip('inc/composant/composants_variables');
    $lv = liste_variables();
    if (is_array($lv)) {
      $r .= '<span class="onlinehelp">'._T('acs:used_in').'</span> ';
      foreach ($ca as $var) {
        if (isset($lv[$var]['c'])) {
        	$pc = $lv[$var]['c'];
        	$pnic = $lv[$var]['nic'];
        	$title = _T('acs:variable').' '.$var;
        	if (isset($GLOBALS['meta']['acs'.ucfirst($pc).$pnic.'Nom']))
        		$title = $GLOBALS['meta']['acs'.ucfirst($pc).$pnic.'Nom'].' ('.$title.')';
          $r .= '<a class="nompage" href="?exec=acs&onglet=composants&composant='.$pc.($pnic ? '&nic='.$pnic : '').'" title="'.$title.'">'.ucfirst($pc).(isset($pnic) ? $pnic : '').'</a> ';
        }
      }
      $r .= '<hr />';
    }
  }

  // On cherche toutes les pages qui contiennent ce composant
  $l = liste_pages_composant(cGetPages($c, $nic), _T('acs:page'), _T('acs:pages'));
  $l .= liste_pages_composant(cGetPages($c, $nic, 'modeles'), _T('acs:modele'), _T('acs:modeles'));
  $l .= liste_pages_composant(cGetPages($c, $nic, 'formulaires'), _T('acs:formulaire'), _T('acs:formulaires'));
  foreach (composants_liste() as $class=>$composant) {
  	$l .= liste_pages_composant(cGetPages($c, $nic, 'composants/'.$class), _T('acs:composant'), _T('acs:composants'));
  }
  if ($l)
  	$r.= $l;

	$traductions = cGetTraductions($c,'composants/'.$c.'/lang',';.*[.]php$;iS');
  $r .= '<hr /><table width="100%"><tr><td colspan="2" class="onlinehelp centre">'.ucfirst(_T('spip:afficher_trad')).'</td></tr>';
  $r .= (count($traductions[0]) ? '<tr><td style="width:10%; vertical-align: top;" align="'.$GLOBALS['spip_lang_right'].'"> '._T('acs:public').' </td><td>'.liens_traductions($c, $traductions[0]).'</td></tr>' : '');
  $r .= (count($traductions[1]) ? '<tr><td style="width:10%; vertical-align: top;" align="'.$GLOBALS['spip_lang_right'].'"> '._T('acs:ecrire').' </td><td>'.liens_traductions($c, $traductions[1], 'ecrire').'</td></tr>' : '');
  $r .= '</table>';
  return $r;
}

function liens_traductions($c, $langs, $cadre='') {
  foreach($langs as $lang) {
    $url = '?exec=composant_get_trad&c='.$c.'&trcmp='.$lang.'&cadre='.$cadre;
    $r .= ' <a href="#cTrad" title="'.traduire_nom_langue($lang).'" onclick="AjaxSqueeze(\''.$url.'\',\'cTrad\');" ><img src="'._DIR_PLUGIN_ACS.'lang/flags/'.$lang.'.gif" alt="'.$lang.'" /></a> ';
  }
  return $r;
}

function liste_pages_composant($p, $singulier, $pluriel) {
  if (count($p['composant']) > 0) {
    $r = '<span class="onlinehelp">'.(count($p['composant']) > 1 ? $pluriel : $singulier).'</span> ';  
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