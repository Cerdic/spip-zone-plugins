<?php
function parser_dossier_squelettes() {
  global $dossier_squelettes;
  return split(':',$dossier_squelettes);
}

function creer_dossier_squelettes($liste) {
  global $dossier_squelettes;
  $dossier_squelettes = join(':',$liste);
}

function ajouter_profil() {
  $connect_statut = $GLOBALS['auteur_session']['statut'];

  $profil = substr($connect_statut,1);

  $dossiers = parser_dossier_squelettes();
  $final = array();
  foreach($dossiers as $d) {
	$final[] = "$d-$profil";
	$final[] = $d;
  }
  creer_dossier_squelettes($final);
}

ajouter_profil();
?>