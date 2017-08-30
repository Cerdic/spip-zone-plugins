<?php
/**
 * Gestion du formulaire de d'édition de reservation_communication
 *
 * @plugin     Réservation Comunications
 * @copyright  2015
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_communication\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION'))
  return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_reservation_communication
 *     Identifiant du reservation_communication. 'new' pour un nouveau reservation_communication.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_communication source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_communication, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_reservation_communication_identifier_dist($id_reservation_communication = 'new', $id_rubrique = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  return serialize(array(intval($id_reservation_communication)));
}

/**
 * Chargement du formulaire d'édition de reservation_communication
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_reservation_communication
 *     Identifiant du reservation_communication. 'new' pour un nouveau reservation_communication.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_communication source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_communication, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_reservation_communication_charger_dist($id_reservation_communication = 'new', $id_rubrique = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  $valeurs = formulaires_editer_objet_charger('reservation_communication', $id_reservation_communication, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);

  if ($id= _request('id_rubrique')) {
    $valeurs['id_rubrique'] = $id;
    $objet = 'rubrique';
  }
  elseif ($id = _request('id_article')) {
    $valeurs['id_article'] = $id ;
    $objet = 'article';
  }
  elseif ($id = _request('id_evenement')) {
    $valeurs['id_evenement'] = $id;
    $objet = 'evenement';
  }

  if ($statut_reservation = _request('statut') or $statut_reservation = _request('statut2')) {
    $valeurs['statut_reservation'] = str_replace('-',',',$statut_reservation);
  }

  if ($id) {
    $data = sql_fetsel('*','spip_' . $objet .'s','id_' .$objet . '=' . $id);

    $valeurs['titre'] = supprimer_numero($data['titre']);
    $valeurs['lang'] = isset($data['lang']) ? $data['lang'] : $GLOBALS['meta']['langue_site'];
    $valeurs['id'] = $id;
    $valeurs['_hidden'] .= '<input type="hidden" name="id" value="' .  $valeurs['id'] . '" />';
  }

  if(_request('type')) $valeurs['type'] = _request('type');

  if ($objet) {
    $valeurs['_hidden'] .= '<input type="hidden" name="objet" value="' . $objet . '" />';
    $valeurs['_hidden'] .= '<input type="hidden" name="id_parent" value="' . $valeurs['id_rubrique'] . '" />';
    $valeurs['_hidden'] .= '<input type="hidden" name="id_article" value="' . $valeurs['id_article'] . '" />';
    $valeurs['_hidden'] .= '<input type="hidden" name="id_evenement" value="' . $valeurs['id_evenement'] . '" />';
  }

  $valeurs['_hidden'] .= '<input type="hidden" name="statut_reservation" value="' . $valeurs['statut_reservation'] . '" />';
  $valeurs['_hidden'] .= '<input type="hidden" name="type" value="' . $valeurs['type'] . '" />';
  $valeurs['_hidden'] .= '<input type="hidden" name="lang" value="' . $valeurs['lang'] . '" />';



  return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de reservation_communication
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_reservation_communication
 *     Identifiant du reservation_communication. 'new' pour un nouveau reservation_communication.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_communication source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_communication, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_reservation_communication_verifier_dist($id_reservation_communication = 'new', $id_rubrique = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {

  return formulaires_editer_objet_verifier('reservation_communication', $id_reservation_communication, array('titre'));

}

/**
 * Traitement du formulaire d'édition de reservation_communication
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_reservation_communication
 *     Identifiant du reservation_communication. 'new' pour un nouveau reservation_communication.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_communication source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_communication, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_reservation_communication_traiter_dist($id_reservation_communication = 'new', $id_rubrique = 0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  return formulaires_editer_objet_traiter('reservation_communication', $id_reservation_communication, $id_rubrique, $lier_trad, $retour, $config_fonc, $row, $hidden);
}
