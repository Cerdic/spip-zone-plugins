<?php

function formulaires_tester_saisie_liste_objets_ajax_yaml_charger_dist () {

  return array(
               'liste_1' => _request('liste_1'),
         );
}

function formulaires_tester_saisie_liste_objets_ajax_yaml_verifier_dist () {

  if (liste_objets_verifier('liste_1')) return;

}

function formulaires_tester_saisie_liste_objets_ajax_yaml_traiter_dist () {

  if (liste_objets_traiter('liste_1')) return;

  /* var_dump(_request('liste_1')); */
}
