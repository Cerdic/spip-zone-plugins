<?php

function formulaires_demo_liste_2_saisies () {

  return decoder_yaml(find_in_path('formulaires/demo_liste_2.yaml'));
}

function formulaires_demo_liste_2_charger_dist () {

  return array(
           'liste_1' => _request('liste_1'),
           'liste_2' => _request('liste_2'),
         );
}

function formulaires_demo_liste_2_verifier_dist () {

  if (saisies_liste_verifier(array('liste_1', 'liste_2'))) return array();

  return array();
}

function formulaires_demo_liste_2_traiter_dist () {

  if (saisies_liste_traiter(array('liste_1', 'list_2')))
      return array('editable' => 'oui');

  return array('editable' => 'oui');
}
