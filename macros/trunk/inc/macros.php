<?php

/* retourne le résultat de l'évaluation du fichier "$nom.php" */
function evaluer_macro ($nom_macro, $contexte = array()) {

  /* Crée les variables du contexte */
  foreach ($contexte as $cle => $valeur) {
    /* On vérifie si $cle est un nom de variable valide en php.
       cf. http://www.php.net/manual/en/language.variables.basics.php */
    if ((preg_match('#[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*#', $cle) === 1)
        AND ($cle !== 'this')) {

      ${$cle} = $valeur;

    } else {
      include_spip('inc/utils');
      $erreur = _T('macros:erreur_nom_variable_invalide',
                   array('nom_variable' => htmlspecialchars($cle)));
      erreur_squelette($erreur);
    }
  }

  /* récupère le résultat de l'évaluation de la macro dans la variable
     $skel */
  $output = fopen('php://output', 'w');
  ob_start();
  include find_in_path($nom_macro . '.php');
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
function recuperer_macro ($nom_macro, $contexte = array()) {

  include_spip('inc/flock');

  $dir = _DIR_CACHE . 'macros';

  if ( ! is_dir($dir)) {
    sous_repertoire($dir);
  }

  $hash_contexte = md5(serialize($contexte));
  $nom_skel = $dir . '/' . str_replace('/', '_', $nom_macro) . '_' . $hash_contexte;
  $path_fichier = $nom_skel . '.html';

  $skel = evaluer_macro($nom_macro, $contexte);

  $utiliser_cache = (!_NO_CACHE) && (!_NO_MACRO_CACHE) && is_readable($path_fichier);

  if (( ! $utiliser_cache)
   && ( ! ecrire_fichier($path_fichier, $skel))) {
    return;
  }

  return $nom_skel;
}

function inclure_macro ($nom_macro, $contexte) {

  include_spip('inc/flock');

  return spip_file_get_contents(recuperer_macro($nom_macro, $contexte) . '.html');
}