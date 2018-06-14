<?php
/**
 * Actions d'envoi du plugin Réservation Comunications
 *
 * @plugin     Réservation Comunications
 * @copyright  2015-2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_communication\Action
 */

function action_reservation_communication_envoyer_dist() {
  include_spip('inc/config');
  $config = lire_config('reservation_evenement');

  $arg = _request('arg');

  if (is_null($arg)){
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }

  list($id_reservation_communication, $type, $lang, $recipients) = explode('-', $arg);


  // Notifications
  if ($notifications = charger_fonction('notifications', 'inc', true)) {
    lang_select($lang);

    $options = array(
      'lang' => $lang,
      'type' => $type,
      'recipients' => $recipients,
      'config'  => $config
    );

    // Determiner l'expediteur
    if ($config['expediteur'] != "facteur")
      $options['expediteur'] = $config['expediteur_' . $config['expediteur']];

    // Envoyer
    $notifications('reservation_communication',$id_reservation_communication, $options);

    // Changer de statut
    if(!$recipients){
      include_spip('action/editer_objet');
      objet_modifier('reservation_communication', $id_reservation_communication, array('statut' => 'envoye', 'date_envoi' => date('Y-m-d H:i:s')));
    }
  }
}
