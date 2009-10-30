<?php
/**
 * D'apres le Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 *
 */

/**
 * Balise #COMPTEUR associee au critere compteur
 *
 * @param unknown_type $p
 * @return unknown
 */
function balise_COMPTEUR_dist($p) {
	return calculer_balise_criteres('compteur', $p);
}

/** Balise #SOMME associee au critere somme */
function balise_SOMME_dist($p) {
	return calculer_balise_criteres('somme', $p);
}

/** Balise #COMPTE associee au critere compte */
function balise_COMPTE_dist($p) {
	return calculer_balise_criteres('compte', $p);
}

/** Balise #MOYENNE associee au critere moyenne */
function balise_MOYENNE_dist($p) {
	return calculer_balise_criteres('moyenne', $p);
}

/** Balise #MINIMUM associee au critere moyenne */
function balise_MINIMUM_dist($p) {
	return calculer_balise_criteres('minimum', $p);
}

/** Balise #MAXIMUM associee au critere moyenne */
function balise_MAXIMUM_dist($p) {
	return calculer_balise_criteres('maximum', $p);
}

/** Balise #STATS associee au critere stats
 * #STATS{id_article,moyenne}
 */
function balise_STATS_dist($p) {
	if (isset($p->param[0][2][0])
	AND $nom = ($p->param[0][2][0]->texte)) {
		return calculer_balise_criteres($nom, $p, 'stats');
	}
	return $p;
}

function calculer_balise_criteres($nom, $p, $balise="") {
	$p->code = '';
	$balise = $balise ? $balise : $nom;
	if (isset($p->param[0][1][0])
	AND $champ = ($p->param[0][1][0]->texte)) {
		return rindex_pile($p, $nom."_$champ", $balise);
	}
  return $p;
}

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
