<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_page_get_infos');
include_spip('inc/acs_widgets');

function acs_pages($page) {
  $allvars.= '<a name="acs_vars"></a><div class="acs_vars_box pliable"><div id="acs_vars">';
  if (_request('show_vars') == 'oui') { // mode sans JQuery
    include_spip('inc/acs_page_get_all_variables');
    $allvars .= acs_page_get_all_variables();
  }
  $allvars.= '</div></div>';
  return $allvars.'<div id="page_infos"><a name="page_infos"></a>'.acs_page_get_infos($page, _request('mode'), _request('detail')).'</div>';
}

function acs_pages_gauche($page) {
  return acs_info_box(
    _T('acs:acs'),
    _T('assistant_configuration_squelettes').'<br /><br />',
    _T('acs:onglet_pages_help'),
    _T('acs:onglet_pages_info').'<br /><br />',
    _DIR_PLUGIN_ACS."img_pack/acs_32x32.gif",
    _T('acs:model_actif', array('model' => $GLOBALS['meta']['acsModel'])).
    (($GLOBALS['meta']['acsSqueletteOverACS']) ? 
      _T('acs:overriden_by', array('over' => str_replace(':', ' ', $GLOBALS['meta']['acsSqueletteOverACS'])))
       :
     	''
    ).
    _T('acs:model_actif2').
    '<br /><br />'.
    '<div class="onlinehelp">'.
    acs_plieur('plieur_acs_vars_box', 'acs_vars_box', '?exec=acs&onglet=pages&show_vars=oui#acs_vars',
    false,
    'if (typeof pavb == \'undefined\') {AjaxSqueeze(\'?exec=acs_page_get_all_variables\',\'acs_vars\'); pavb = true;}',
    _T('acs:toutes_les_variables').'</div>'.
    '<br />'
    )
  );
}

function acs_pages_droite($page) {
  return acs_box(_T('acs:pages'), liste_pages_du_site('pages'),_DIR_PLUGIN_ACS."/img_pack/pages-24.gif" );
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
  return $r;
}
?>
