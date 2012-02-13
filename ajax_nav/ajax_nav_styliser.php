<?php

function ajax_nav_styliser($flux) {

  // si l'url contient un parametre 'getbyid' non-vide, on redirige vers le squelette getbyid
  // qui affiche le bloc demande
  $id = $flux['args']['contexte']['getbyid'];
  if ($id != '') {
    $flux['data'] = preg_replace('/.html$/', '', find_in_path('getbyid.html'));
  }

  // si l'url contient un parametre 'getinfos' non-vide, on redirige vers le squelette getinfos
  // qui affiche les infos necessaires a ajax_nav.js au format JSON.
  $getInfos = $flux['args']['contexte']['getinfos'];
  if ($getInfos != '') {
    $flux['data'] = preg_replace('/.html$/', '', find_in_path('getinfos.html'));
  }

  return $flux;
}

?>