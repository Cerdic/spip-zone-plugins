<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Implémentation du pipeline insert_head pour le plugin ACS
 */
function acs_insert_head($flux) {
  $flux = '<script src="?page=jquery.js" type="text/javascript"></script>';
  $js = find_in_path('acs.js.html');
  if ($js)
    $flux .= '<script type="text/javascript" src="spip.php?page=acs.js"></script>';
  return $flux;
}
?>