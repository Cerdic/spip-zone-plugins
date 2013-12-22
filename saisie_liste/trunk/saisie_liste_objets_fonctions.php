<?php

function enumerer ($max) {

  $resultat = array();
  for ($i=0; $i<=$max; $i++) {
    $resultat[] = $i;
  }
  return $resultat;
}

function joindre ($tableau, $liant) {

  return implode($liant, $tableau);
}

function filtrer_valeurs_vides ($valeurs) {

  $valeurs_filtrees = array();

  unset($valeurs['action']);
  unset($valeurs['permutations']);

  foreach ($valeurs as $objet) {
    $objet_est_vide = TRUE;
    if (is_array($objet)) {
      foreach ($objet as $valeur) {
        if ($valeur !== '') {
          $objet_est_vide = FALSE;
        }
      }
    }
    if ( ! $objet_est_vide) {
      $valeurs_filtrees[] = $objet;
    }
  }

  return $valeurs_filtrees;
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

function permuter ($tableau, $permutations) {

  $resultat = array();
  for ($i=0; $i<count($permutations); $i++) {
    $resultat[$i] = $tableau[$permutations[$i]];
  }
  return $resultat;
}

function traitements_liste_objets ($nom_saisie) {

  $valeurs = _request($nom_saisie);
  $permutations = explode(',', $valeurs['permutations']);

  if (array_key_exists('action', $valeurs)) {
    foreach ($valeurs['action'] as $details_action => $valeur_submit) {
      $details_action = explode('-', $details_action);
      $action      = $details_action[0];
      $index_objet = $details_action[1];
      switch ($action) {
        case 'supprimer':
          unset($valeurs[intval($index_objet)]);
          break;
        case 'ajouter':
          // on n'as rien à faire pour ajouter un objet, il suffit de
          // recharger le formulaire
          break;
        case 'monter':
          // il faut opérer sur la liste des permutations, parce ce qu'elle
          // correspond à l'ordre des objets affichés quand l'utilisateur
          // a submit.
          $index_objet = array_search($index_objet, $permutations);
          $objet_en_dessus = $permutations[$index_objet-1];
          $permutations[$index_objet-1] = $permutations[$index_objet];
          $permutations[$index_objet]   = $objet_au_dessus;
          break;
        case 'descendre':
          $index_objet = array_search($index_objet, $permutations);
          $objet_en_dessous = $permutations[$index_objet+1];
          $permutations[$index_objet+1] = $permutations[$index_objet];
          $permutations[$index_objet]   = $objet_en_dessous;
          break;
      }
    }

    set_request($nom_saisie, filtrer_valeurs_vides(
                                 permuter($valeurs, $permutations)));

    return $erreurs = array($nom_saisie => $action . '-' . $index_objet . ' ok');
  }

  set_request($nom_saisie, filtrer_valeurs_vides(
                               permuter($valeurs, $permutations)));
  return FALSE;
}