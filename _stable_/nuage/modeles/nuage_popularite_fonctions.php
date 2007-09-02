<?php

function filtre_calculer_nuage($titres, $urls, $poids, $expose) {
  $resultat = array();
  $max = empty($poids)?0:max($poids);
  if($max>0){
    foreach ($titres as $id => $t) {
      $score = $poids[$id]/$max; # entre 0 et 1
      $score = pow($score,1.5); # lissage
      $s = ceil(15*$score);
      $resultat[$t] = array(
        'url'   => $urls[$id],
        'poids' => ($poids[$id]?$poids[$id]:"0")."/".$max,
        'taille' => $s,
        'expose' => filtre_find($expose, $id)
      );
    }
  }
  return $resultat;
}

?>