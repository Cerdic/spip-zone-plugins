<?php

function filtrer_valeurs_vides ($valeurs) {

  $valeurs_filtrees = array();

  if (isset($valeurs['action'])) unset($valeurs['action']);

  foreach ($valeurs as $objet) {
    $objet_est_vide = TRUE;
    foreach ($objet as $valeur) {
      if ($valeur !== '') {
        $objet_est_vide = FALSE;
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

function traitements_liste_objets_ok ($nom_saisie) {

  $valeurs = _request($nom_saisie);

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
          // recharger le formulaire, et celui-ci affichera un objet vide
          // prêt à remplir à la fin de la liste.
          break;
      }
    }

    set_request($nom_saisie, filtrer_valeurs_vides($valeurs));
    return FALSE;
  }

  set_request($nom_saisie, filtrer_valeurs_vides($valeurs));
  return TRUE;
}