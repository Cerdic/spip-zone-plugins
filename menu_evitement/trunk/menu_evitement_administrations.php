<?php

include_spip ('inc/config');

function menu_evitement_upgrade ($nom_meta_base_version, $version_cible) {

  $defaut = array(
              'lien_vers_menu_admin'          => 'on',
              'cacher_menu_quand_pas_focus'   => 'on',
              'cacher_ancres_quand_pas_focus' => 'on',
              'structure'                     => array(
                                                   0 => array(
                                                          'cible'       => 'contenu',
                                                          'titre'       => 'Aller au contenu',
                                                          'texte_ancre' => 'retour au menu',
                                                        ),
                                                   1 => array(
                                                          'cible'       => 'extra',
                                                          'titre'       => 'Aller au menu droite',
                                                          'texte_ancre' => 'retour au menu',
                                                        ),
                                                 ),
            );

  // premiere install du plugin
  if ( ! lire_config($nom_meta_base_version)) {
    ecrire_config('menu_evitement', $defaut);
    ecrire_config($nom_meta_base_version, $version_cible);
  }
}

function menu_evitement_vider_tables ($nom_meta_base_version) {

  effacer_config('menu_evitement');
  effacer_config($nom_meta_base_version);
}

?>