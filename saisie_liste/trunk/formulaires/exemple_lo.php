<?php

function formulaires_exemple_lo_charger_dist () {

  return array(
           'liste_1' => array(
                          0 => array(
                                 'titre_objet' => 'Un bel objet',
                                 'description_objet' => 'bla bla bla bla',
                               ),
                        ),
           'liste_2' => _request('liste_2'),
         );
}

function formulaires_exemple_lo_verifier_dist () {

  if (saisies_liste_verifier(array('liste_1', 'liste_2')))
    return array();

  return array();
}

function formulaires_exemple_lo_traiter_dist () {

  $valeurs = array(
      'message_ok' => var_export(_request('liste_1'), TRUE) . '<br>' .
                      var_export(_request('liste_2'), TRUE),
  );

  if (saisies_liste_traiter(array('liste_1', 'liste_2')))
    return array('editable' => 'oui');

  return $valeurs;
}