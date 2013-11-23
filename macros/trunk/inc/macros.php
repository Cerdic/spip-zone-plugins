<?php

/**
 * retourne un nom de squelette correspondant à la macro évaluée avec le
 * contexte donné.
 *
 * Si la macro est appelée pour la première fois avec ce contexte, on
 * écrit le squelette résultant dans un fichier et on retourne le nom de
 * ce fichier. Sinon, on utilise le fichier créé lors du premier appel.
 *
 * @param string $nom_macro  Le nom de la macro. Chemin vers le fichier
 *                           php sans l'extention '.php'.
 * @param array $contexte    Un tableau de variables, au format
 *                           array($nom_variable => $valeur_variable)
 *
 * @return string            un nom de squelette utilisable dans
 *                           recuperer_fond()
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

  include_spip('inc/utils');
  if (test_espace_prive()) {
    $nom_skel = substr($nom_skel, 3);
  }

  return $nom_skel;
}

/**
 * retourne le résultat de l'évaluation d'une macro
 *
 * Idem que recuperer_macro, mais retourne le contenu du squelette au
 * lieu du nom de fichier.
 *
 * @param string $nom_macro  Le nom de la macro. Chemin vers le fichier
 *                           php sans l'extention '.php'.
 * @param array  $contexte   Un tableau de variables, au format
 *                           array($nom_variable => $valeur_variable)
 *
 * @return string            Le résultat de l'évaluation de la macro
 *
 */
function inclure_macro ($nom_macro, $contexte) {

  include_spip('inc/flock');

  return spip_file_get_contents(recuperer_macro($nom_macro, $contexte) . '.html');
}

/**
 * retourne le résultat de l'évaluation d'une macro. Fonction interne,
 * il vaut mieux utiliser inclure_macro
 *
 * Crée les variables définies dans le tableau $contexte, passe le
 * fichier marco dans php et retourne le résultat.
 *
 * @param string $nom_macro  Le nom de la macro. Chemin vers le fichier
 *                           php sans l'extention '.php'.
 * @param array  $contexte   Un tableau de variables, au format
 *                           array($nom_variable => $valeur_variable)
 *
 * @return string            Le résultat de l'évaluation de la macro
 *
 */
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