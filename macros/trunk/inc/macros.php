<?php

/* retourne le résultat de l'évaluation du fichier "$nom.php" */
function evaluer_macro ($nom, $contexte) {

  /* Crée les variables du contexte */
  foreach ($contexte as $cle => $valeur) {
    ${$cle} = $valeur;
  }

  /* récupère le résultat de l'évaluation de la macro dans la variable
     $skel */
  $output = fopen('php://output', 'w');
  ob_start();
  include find_in_path($nom . '.php');
  fclose($output);
  $skel = ob_get_clean();

  return $skel;
}

/**
 * retourne un nom de squelette correspondant à la macro évaluée avec le
 * contexte donné.
 *
 * @param string $nom_macro  Le nom de la macro a évaluer
 * @param array $contexte    Un tableau encodant le contexte, de la forme
 *                           array('nom_variable' => $valeur_variable)
 * @return string  un nom de squelette utilisable dans recuperer_fond()
 */
function recuperer_macro ($nom_macro, $contexte) {

  include_spip('inc/flock');

  $dir = _DIR_CACHE . 'macros';

  if ( ! is_dir($dir)) {
    sous_repertoire($dir);
  }

  $hash_contexte = md5(serialize($contexte));
  $nom_skel = $dir . '/' . $nom_macro . '_' . $hash_contexte;
  $path_fichier = $nom_skel . '.html';

  $skel = evaluer_macro($nom_macro, $contexte);

  $utiliser_cache = (! _NO_CACHE) && (!_NO_MACRO_CACHE) && is_readable($path_fichier);

  if (( ! $utiliser_cache)
   && ( ! ecrire_fichier($path_fichier, $skel))) {
    return;
  }

  return $nom_skel;
}