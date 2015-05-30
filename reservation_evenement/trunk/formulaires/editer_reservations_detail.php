<?php
/**
 * Gestion du formulaire de d'édition de reservations_detail
 *
 * @plugin     Reservations_detail
 * @copyright  2013-2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Res\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION'))
  return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_reservations_detail
 *     Identifiant du reservations_detail. 'new' pour un nouveau reservations_detail.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_reservations_detail_identifier_dist($id_reservations_detail = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  return serialize(array(intval($id_reservations_detail)));
}

/**
 * Chargement du formulaire d'édition de reservations_detail
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_reservations_detail
 *     Identifiant du reservations_detail. 'new' pour un nouveau reservations_detail.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_reservations_detail_charger_dist($id_reservations_detail = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  $date = date('Y-m-d G:i:s');
  $valeurs = formulaires_editer_objet_charger('reservations_detail', $id_reservations_detail, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
  
  if (isset($valeurs['id_evenement']) AND $valeurs['id_evenement'] > 0)
    $valeurs['id_article'] = sql_getfetsel('id_article', 'spip_evenements', 'id_evenement=' . $valeurs['id_evenement']);
  
  $valeurs['id_reservation'] = _request('id_reservation') ? _request('id_reservation') : $valeurs['id_reservation'];
  
  $sql = sql_select('id_article', 'spip_evenements', 'date_fin > ' . sql_quote($date));
  
  $valeurs['articles'] = array();
  $valeurs['evenement_anterieurs'] = _request('evenement_anterieurs');
  
  while ($data = sql_fetch($sql))
    $valeurs['articles'][] = $data['id_article'];
  return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de reservations_detail
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_reservations_detail
 *     Identifiant du reservations_detail. 'new' pour un nouveau reservations_detail.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_reservations_detail_verifier_dist($id_reservations_detail = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  $obligatoire = array(
    'id_evenement',
    'id_reservation'
  );
  if (test_plugin_actif('prix_objets'))
    $obligatoire = array_merge($obligatoire, array('id_prix_objet'));

  return formulaires_editer_objet_verifier('reservations_detail', $id_reservations_detail, $obligatoire);
}

/**
 * Traitement du formulaire d'édition de reservations_detail
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_reservations_detail
 *     Identifiant du reservations_detail. 'new' pour un nouveau reservations_detail.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservations_detail source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservations_detail, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_reservations_detail_traiter_dist($id_reservations_detail = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
  return formulaires_editer_objet_traiter('reservations_detail', $id_reservations_detail, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
}
?>