<?php

function balise_VISITES_ARTICLES($p) {
  $p->code = 'calcule_balise_stats(\'visites_articles\')';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function balise_TOTAL_VISITES($p) {
  $p->code = 'calcule_balise_stats(\'total_visites\')';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function balise_NB_ARTICLES($p) {
  $p->code = 'calcule_balise_stats(\'nb_articles\')';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function balise_NB_AUTEURS($p) {
  $p->code = 'calcule_balise_stats(\'nb_auteurs\')';
  $p->statut = 'php';
  $p->interdire_scripts = false;
  return $p;
}

function calcule_balise_stats($stat) {
	$req = array('total_visites' => array('SUM(visites)','spip_visites'),
               'nb_articles' => array('COUNT(*)','spip_articles', 'statut = "publie"'),
               'nb_auteurs' => array('COUNT(*)','spip_auteurs', 'statut <> "nouveau"'),
               'visites_articles' => array('SUM(visites)','spip_articles')
              );
  $req = $req[$stat];
  if (!is_array($req) || !count($req))
  	return '';
  return sql_getfetsel($req[0], $req[1], $req[2]);
}
?>