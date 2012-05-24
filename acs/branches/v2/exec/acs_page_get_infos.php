<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_acs_page_get_infos () {
  include_spip('inc/acs_page_get_infos');
  ajax_retour(acs_page_get_infos(_request('pg'), _request('mode'), _request('detail')));
}

?>
