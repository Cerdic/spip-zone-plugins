<?php

function balise_AGENDA($p) {
  $annee = interprete_argument_balise(1,$p);
  $mois = interprete_argument_balise(2,$p);
	$p->code = 'calculer_balise_agenda('.$annee.', '.$mois.')';
	$p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function calculer_balise_agenda($annee, $mois) {
	return 'toto';
}
?>