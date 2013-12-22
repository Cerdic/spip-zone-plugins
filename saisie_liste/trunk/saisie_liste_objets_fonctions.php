<?php

function compter_non_vides ($valeurs) {

  $i = 0;
  foreach ($valeurs as $objet) {
    foreach ($objet as $valeur) {
      if ($valeur !== '') {
        $i = $i + 1;
        break;
      }
    }
  }
  return $i;
}

function preparer_tableau_saisie ($tableau_saisie) {

  if (array_key_exists('saisie', $tableau_saisie)) {
    $resultat = array('saisie' => $tableau_saisie['saisie']);
    unset($tableau_saisie['saisie']);
    $resultat['options'] = $tableau_saisie;
    return $resultat;
  }
  else {
    return 'ERREUR SAISIE LISTE_OBJETS : mauvais paramètres.';
  }
}

function renommer_saisies ($tableau_saisie, $index_objet, $nom_objet) {

  $tableau_saisie['options']['nom'] = $nom_objet . "[" . $index_objet . "][" . $tableau_saisie['options']['nom'] . "]";

  return $tableau_saisie;
}

function charger_valeurs ($tableau_saisie, $valeurs, $index_objet, $nom_objet) {

  $tableau_saisie['options']['defaut'] = $valeurs[ $index_objet ][ $tableau_saisie['options']['nom'] ];

  return $tableau_saisie;
}