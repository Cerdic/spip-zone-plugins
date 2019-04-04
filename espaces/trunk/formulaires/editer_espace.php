<?php
/**
 * Gestion du formulaire de d'édition de espace
 *
 * @plugin     Espaces
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Espaces\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
  return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_espace
 *     Identifiant du espace. 'new' pour un nouveau espace.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le espace créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un espace source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du espace, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_espace_identifier_dist($id_espace = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  return serialize(array(intval($id_espace), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de espace
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_espace
 *     Identifiant du espace. 'new' pour un nouveau espace.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le espace créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un espace source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du espace, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_espace_charger_dist($id_espace = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  $valeurs = formulaires_editer_objet_charger('espace', $id_espace, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

  // Eviter d'associer un auteur.
  $valeurs['_hidden'] .= '<input type="hidden" name="id_auteur" value="" />';

  // Publier directement
  if ($id_espace == 'oui') {
    $valeurs['_hidden'] .= '<input type="hidden" name="statut" value="publie" />';
  }

  return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de espace
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_espace
 *     Identifiant du espace. 'new' pour un nouveau espace.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le espace créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un espace source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du espace, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_espace_verifier_dist($id_espace = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  $erreurs = array();

  $erreurs = formulaires_editer_objet_verifier('espace', $id_espace, array('titre'));

  return $erreurs;
}

/**
 * Traitement du formulaire d'édition de espace
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_espace
 *     Identifiant du espace. 'new' pour un nouveau espace.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le espace créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un espace source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du espace, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_espace_traiter_dist($id_espace = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  $retours = formulaires_editer_objet_traiter('espace', $id_espace, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

  // Un lien a prendre en compte ?
  if ($associer_objet and $id_espace = $retours['id_espace']) {
    list($objet, $id_objet) = explode('|', $associer_objet);

    if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
      include_spip('action/editer_liens');

      objet_associer(array('espace' => $id_espace), array($objet => $id_objet));

      if (isset($retours['redirect'])) {
        $retours['redirect'] = parametre_url($retours['redirect'], 'id_lien_ajoute', $id_espace, '&');
      }
    }
  }

  return $retours;
}
