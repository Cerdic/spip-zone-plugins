<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne des informations détaillées sur une page
 * Utilise le cache ACS
 */

include_spip('inc/acs_presentation');
include_spip('inc/composant/page_source');

function acs_page_get_infos($page, $mode, $detail) {
  include_spip('inc/acs_cache');
  $mode_source = ($mode == 'source');
  $mode = $mode_source ? '_source' : '_infos'.$detail;
  $r = cache('page_get_infos', 'pg_'.$GLOBALS['meta']['acsSet'].'_'.urlencode($page).$mode, array($page, $mode_source, $detail));
  
  // Si le fichier a été modifié depuis la mise en cache, on force le recalcul
  $pg = find_in_path($page.'.html');
  $pg_derniere_modif = filemtime($pg);
  if ($r[2] < $pg_derniere_modif)
    $r = cache('page_get_infos', 'pg_'.$GLOBALS['meta']['acsSet'].'_'.urlencode($page).$mode, array($page, $mode_source, $detail), true);
  return $r[0];
}

// renvoie un widget avec les options d'affichage d'une page
function page_modes($page, $mode_source, $detail) {
  // Plieur
  if ($detail && ($detail > 1)) {
    $on = true;
    $detail ='';
  }
  else {
    $on = false;
    $detail = '&detail=2';
    
  }
  // Mode schema / source
  $link = '<a style="color: white" title="'.$page.'" href="?exec=acs&onglet=pages&pg='.$page;
  if ($mode_source) {
    $lblsrc = '<a id="mode_source" name="srcon" title="'.$page.'"><b>'._T('acs:source').'</b></a>';
    $lblsch = $link.'" id="mode_schema">'._T('acs:schema').'</a>';
  }
  else {
    $lblsrc = $link.'&mode=source" id="mode_source" name="srcoff">'._T('acs:source').'</a>';
    $lblsch =  '<a id="mode_schema"><b>'._T('acs:schema').'</b></a>';
  }
  // Rendu
  $r = '<table><tr><td>';
  $r .= $lblsrc.'</td><td> / </td><td> '.$lblsch.' </td><td> ';
  $r .= acs_plieur('plieur_spip_params', 'spip_params', '?exec=acs&onglet=pages&pg='.$page.$detail, $on);
  $r .= '</td><td>&nbsp;</td><td>'.acs_help_call('acs_bloc_page').'</td></tr></table>';
  return $r;
}

function page_get_infos($page, $mode_source=false, $detail='') {
  include_spip('inc/acs_widgets');

  $pg = find_in_path($page.'.html');
  $pg_derniere_modif = filemtime($pg);
  $pageContent = @file_get_contents($pg);
  $includes = analyse_page($pageContent, $mode_source);
  
  $r = acs_help_div('acs_bloc_page', _T('acs:pg_help').'<br /><br />');

  if (!$mode_source && (count($includes['vars']) > 0)) {
    $r .= '<div class="onlinehelp">'._T('acs:variables').' : '.
          implode(' ', $includes['vars']).
          '</div><br />';
    $infos = true;
  }
  ksort($includes['tags']);
  if ($mode_source) {
    $r .= '<div class="onlinehelp">'._T('acs:source_page').' : </div><div style="line-height: 1.5em;">';
    $dejalu = 0;
    $srcol = array();
    foreach ($includes['tags'] as $debut=>$tag) {
      //echo '<br/><div class="alert">¤ '.$tag['type'].' : '.$debut.'-'.$tag['fin'].'</div><br/>'
            //.htmlspecialchars(substr($pageContent, $debut, $tag['fin'] - $debut)).'' // debug code

      $source_tag = substr($pageContent, $debut, $tag['fin'] - $debut);
      if ($tag['contenu'])
        $spip_tag = $tag['contenu'];
      else
        $spip_tag = $source_tag;
      if ($debut > $dejalu) {
        $srcol[] = affiche_source($pageContent, $dejalu, $debut - $dejalu).'<span class="col_'.$tag['type'].'">'.affiche_source($spip_tag).'</span>';
        $dejalu = $tag['fin'];
      }
      else {  // il faut réécrire par dessus la dernière balise acs-spip (on espère que c'est la dernière ! ;-)
        $pos = strpos(end($srcol), $source_tag);
        if ($pos !== false) {
          $db = count($srcol) - 1;
          $srcol[$db] = substr($srcol[$db], 0, $pos).'<span class="col_'.$tag['type'].'">'.$spip_tag.'</span>'.substr($srcol[$db], $pos + strlen($source_tag));
        }
      }
    }
    $pagid = str_replace('/', '_slash_', str_replace('-', '_tiret_', $page));
    $src = '<div class="spip_source crayon source-'.$pagid.'-1">';
    $src .= ' '.nl2br(implode('',$srcol));
    if ($dejalu < strlen($pageContent))
      $src .= affiche_source($pageContent, $dejalu);
    $src .= '</div>';
    $r .= $src;
  }
  else {
    if (count($includes['tags'])) {
      $r .= '<div class="onlinehelp">'._T('acs:structure_page').' : </div><div class="structure_page">';
      foreach ($includes['tags'] as $debut=>$tag) {
        $schema .= ' '.$tag['contenu'];
      }
      $r .= $schema;
    }
    else
      $no_infos = true;
  }
  $r .= '</div><br />';

  if (isset($no_infos))
    $r = '<div>'._T('acs:page_rien_a_signaler').'</div><br />';

  $r .= '<table width="100%"><tr><td><span class="onlinehelp">'._T('acs:source').' : </span><a class="lien_source" href="?exec=acs&onglet=pages&pg='.$page.'&mode=source" title="'.read_perms($pg).'">'.substr($pg, 3).'</a></td>';
  $r .= '<td style="text-align:'.$GLOBALS['spip_lang_right'].'">'._T('acs:acsDerniereModif').' '.date('Y-m-d H:i:s', $pg_derniere_modif).'</td>';
  $r .= '</tr></table>';

  $r = acs_box(_T('acs:page').' '.$page, $r, _DIR_PLUGIN_ACS."images/page-24.gif", false, page_modes($page, $mode_source, $detail));
  return $r;
}

function affiche_source($txt, $debut=0, $longueur=0) {
  if ($longueur == 0)
    $longueur = strlen($txt) - $debut;

  $txt = substr($txt, $debut, $longueur);
  $txt = preg_replace(array('/</', '/>/'), array('&lt;', '&gt;'), $txt);

// Indentation et sauts de lignes
  $txt = explode("\n", $txt);
  foreach($txt as $n => $line) {
    $txt[$n] =  preg_replace('/ (?= )/s', '&nbsp;', $line);
  }
  $txt = implode('<br />', $txt);
  return $txt;
}

function read_perms($f) {
	$perms = fileperms($f);
  return substr(decoct($perms), 3);
}
?>