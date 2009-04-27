<?php
if (!defined("_ECRIRE_INC_VERSION")) return; // sécurité

/**
 * Balise pour tester que les différents caches ont bien été
 * rafraîchis
 */
function balise_NOCACHE_HORODATAGE_dist($p)
{
  $p->code .= "' Cache squelette = ' . '" . strftime('%T') . "'";
  $p->code .= " . ' - Cache page = ' . strftime('%T')";
  return $p;
}
