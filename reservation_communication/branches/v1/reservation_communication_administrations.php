<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Réservation Comunications
 *
 * @plugin     Réservation Comunications
 * @copyright  2015
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_communication\Installation
 */

if (!defined('_ECRIRE_INC_VERSION'))
  return;

/**
 * Fonction d'installation et de mise à jour du plugin Réservation Comunications.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 **/
function reservation_communication_upgrade($nom_meta_base_version, $version_cible) {
  $maj = array();

  $maj['create'] = array( array(
      'maj_tables',
      array(
        'spip_reservation_communications',
        'spip_reservation_communication_destinataires'
      )
    ));

  include_spip('base/upgrade');
  maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Réservation Comunications.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 **/
function reservation_communication_vider_tables($nom_meta_base_version) {

  sql_drop_table("spip_reservation_communications");
  sql_drop_table("spip_reservation_communication_destinataires");
  # Nettoyer les versionnages et forums
  sql_delete("spip_versions", sql_in("objet", array(
    'spip_reservation_communications',
    'spip_reservation_communication_destinataires'
  )));
  sql_delete("spip_versions_fragments", sql_in("objet", array(
    'spip_reservation_communications',
    'spip_reservation_communication_destinataires'
  )));
  sql_delete("spip_forum", sql_in("objet", array(
    'spip_reservation_communications',
    'spip_reservation_communication_destinataires'
  )));

  effacer_meta($nom_meta_base_version);
}
