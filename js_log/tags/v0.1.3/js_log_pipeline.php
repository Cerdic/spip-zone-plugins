<?php

function ajouter_script($flux) {

  include_spip('public/spip_bonux_balises');

  $flux .= '<script type="text/javascript" src="' . produire_fond_statique('js_log.js') . '"></script>';
  return $flux;
}

function inclure_formulaire_erreurs_js ($flux) {

  // insère le formulaire erreur_js au début du body
  return preg_replace('/(<body[^>]*>)/',
                      '$1' . recuperer_fond('client_logger'),
                      $flux);
}

function js_log_insert_head($flux) {

  return ajouter_script($flux);
}

function js_log_header_prive($flux) {

  return ajouter_script($flux);
}

function js_log_affichage_final($page) {

  return inclure_formulaire_erreurs_js($page);
}

function js_log_body_prive ($body) {

  return inclure_formulaire_erreurs_js($body);
}

?>