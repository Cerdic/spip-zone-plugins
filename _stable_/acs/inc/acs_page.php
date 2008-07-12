<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_page_get_infos');
include_spip('inc/acs_pages');
include_spip('inc/acs_widgets');

function acs_page($page) {
  return '<div id="page_infos"><a name="page_infos"></a>'.
            acs_page_get_infos($page, _request('mode')).
         '</div>';
}

function acs_page_gauche($page) {
   return acs_info_box(
    _T('acs:page'),
    _T('acs:onglet_page_description').'<br /><br />',
    _T('acs:onglet_page_help'),
    _T('acs:onglet_page_info').'<br /><br />',
    _DIR_PLUGIN_ACS."img_pack/page-24.gif");
}

function acs_page_droite($page) {
  return acs_box(_T('acs:pages'), liste_pages_du_site('page'),_DIR_PLUGIN_ACS."/img_pack/pages-24.gif" );
}
?>
