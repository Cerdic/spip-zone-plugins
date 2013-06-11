<?php

function ajouter_script($flux) {

  $flux .= '<script type="text/javascript" src="' . produire_fond_statique('js_log.js') . '"></script>';
  return $flux;
}

function js_log_insert_head($flux) {

  return ajouter_script($flux);
}

function js_log_header_prive($flux) {

  return ajouter_script($flux);
}

?>