<?php

function formulaires_tester_saisie_liste_objets_ajax_charger_dist () {

  return array(
         'liste_1' => array(
                    0 => array(
                           'titre_objet' => 'Un bel objet',
                           'description_objet' => 'bla bla bla bla',
                         ),
                  ),
         );
}

function formulaires_tester_saisie_liste_objets_ajax_verifier_dist () {

  if ($err = traitements_liste_objets('liste_1')) return $err;

}

function formulaires_tester_saisie_liste_objets_ajax_traiter_dist () {

  /* var_dump(_request('liste_1')); */
}
