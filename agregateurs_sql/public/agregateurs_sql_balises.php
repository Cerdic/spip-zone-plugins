<?php
/**
 * Balise #EXPRESSION
 * 
 * (c) 2009 Renato
 * Licence GPL
 *
 */



function balise_SUM($p) {
  $t = $p->param[0][1][0]->texte;
  $p->param[0][1][0]->texte = "SUM($t)";
  $p->param[0][2][0]->texte = "sum_$t";
  return balise_EXPRESSION($p); 
}

function balise_EXPRESSION($p) {
  static $num = 1;
  $b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
  if ($b === '' || !isset($p->boucles[$b])) {
     erreur_squelette(
       _T('zbug_champ_hors_boucle',
       array('champ' => "#$b" . 'EXPRESSION')
     ), $p->id_boucle);
     $p->code = "''";
  } else {
    if (isset($p->param[0][1][0])
    AND $champ = ($p->param[0][1][0]->texte)) {
      if(isset($p->param[0][2][0]) AND $p->param[0][2][0]->texte)
        $alias = $p->param[0][2][0]->texte;
      else {
        $alias = "expr_$num";
        $num++;
      }
      $p->code = "\$Pile[\$SP]['$alias']";
      $p->boucles[$b]->select[] = "$champ as $alias";
      $p->interdire_scripts = true;
    } else {
       erreur_squelette(
         "pas de balises dans #EXPRESSION", $p->id_boucle);
      $p->code = "''";
    }
  }
  return $p;
}

?>
