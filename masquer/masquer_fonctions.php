<?php
if (!function_exists('critere_tout_dist')){
  function critere_tout_dist($idb, &$boucles, $crit) {
    $boucle = &$boucles[$idb];
    $boucle->modificateur['tout'] = true;
  }
}
?>