<?php

function saisie_liste_objets_jqueryui_plugins ($scripts) {

  if ( ! in_array('jquery.ui.sortable', $scripts)) {
    $scripts[] = 'jquery.ui.sortable';
  }
  return $scripts;
}

function saisie_liste_objets_insert_head ($flux) {

  $flux .= '<link rel="stylesheet" href="' . find_in_path('saisie_liste_objets.css') . '" />';
  $flux .= '<script src="' . find_in_path('js/jquery.saisie_liste_objets.js') . '" type="text/javascript"></script>';  

  return $flux;
}

function saisie_liste_objets_header_prive ($flux) {

  return saisie_liste_objets_insert_head($flux);
}