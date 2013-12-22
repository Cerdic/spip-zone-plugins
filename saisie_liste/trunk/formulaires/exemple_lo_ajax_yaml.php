<?php

function formulaires_exemple_lo_ajax_yaml_charger_dist () {

  return array(
           'liste_1' => _request('liste_1'),
         );
}

function formulaires_exemple_lo_ajax_yaml_verifier_dist () {

  $erreurs = liste_verifier('liste_1');

  return $erreurs;
}

function formulaires_exemple_lo_ajax_yaml_traiter_dist () {

  if (liste_traiter('liste_1')) return;
}
