<?php

function formulaires_demo_liste_1_charger_dist () {

  return array(
           'liste_1' => array(
                          0 => array(
                                 'titre_element' => 'Des frites',
                               ),
                          1 => array(
                                 'titre_element' => 'De la mayo',
                               ),
                        ),
         );
}

function formulaires_demo_liste_1_verifier_dist () {

  if (saisies_liste_verifier('liste_1'))
    return array();

  return array();
}

function formulaires_demo_liste_1_traiter_dist () {

  $valeurs = array(
      'message_ok' => implode(', ',
                        array_map(function ($el) {
                                    return $el['titre_element'];
                                  },
                                  _request('liste_1'))),
  );

  if (saisies_liste_traiter('liste_1'))
    return array('editable' => 'oui');

  return $valeurs;
}