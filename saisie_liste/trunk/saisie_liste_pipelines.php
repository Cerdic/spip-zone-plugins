<?php

function saisie_liste_jqueryui_plugins ($scripts) {

  if ( ! in_array('jquery.ui.sortable', $scripts)) {
    $scripts[] = 'jquery.ui.sortable';
  }
  return $scripts;
}

function saisie_liste_insert_head ($flux) {

  $flux .= '<link rel="stylesheet" href="' . find_in_path('saisie_liste.css') . '" />';
  $flux .= '<script src="' . find_in_path('javascript/jquery.saisie_liste.js') . '" type="text/javascript"></script>';  

  return $flux;
}

function saisie_liste_header_prive ($flux) {

  return saisie_liste_insert_head($flux);
}