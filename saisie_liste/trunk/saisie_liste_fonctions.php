<?php

/***********/
/* Filtres */
/***********/

function enumerer ($max) {

  $resultat = array();
  for ($i=0; $i<=$max; $i++) {
    $resultat[] = $i;
  }
  return $resultat;
}

function joindre ($tableau, $liant) {

  return implode($liant, $tableau);
}

/**
 * preparer_tableau_saisies - convertit un tableau définissant des saisies
 *
 * Convertit un tableau définissant une saisie au format :
 *
 *   array(
 *         'saisie'             => 'type_saisie',
 *         'nom'                => 'nom_saisie',
 *         'un_autre_paramètre' => 'blabla',
 *         'saisies'            => array( … ),
 *        )
 *
 * vers le format exigé par #GENERER_SAISIES.
 *
 * @param array $tableau_saisie
 *     Un tableau au format ci-dessus
 * @return array
 *     Un tableau équivalent au format de #GENERER_SAISIES
 */
function preparer_tableau_saisie ($tableau_saisie) {

  if (array_key_exists('saisie', $tableau_saisie)) {
    $resultat = array(
        'saisie'  => $tableau_saisie['saisie'],
    );
    if (isset($tableau_saisie['saisies'])) {
      $resultat['saisies'] = $tableau_saisie['saisies'];
    }

    unset($tableau_saisie['saisie']);
    unset($tableau_saisie['saisies']);
    $resultat['options'] = $tableau_saisie;
    return $resultat;
  }
  else {
    return 'ERREUR SAISIE LISTE : mauvais paramètres.';
  }
}

/**
 * charger_valeurs - charge des valeurs par défaut dans un tableau de saisies
 *
 * @param array $tableau_saisie
 *     Un tableau de saisies au format de #GENERER_SAISIES représentant
 *     un objet de la saisie liste.
 * @param array $valeurs
 *     Les valeurs par défaut, pour la saisie liste en entier.
 * @param array $index_objet
 *     L'index de l'objet dont on veut charger les valeurs.
 * @return array
 *     Un tableau de saisies au format de #GENERER_SAISIES représentant
 *     un objet de la saisie liste, dans lequel l'objet $index_objet
 *     a comme valeurs par défaut les valeurs de la $index_objet-ième
 *     ligne du tableau $valeurs.
 */
function charger_valeurs ($tableau_saisie, $valeurs, $index_objet) {

  $tableau_saisie['options']['defaut'] = $valeurs[ $index_objet ][ $tableau_saisie['options']['nom'] ];

  return $tableau_saisie;
}

/**
 * renommer_saisies - renomme les saisies d'un objet d'une saisie liste_objet pour en faire des sous-saisies.
 *
 * Parcours les noms de l'objet, et change "nom" en
 * "nom-saisie-liste-objet[$index_objet][nom]"
 *
 * @param array $tableau_saisie
 *     Un tableau de saisies au format de #GENERER_SAISIES représentant
 *     un objet de la saisie liste.
 * @param array $index_objet
 *     L'index de l'objet en cours de traitement
 * @param array $nom_saisie_liste
 *     Le nom de la saisie liste
 * @return array
 *     Le tableau $tableau_saisie dans lequels on a renommé les saisies.
 */
function renommer_saisies ($tableau_saisie, $index_objet, $nom_objet) {

  $tableau_saisie['options']['nom'] = $nom_objet . "[" . $index_objet . "][" . $tableau_saisie['options']['nom'] . "]";

  return $tableau_saisie;
}

/****************************/
/* Traitements du formulaire */
/****************************/

/**
 * filtrer_valeurs - filtre un tableau de valeurs pour retirer les infos
 *     qui n'importent que pour le fonctionnement interne de la saisie
 *     liste. Retire aussi les valeurs vides.
 *
 * @param array $valeurs
 *     Les valeurs retournées par _request('nom-saisie-liste')
 * @return array
 *     Les valeurs prêtes à être utilisées dans les fonctions verifier et traiter.
 */
function filtrer_valeurs ($valeurs) {

  $valeurs_filtrees = array();

  unset($valeurs['action']);
  unset($valeurs['permutations']);

  foreach ($valeurs as $objet) {
    $objet_est_vide = TRUE;
    if (is_array($objet)) {
      foreach ($objet as $valeur) {
        if ($valeur !== '') {
          $objet_est_vide = FALSE;
        }
      }
    }
    if ( ! $objet_est_vide) {
      $valeurs_filtrees[] = $objet;
    }
  }

  return $valeurs_filtrees;
}

/**
 * permuter - Permute les index d'un tableau selon un permutation donnée.
 *
 * @param array $tableau
 *     un tableau indexé par des nombres entiers.
 * @param array permutations
 *     un tableau de même taille représentant une permutation.
 *     P.ex ce tableau de permutation :
 *         array(
 *               0 => 2,
 *               1 => 1,
 *               2 => 0,
 *              )
 *     permet d'échanger les valeurs de la première et la dernière ligne
 *     d'un tableau a 3 éléments.
 * @return array
 *     Le tableau après permutation.
 */
