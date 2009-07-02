<?php
/**
 * Cette balise génère une expression rationnelle pour le champ statut
 * afin de choisir le mode ("directe" ou pas) dans les boucles ARTICLES spip_articles_notations
 */
function balise_DEMOCRATIE_STATUT ($p) {
  if (isset($GLOBALS["meta"]['acsDemocratieDirecte']) && ($GLOBALS["meta"]['acsDemocratieDirecte'] == 'oui'))
    $p->code = '"(publie|prop)"';
  else
    $p->code = '"publie"';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

?>
