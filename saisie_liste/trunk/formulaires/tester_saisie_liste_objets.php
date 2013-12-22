<?php

function formulaires_tester_saisie_liste_objets_charger_dist () {

  return array(
         'liste_1' => array(
                    0 => array(
                           'titre_objet' => 'Un bel objet',
                           'description_objet' => 'bla bla bla bla',
                         ),
                  ),
         );
}

function formulaires_tester_saisie_liste_objets_verifier_dist () {

  if ( ! traitements_liste_objets_ok('liste_1')) return;


  var_dump(_request('liste_1'));
}