<?php
function formulaires_configurer_foundation_charger() {

  // Lire le fichier YAML qui contient la structure du formulaire.
  include_spip('inc/yaml');
  $formulaire = yaml_decode_file(find_in_path('formulaires/configurer_foundation.yaml'));

  // Lire la configuration de foundation
  $config = lire_config('foundation');

  // Ajouter le formulaire au contexte pour utiliser #GENERER_SAISIES
  // On utilise un _ devant le nom ENV pour évité les traitements automatique.
  $config['_form_saisies'] = $formulaire;

  return $config;
}