<?php

function formulaires_demo_liste_3_saisies () {

  return decoder_yaml(find_in_path('formulaires/demo_liste_3.yaml'));
}

function formulaires_demo_liste_3_charger_dist () {

  return array(
           'messages' => _request('messages'),
         );
}

function formulaires_demo_liste_3_verifier_dist () {

  if (saisies_liste_verifier('messages')) return array();

  return array();
}

function formulaires_demo_liste_3_traiter_dist () {

  if (saisies_liste_traiter('messages'))
      return array('editable' => 'oui');

  return array('editable' => 'oui');
}
