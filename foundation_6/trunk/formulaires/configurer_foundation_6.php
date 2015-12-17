<?php
function formulaires_configurer_foundation_saisies() {
  // Lire le fichier YAML qui contient la structure du formulaire.
  include_spip('inc/yaml');
  $saisies = array(
    array(
      'saisie' => 'oui_non',
      'options' => array(
        'nom' => 'javascript',
        'label' => _T('foundation_6:activer_javascript'),
        'explication' => _T('foundation_6:activer_javascript_explication')
      )
    )
  );
  return $saisies;
}

function formulaires_configurer_foundation_charger() {
  // Lire la configuration de foundation
  $config = lire_config('foundation');

  return $config;
}
