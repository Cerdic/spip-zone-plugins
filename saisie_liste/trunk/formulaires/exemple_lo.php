<?php

function formulaires_exemple_lo_charger_dist () {

  return array(
           'liste_1' => array(
                          0 => array(
                                 'titre_objet' => 'Un bel objet',
                                 'description_objet' => 'bla bla bla bla',
                               ),
                        ),
         );
}

function formulaires_exemple_lo_verifier_dist () {

  if (liste_verifier('liste_1')) return;
  if (liste_verifier('liste_2')) return;

}

function formulaires_exemple_lo_traiter_dist () {

  if (liste_traiter('liste_1')) return;
  if (liste_traiter('liste_2')) return;

  return array(
           'message_ok' => var_export(_request('liste_1'), TRUE) . '<br>' .
                           var_export(_request('liste_2'), TRUE),
         );
}