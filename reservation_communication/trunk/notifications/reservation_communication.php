<?php
/**
 * Envoi des communications
 *
 * @plugin     Réservation Comunications
 * @copyright  2015-2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_communication\Notifications
 */

if (!defined("_ECRIRE_INC_VERSION"))
  return;

/**
 * Les communications envoyés via le plugin
 *
 * @param string $quoi
 * @param int $id_reservation_communication
 * @param array $options
 */
function notifications_reservation_communication_dist($quoi, $id_reservation_communication, $options) {
  $config = $options['config'];
  $envoyer_mail = charger_fonction('envoyer_mail', 'inc');

  $subject = sql_getfetsel('titre', 'spip_reservation_communications', 'id_reservation_communication = ' . $id_reservation_communication);
  $message = recuperer_fond('notifications/contenu_reservation_communication', array('id_reservation_communication' => $id_reservation_communication));

  // attacher les documents de la communication
  $sql = sql_select('*', 'spip_documents AS d LEFT JOIN spip_documents_liens AS dl USING (id_document)
        LEFT JOIN spip_types_documents USING(extension)', 'dl.id_objet = ' . $id_reservation_communication . '
        AND dl.objet="reservation_communication" AND dl.vu = "non"');
  $id_document = array();

  $o = array('html' => $message);

  while ($doc = sql_fetch($sql)) {
    $fichier = $doc['fichier'];
    $id_document[] = $doc['id_document'];
    list($extension, $nom) = explode('/', $fichier);
    $chemin = realpath(_DIR_IMG . $fichier);
    $o['pieces_jointes'][] = array(
      'chemin' => $chemin,
      'nom' => $nom,
      'encodage' => 'base64',
      'mime' => $doc['mime_type']
    );
  }

  if (isset($options['recipients'])) {
    $recipients = $options['recipients'];
    $envoyer_mail($recipients, $subject, $o);
  }
  else {
    $recipients = array();
    $sql = sql_select('email', 'spip_reservation_communication_destinataires', 'id_reservation_communication = ' . $id_reservation_communication);
    $recipients = array();
    // Envoyer les emails
    while ($data = sql_fetch($sql)) {
      $envoyer_mail($data['email'], $subject, $o);
      $recipients[] = $data['email'];
    }
  }

  // Si présent -  l'api de notifications_archive
  if ($archiver = charger_fonction('archiver_notification', 'inc', true)) {
    $envoi = 'reussi';
    if (!$envoyer_mail)
      $envoi = 'echec';

    $o = array(
      'recipients' => $recipients,
      'sujet' => $subject,
      'texte' => $message,
      'html' => 'oui',
      'id_objet' => $id_reservation_communication,
      'objet' => 'reservation_communication',
      'envoi' => $envoi,
      'type' => $quoi
    );

    if (is_array($recipients)) {
      foreach ($recipients as $recipient) {
        $o['recipients'] = $recipient;
        $archiver($o);
      }
    }
    else
      $archiver($o);
  }
}
