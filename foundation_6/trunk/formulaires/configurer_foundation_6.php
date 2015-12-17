<?php
function formulaires_configurer_foundation_saisies() {
  // Lire le fichier YAML qui contient la structure du formulaire.
  include_spip('inc/yaml');
  $formulaire = yaml_decode_file(find_in_path('formulaires/configurer_foundation.yaml'));

  return $formulaire;
}

function formulaires_configurer_foundation_charger() {
  // Lire la configuration de foundation
  $config = lire_config('foundation');

  return $config;
}