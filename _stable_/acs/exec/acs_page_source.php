<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_acs_page_source () {
  $pg = _request('pg');

  include_spip('lib/page_source');
  ajax_retour(page_source($pg));
}

?>
