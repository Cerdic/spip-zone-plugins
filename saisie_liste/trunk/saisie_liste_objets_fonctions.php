<?php

function liste_objets_numeroter_saisie ($saisie, $nom, $no) {

  $saisie = preg_replace('/name="([^"]+)"/i',
                         'name="' . $nom . '[' . $no . ']' . '[$1]"',
                         $saisie);

  $saisie = preg_replace('/id="champ_([^"]+)"/i',
                         'id="champ_' . $nom . '[' . $no . ']' . '[$1]"',
                         $saisie);

  return $saisie;
}

function extraire_nom_saisie ($saisie) {

  preg_match('/name="([^"]+)"/xi', $saisie, $matches);
  return $matches[1];
}

function liste_objets_charger_valeur_saisie ($saisie, $valeur) {

  if (preg_match('/value="([^"]+)"/i', $saisie, $matches) === 1) {
    $saisie = preg_replace('/value="([^"]+)"/i',
                           'value="' . $valeur . '"',
                           $saisie);
  } else {
    $saisie = preg_replace('/<input/i',
                           '<input value="' . $valeur . '"',
                           $saisie);
  }

  return $saisie;
}