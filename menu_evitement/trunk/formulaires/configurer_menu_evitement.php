<?php

include_spip('inc/config');
include_spip('inc/texte');

function nettoyer_structure ($structure) {
  $resultat = array();
  foreach ($structure as $item) {
    $pas_vide = False;
    foreach ($item as $cle => $val) {
      if ($val !== '') {
        $pas_vide = True;
        if (($cle == 'titre') || ($cle == 'texte_ancre')) {
          $item[$cle] = typo($val);
        } else if (($cle == 'cible') || ($cle == 'class')) {
          $item[$cle] = preg_replace('/[#.]/', '', $val);
        }
      }
    }
    if ($pas_vide) {
      $resultat[] = $item;
    }
  }
  return $resultat;
}

function permuter_structure ($structure, $permutation) {

  if ($permutation == '') {
    $permutation = implode(',', range(0, count($structure)));
  }

  $permutation = explode(',', $permutation);
  $resultat = array();
  foreach ($permutation as $val) {
    $resultat[] = $structure[$val];
  }
  return $resultat;
}

function formulaires_configurer_menu_evitement_charger () {

  return lire_config('menu_evitement');
}

function formulaires_configurer_menu_evitement_traiter () {

  $post = array(
            'lien_vers_menu_admin'          => _request('lien_vers_menu_admin'),
            'cacher_menu_quand_pas_focus'   => _request('cacher_menu_quand_pas_focus'),
            'cacher_ancres_quand_pas_focus' => _request('cacher_ancres_quand_pas_focus'),
            'structure'                     => nettoyer_structure(
                                                 permuter_structure(
                                                   _request('structure'),
                                                   _request('permutation-structures'))
                                               ),
          );

  ecrire_config('menu_evitement', $post);

  return array(
               'editable' => true,
               'message_ok' => 'Donnees Enregistrees',
  );
}

?>