function permuter ($tableau, $permutations) {

  $resultat = array();
  for ($i=0; $i<count($permutations); $i++) {
    $resultat[$i] = $tableau[$permutations[$i]];
  }
  return $resultat;
}

/**
 * executer_actions_liste_objet - execute les actions demandées par la
 *     valeur associée à la clé 'action' d'un tableau de valeurs retourné
 *     par une saisie liste
 *
 * @param array $valeurs
 *     un tableau de valeurs retourné par une saisie liste
 * @return array
 *     Le tableau après execution des actions.
 */
function executer_actions_liste_objet ($valeurs) {

  $permutations = explode(',', $valeurs['permutations']);

  if (array_key_exists('action', $valeurs)) {
    foreach ($valeurs['action'] as $details_action => $valeur_submit) {
      $details_action = explode('-', $details_action);
      $action      = $details_action[0];
      $index_objet = $details_action[1];
      switch ($action) {
      case 'supprimer':
        unset($valeurs[intval($index_objet)]);
        break;
      case 'ajouter':
        // on n'as rien à faire pour ajouter un objet, il suffit de
        // recharger le formulaire
        break;
      case 'monter':
        // il faut opérer sur la liste des permutations, parce ce qu'elle
        // correspond à l'ordre des objets affichés quand l'utilisateur
        // a submit.
        $index_objet     = array_search($index_objet, $permutations);
        $objet_au_dessus = $permutations[$index_objet-1];
        $permutations[$index_objet-1] = $permutations[$index_objet];
        $permutations[$index_objet]   = $objet_au_dessus;
        break;
      case 'descendre':
        $index_objet      = array_search($index_objet, $permutations);
        $objet_en_dessous = $permutations[$index_objet+1];
        $permutations[$index_objet+1] = $permutations[$index_objet];
        $permutations[$index_objet]   = $objet_en_dessous;
        break;
      }
    }
  }
  return filtrer_valeurs(permuter($valeurs, $permutations));
}

/**
 * traitements_liste - execute les traitements nécessaire pour
 *     le bon fonctionnement d'une saisie liste_objet.
 *
 * @param string $nom_saisie
 *     le nom d'une saisie liste
 * @param string $appelant
 *     le contexte dans lequel la fonction est appelée. Deux valeurs
 *     sont possibles : 'verifier' ou 'traiter'
 * @return bool
 *     TRUE si l'on souhaite interrompre les traitements définis par les
 *     fonctions verifier et traiter du formulaire. FALSE, sinon.
 */
function traitements_liste ($nom_saisie, $appelant) {

  static $interrompre_traitements_formulaire = array();

  /* cette fonction est appellée dans vérifier, puis dans traiter.
     La première fois on calcule la valeur de $interrompre_traitements_formulaire,
     et la deuxième fois on ne fais que la retourner. */
  if ($appelant === 'verifier') {
    $interrompre_traitements_formulaire[$nom_saisie] = FALSE;
  } else if ($appelant === 'traiter') {
      return $interrompre_traitements_formulaire[$nom_saisie];
  }

  $valeurs = _request($nom_saisie) ? _request($nom_saisie) : array();

  if (array_key_exists('action', $valeurs)) {
    $interrompre_traitements_formulaire[$nom_saisie] = TRUE;
  }

  $valeurs = executer_actions_liste_objet ($valeurs);
  set_request($nom_saisie, $valeurs);

  return $interrompre_traitements_formulaire[$nom_saisie];
}

function traitements_listes($saisies, $appelant) {

  if ( ! is_array($saisies)) {
    $saisies = array($saisies);
  }

  $resultat = FALSE;

  foreach ($saisies as $nom_saisie) {
    if (traitements_liste($nom_saisie, $appelant)) {
      $resultat = $nom_saisie;
    }
  }
  return $resultat;
}

/**
 * verifier et préparer les valeurs de saisies liste
 *
 * @param mixed $saisies  Le nom d'une saisie liste ou une liste de nom de
 *                        saisies liste.
 * @return mixed   Retourne FALSE si le submit cliqué n'est pas un submit
 *                 de saisie liste. On peut alors continuer les
 *                 vérifications.
 *                 Si le submit est un submit d'une saisie liste, on
 *                 retourne le nom de la saisie en question.
 */
function saisies_liste_verifier ($saisies) {

  return traitements_listes($saisies, 'verifier');
}

/**
 * traiter les valeurs de saisies liste
 *
 * @param mixed $saisies  Le nom d'une saisie liste ou une liste de nom de
 *                        saisies liste.
 * @return mixed   Retourne FALSE si le submit cliqué n'est pas un submit
 *                 de saisie liste. On peut alors continuer les
 *                 vérifications.
 *                 Si le submit est un submit d'une saisie liste, on
 *                 retourne le nom de la saisie en question.
 */
function saisies_liste_traiter ($saisies) {

  return traitements_listes($saisies, 'traiter');
}