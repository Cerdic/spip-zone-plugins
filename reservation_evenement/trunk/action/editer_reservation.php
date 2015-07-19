<?php
if (!defined('_ECRIRE_INC_VERSION'))
  return;

function reservation_inserer($id_parent = null, $set = null) {
  $table_sql = table_objet_sql('reservation');
  $champs = array();
  $champs['statut'] = 'encours';
  $champs['date'] = date('Y-m-d H:i:s');

  if ($set)
    $champs = array_merge($champs, $set);

  // Envoyer aux plugins
  $champs = pipeline('pre_insertion', array(
    'args' => array('table' => $table_sql, ),
    'data' => $champs
  ));

  $id = sql_insertq($table_sql, $champs);

  if ($id) {
    pipeline('post_insertion', array(
      'args' => array(
        'table' => $table_sql,
        'id_objet' => $id,
      ),
      'data' => $champs
    ));
  }
  return $id;
}

function reservation_instituer($id_reservation, $c, $calcul_rub = true) {
  $table_sql = table_objet_sql('reservation');
  $trouver_table = charger_fonction('trouver_table', 'base');

  include_spip('inc/autoriser');
  include_spip('inc/rubriques');
  include_spip('inc/modifier');
  include_spip('inc/config');
  $config = lire_config('reservation_evenement');

  $row = sql_fetsel('statut,date,id_auteur,email,lang', 'spip_reservations', 'id_reservation=' . intval($id_reservation));
  $statut_ancien = $statut = $row['statut'];
  $date_ancienne = $date = $row['date'];

  $d = isset($c['date']) ? $c['date'] : null;
  $s = isset($c['statut']) ? $c['statut'] : $statut;

  $champs = array();
  // cf autorisations dans inc/instituer_objet
  if ($s != $statut OR ($d AND $d != $date)) {
    if (autoriser('modifier', 'reservation', $id_reservation))
      $statut = $champs['statut'] = $s;
    else
      spip_log("editer_reservation $id_reservation refus " . join(' ', $c));

    // En cas de publication, fixer la date a "maintenant"
    // sauf si $c commande autre chose
    // ou si l'objet est deja date dans le futur
    // En cas de proposition d'un objet (mais pas depublication), idem
    if ($champ_date) {
      if ($champs['statut'] == 'publie' OR ($champs['statut'] == 'prop' AND !in_array($statut_ancien, array(
        'publie',
        'prop'
      ))) OR $d) {
        if ($d OR strtotime($d = $date) > time())
          $champs[$champ_date] = $date = $d;
        else
          $champs[$champ_date] = $date = date('Y-m-d H:i:s');
      }
    }
  }

  //les champs extras auteur
  include_spip('cextras_pipelines');
  $valeurs_extras = array();

  if (function_exists('champs_extras_objet')) {
    //Charger les définitions pour la création des formulaires
    $champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));

    if (is_array($champs_extras_auteurs)) {
      foreach ($champs_extras_auteurs as $value) {
        $valeurs_extras[$value['options']['nom']] = _request($value['options']['nom']);
      }
    }

    $champs['donnees_auteur'] = serialize($valeurs_extras);
  }

  // Envoyer aux plugins
  $champs = pipeline('pre_edition', array(
    'args' => array(
      'table' => 'spip_reservations',
      'id_reservation' => $id_reservation,
      'action' => 'instituer',
      'statut_ancien' => $statut_ancien,
      'date_ancienne' => $date_ancienne,
    ),
    'data' => $champs
  ));

  if (!count($champs))
    return '';

  // Envoyer les modifs.
  objet_editer_heritage('reservation', $id_reservation, '', $statut_ancien, $champs);

  // Invalider les caches
  include_spip('inc/invalideur');
  suivre_invalideur("id='reservation/$id_reservation'");

  // Pipeline
  pipeline('post_edition', array(
    'args' => array(
      'table' => 'spip_reservations',
      'id_reservation' => $id_reservation,
      'action' => 'instituer',
      'statut_ancien' => $statut_ancien,
      'date_ancienne' => $date_ancienne,
      'id_parent_ancien' => $id_rubrique,
    ),
    'data' => $champs
  ));

  // Les traitements spécifiques

  $set = array(
    'id_reservation' => $id_reservation,
    'statut' => $statut
  );

  // Gérer les détails des réservations
  $evenements = _request('id_evenement');

  // Si les déclinaisons sont actives on récupère les évenements via le prix
  if (test_plugin_actif('declinaisons')) {
    $evenements = array();
    if ($id_prix_objet = _request('id_objet_prix')) {
      foreach (array_keys($id_prix_objet ) AS $id_evenement) {
        $evenements[] = $id_evenement;
      }
    }
  }
  // Si on n'est pas dans le cas d'une création, on récupère les détails attachées à la réservation
  if (!is_array($evenements) OR (is_array($evenements) AND count($evenements) == 0)) {
    include_spip('action/editer_reservations_detail');
    $c = array('statut' => $statut);
    $sql = sql_select('id_reservations_detail', 'spip_reservations_details', 'id_reservation=' . $id_reservation);
    while ($data = sql_fetch($sql)) {
      reservations_detail_instituer($data['id_reservations_detail'], $c);
    }
  }
  else {
    $action = charger_fonction('editer_objet', 'action');
    $set['evenements'] = $evenements;
    set_request('evenements', $evenements);
  }
  //Si on est dans le cas d'une création
  if (is_array($evenements)) {
    // Pour chaque évènement on crée un détail de la réservation
    foreach ($evenements AS $id_evenement) {
      // Si aucun détail n'est attaché à l'évènement, on le crée
      if (!$reservations_detail = sql_fetsel('*', 'spip_reservations_details', 'id_reservation=' . $id_reservation . ' AND id_evenement=' . $id_evenement)) {
        $id_reservations_detail = 'new';
        $set['id_prix_objet'] = $id_prix_objet[$id_evenement];
      }
      else {
        $id_reservations_detail = $reservations_detail['id_reservations_detail'];
        $set['quantite'] = $reservations_detail['quantite'];
      }

      // Pour l'enregistrement
      $set['id_evenement'] = $id_evenement;

      // Eviter l'envoi d'une notification pour chaque détail
      set_request('envoi_separe_actif', 'non');
      $detail = $action($id_reservations_detail, 'reservations_detail', $set);
    }
  }

  // Notifications

  if ((!$statut_ancien OR $statut != $statut_ancien) && (isset($config['activer'])) && (isset($config['quand']) && is_array($config['quand']) && in_array($statut, $config['quand'])) && ($notifications = charger_fonction('notifications', 'inc', true))) {
    //Déterminer la langue pour les notifications
    $lang = isset($row['lang']) ? $row['lang'] : lire_config('langue_site');
    lang_select($lang);
    // Determiner l'expediteur
    $options = array(
      'statut' => $statut,
      'lang' => $lang
    );
    if ($config['expediteur'] != "facteur")
      $options['expediteur'] = $config['expediteur_' . $config['expediteur']];

    // Envoyer au vendeur et au client
    $notifications('reservation_vendeur', $id_reservation, $options);
    if ($config['client']) {
      //$row['email']=trim($row['email']);
      if (intval($row['id_auteur']) AND $row['id_auteur'] > 0)
        $options['email'] = sql_getfetsel('email', 'spip_auteurs', 'id_auteur=' . $row['id_auteur']);
      else
        $options['email'] = $row['email'];

      $notifications('reservation_client', $id_reservation, $options);
    }

  }

  return '';
  // pas d'erreur
}
