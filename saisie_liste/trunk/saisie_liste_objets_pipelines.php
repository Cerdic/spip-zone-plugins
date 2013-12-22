<?php

function saisie_liste_objets_jqueryui_plugins ($scripts) {

  if ( ! in_array('jquery.ui.sortable', $scripts)) {
    $scripts[] = 'jquery.ui.sortable';
  }
  return $scripts;
}