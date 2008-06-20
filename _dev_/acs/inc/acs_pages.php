<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne la page d'admin "Pages"
 */
include_spip('inc/acs_widgets');

function acs_pages() {
  $r = acs_box(_T('acs:pages'), liste_pages_du_site('pages', true), _DIR_ACS."/img_pack/pages-24.gif");
  $r .= '<div id="page_infos"><a name="page_infos"></a>';
  if (_request('pg')) {
    include_spip('inc/acs_page_get_infos');
    $r .= acs_page_get_infos(_request('pg'), _request('mode'));
  }
  $r .= '</div>';

  $r.= '<a name="acs_vars"></a><div class="acs_vars_box pliable"><div id="acs_vars">';
  if (_request('show_vars') == 'oui') { // mode sans JQuery
    include_spip('inc/acs_page_get_all_variables');
    $r .= acs_page_get_all_variables();
  }
  $r.= '</div></div>';
  return $r;
}

function acs_pages_gauche() {
  return acs_info_box(
    _T('acs:acs'),
    _T('acs:description').'<br /><br />',
    _T('acs:help').'<br /><br />',
    _T('acs:info').'<br /><br />',
    _DIR_PLUGIN_ACS."img_pack/pages-24.gif",
    _T('acs:model_actif', array('model' => $GLOBALS['meta']['acsModel'])).
      (($GLOBALS['meta']['acsSqueletteOverACS']) ? _T('acs:overriden_by', array('over' => str_replace(':', ' ', $GLOBALS['meta']['acsSqueletteOverACS']))) : '')._T('acs:model_actif2').'<br /><br />',
    '<div class="onlinehelp">'.
      acs_plieur('plieur_acs_vars_box', 'acs_vars_box', '?exec=acs&onglet=pages&show_vars=oui#acs_vars',
        false,
        'if (typeof pavb == \'undefined\') {AjaxSqueeze(\'?exec=acs_page_get_all_variables\',\'acs_vars\'); pavb = true;}',
        _T('acs:toutes_les_variables').'</div>'
      )
  );
}

function acs_pages_droite() {
  $cIconDef = _DIR_PLUGIN_ACS."/img_pack/composant-24.gif";

  $configfile = find_in_path('composants/config.php');
  @include $configfile;
  if (is_array($choixComposants))
    $l = liste_widgets($choixComposants, true, false);
  else
    $l = '&nbsp;';

  return acs_box(count($choixComposants).' '.((count($choixComposants)==1) ? strtolower(_T('composant')) : strtolower(_T('composants'))), $l, $cIconDef, 'acs_box_composants');
}

/**
 * Lit la liste des pages, modèles, et formulaires
 */
function liste_pages_du_site($onglet, $large=false) {
  include_spip('lib/composant/pages_liste');

  if ($large)
    $r = '<table width="100%" class="liste_pages">';
  foreach(pages_liste() as $dir=>$pages) {
    $misenpage = array();
    $misenpage['pg'] = array();
    $misenpage['inc'] = array();
    foreach($pages as $pagename=>$pageparam) {
      $link = (($dir != '') ? $dir."/" : "").$pagename;

      $link = '<a class="page_lien nompage" href="?exec=acs&onglet='.$onglet.'&pg='.$link.'" title="'.$link.'">';
      if (substr($pageparam['source'], 0, 4) == 'over')
        $page = $link.'<u>'.$pagename.'</u></a>'; // Highlight override
      else if ($pageparam['source'] == 'acs')
        $page = $link.'<b>'.$pagename.'</b></a>';
      else if (substr($pageparam['source'], 0, 7) == 'plugin_')
        $page = $link.'<i>'.$pagename.'</i></a>';
      else
        $page = $link.$pagename.'</a>';
      if (substr($pagename, 0, 4) == 'inc-')
        $misenpage['inc'][] = $page;
      else
        $misenpage['pg'][] = $page;
    }
    if (count($misenpage['pg']) > 0) {
      if ($large) $r .= '<tr><td>';
      $r .= '<span class="onlinehelp">'._T('acs:'.($dir ? $dir : 'pages')).'</span>';
      if ($large) $r .= '</td><td style="padding-left: 5px;"> '; else $r .= '<br />';
      $r .= implode(' ', $misenpage['pg']);
      if ($large) $r .= '</td></tr>'; else $r .= '<br />';
     }
    if (count($misenpage['inc']) > 0) {
      if ($large) $r .= '<tr><td>';
      $r .= '<span class="onlinehelp">'._T('acs:includes').'</span>';
      if ($large) $r .= '</td><td style="padding-left: 5px;"> '; else $r .= '<br />';
      $r .= implode(' ', $misenpage['inc']);
      if ($large) $r .= '</td></tr>'; else $r .= '<br />';
    }
    if ($large) $r .= '<tr class="liste_pages_sep"><td colspan="2"></td></tr>'; else $r .= '<br />';
  }
  if ($large) $r .= '</table>';

  $r .= '<table width="100%"><tr><td>'._T('version').' <a style="color: black" title="Spip '.implode(', ', $GLOBALS['acs_table_versions_spip']).'">ACS '.ACS_VERSION.' ('.ACS_RELEASE.')</a></td><td style="text-align:'.$GLOBALS['spip_lang_right'].'">'._T('acs:acsDerniereModif').' '.date("Y-m-d H:i:s", $GLOBALS['meta']['acsDerniereModif']).'</td></tr></table>';
  return $r;
}

?>