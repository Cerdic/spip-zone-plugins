<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Inclut les balises spip définies par les composants actifs
 * Include components defined spip-tags
 */
require_once _DIR_ACS.'lib/composant/composants_actifs.php';

function composants_ajouter_balises() {
  foreach (composants_actifs() as $c) {
    $bc= find_in_path('composants/'."$c/$c".'_balises.php');
    if ($bc)
      include($bc); // Les erreurs ne doivent JAMAIS être masquées, ici
  }
}
?>
