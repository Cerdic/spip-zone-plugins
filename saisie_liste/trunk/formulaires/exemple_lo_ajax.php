<?php

function formulaires_exemple_lo_ajax_saisies () {

  return decoder_yaml(find_in_path('formulaires/exemple_lo_ajax.yaml'));
}

function formulaires_exemple_lo_ajax_charger_dist () {

  return array(
           'liste_1' => _request('liste_1'),
           'liste_2' => _request('liste_2'),
         );
}

function formulaires_exemple_lo_ajax_verifier_dist () {

  if (saisies_liste_verifier(array('liste_1', 'liste_2'))) return array();

  return array();
}

function formulaires_exemple_lo_ajax_traiter_dist () {

  if (saisies_liste_traiter(array('liste_1', 'list_2')))
      return array('editable' => 'oui');

  return array('editable' => 'oui');
}
