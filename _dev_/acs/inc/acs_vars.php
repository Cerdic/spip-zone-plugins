<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_page_get_all_variables');

function acs_vars_gauche(){
  return acs_box(_T('acs:variables'), _T('acs:toutes_les_variables'), _DIR_PLUGIN_ACS.'/img_pack/vars-24.gif', false, '<img src="'._DIR_PLUGIN_ACS.'/img_pack/info.png" />');
}

function acs_vars() {
  return acs_page_get_all_variables();
}

function acs_vars_droite() {
  
}
?>