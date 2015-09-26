<?php
/**
 * Le Formulaire réservation
 *
 * @plugin     Réservation Événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Formulaires
 */

if (!defined("_ECRIRE_INC_VERSION"))
  return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_reservation_charger_dist($id=array(), $id_article = '', $retour = '') {
  include_spip('inc/config');
  include_spip('formulaires/selecteur/generique_fonctions');

  $config = lire_config('reservation_evenement',array());
  $enregistrement_inscrit = isset($config['enregistrement_inscrit']) ? $config['enregistrement_inscrit'] : 'on';
  $enregistrement_inscrit_obligatoire = isset($config['enregistrement_inscrit_obligatoire']) ? $config['enregistrement_inscrit_obligatoire'] : '';

  //Si l'affichage n'est pas déjà définie on établit si une zone s'applique
  if (!$id_article AND !$id) {
    include_spip('inc/reservation_evenements');

    $rubrique_reservation = isset($config['rubrique_reservation']) ? $config['rubrique_reservation'] : '';
    if (is_array($rubrique_reservation)) {
      $rubrique_reservation = picker_selected($rubrique_reservation, 'rubrique');
      $zone = rubrique_reservation('', 'evenement', $rubrique_reservation, array(
        'tableau' => 'oui',
        'where' => 'e.date_fin>NOW() AND e.inscription=1 AND e.statut="publie"',
        'select' => '*',
        'resultat' => 'par_id'
      ));
    }
  }

  //Si pas de zone, on établit les événements à afficher
  if (!is_array($zone)) {
    $where = array('date_fin>NOW() AND inscription=1 AND statut="publie"');
    //Si filtré par événement/s
    if ($id) {
      if (is_array($id))
        $id = implode(',', $id);
        $sql = sql_select('id_evenement_source,id_evenement','spip_evenements','id_evenement IN (' . $id . ')');
 
      $id = array();
      
      while ($row = sql_fetch($sql)) {
        if ($row['id_evenement_source'] == 0)
          $id[] = $row['id_evenement'];
        else
          $id[] = $row['id_evenement_source'];
      }
        $where[] = 'id_evenement IN (' . implode(',', $id). ')';
    }
    
    //Si filtré par article/s
    if ($id_article) {
      if (is_array($id_article))
        $id_article= implode(',', $id_article);

        $where[] = 'id_article IN (' . $id_article . ')';
    }

    $sql = sql_select('*', 'spip_evenements', $where, '', 'date_debut,date_fin');

    $evenements = array();
    $articles = array();
    while ($row = sql_fetch($sql)) {
      $evenements[$row['id_evenement']] = $row;
      $articles[] = $row['id_article'];
    }
  }
  //Sinon on affiche les événements de la zone établit
  else {
    $evenements = $zone;
  }

  // valeurs d'initialisation
  $valeurs = array(
    'evenements' => $evenements,
    'articles' => $evenements,
    'lang' => $GLOBALS['spip_lang'],
    'id_evenement' => $id
  );

  if (intval($GLOBALS['visiteur_session'])) {
    $session = $GLOBALS['visiteur_session'];
    $nom = $session['nom'];
    $email = $session['email'];
  }

  $valeurs['id_objet_prix'] = _request('id_objet_prix') ? (is_array(_request('id_objet_prix')) ? _request('id_objet_prix') : array(_request('id_objet_prix'))) : array();

  $valeurs['nom'] = $nom;
  $valeurs['email'] = $email;
  $valeurs['enregistrer'] = _request('enregistrer');
  $valeurs['new_pass'] = _request('new_pass');
  $valeurs['new_pass2'] = _request('new_pass2');
  $valeurs['new_login'] = _request('new_login');
  $valeurs['statut'] = 'encours';
  $valeurs['quantite'] = _request('quantite') ? _request('quantite') : 1;
  $valeurs['enregistrement_inscrit'] = $enregistrement_inscrit;
  $valeurs['enregistrement_inscrit_obligatoire'] = $enregistrement_inscrit_obligatoire;

  //les champs extras auteur
  include_spip('cextras_pipelines');

  if (function_exists('champs_extras_objet')) {
    //Charger les définitions pour la création des formulaires
    $valeurs['champs_extras_auteurs'] = champs_extras_objet(table_objet_sql('auteur'));
    foreach ($valeurs['champs_extras_auteurs'] as $key => $value) {
      $valeurs[$value['options']['nom']] = $session[$value['options']['nom']];
      $valeurs['champs_extras_auteurs'][$key]['options']['label'] = extraire_multi($value['options']['label']);

    }
  }

  $valeurs['_hidden'] .= '<input type="hidden" name="statut" value="' . $valeurs['statut'] . '"/>';
  $valeurs['_hidden'] .= '<input type="hidden" name="lang" value="' . $valeurs['lang'] . '"/>';
  if ($enregistrement_inscrit_obligatoire)
    $valeurs['_hidden'] .= '<input type="hidden" name="enregistrer[]" value="1"/>';
  return $valeurs;
}

function formulaires_reservation_verifier_dist($id = '', $id_article = '', $retour = '') {
  $erreurs = array();
  $email = _request('email');
  $id_auteur = '';

  if (isset($GLOBALS['visiteur_session']['id_auteur']) and $GLOBALS['visiteur_session']['id_auteur'] > 0) {
    $id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
  }

  // Si l'enregistrement est choisi
  if (_request('enregistrer')) {
    include_spip('inc/auth');
    $obligatoires = array(
      'nom',
      'email',
      'new_pass',
      'new_login'
    );
    foreach ($obligatoires AS $champ) {
      if (!_request($champ))
        $erreurs[$champ] = _T("info_obligatoire");
    }
    //Vérifier le login
    if ($err = auth_verifier_login($auth_methode, _request('new_login'), $id_auteur)) {
      $erreurs['new_login'] = $err;
      $erreurs['message_erreur'] .= $err;
    }

    //Vérifier les mp
    if ($p = _request('new_pass')) {
      if ($p != _request('new_pass2')) {
        $erreurs['new_pass'] = _T('info_passes_identiques');
        $erreurs['message_erreur'] .= _T('info_passes_identiques');
      }
      elseif ($err = auth_verifier_pass($auth_methode, _request('new_login'), $p, $id_auteur)) {
        $erreurs['new_pass'] = $err;
      }
    }
  }
  else {
    $obligatoires = array(
      'nom',
      'email'
    );

    if (test_plugin_actif('declinaisons'))
      array_push($obligatoires, 'id_objet_prix');
    else
      array_push($obligatoires, 'id_evenement');

    foreach ($obligatoires AS $champ) {
      if (!_request($champ))
        $erreurs[$champ] = _T("info_obligatoire");
    }
  }

  if ($email) {
    include_spip('inc/filtres');
    // un redacteur qui modifie son email n'a pas le droit de le vider si il y en avait un
    if (!email_valide($email)) {
      $erreurs['email'] = $id_auteur ? _T('form_email_non_valide') : _T('form_prop_indiquer_email');
    }
    elseif (!$id_auteur) {
      if ($email_utilise = sql_getfetsel('email', 'spip_auteurs', 'email=' . sql_quote($email)))
        $erreurs['email'] = _T('reservation:erreur_email_utilise');
    }
  }

  //les champs extras auteur
  include_spip('cextras_pipelines');

  if (function_exists('champs_extras_objet')) {
    include_spip('inc/saisies');
    //Charger les définitions
    $champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));
    $erreurs = array_merge($erreurs, saisies_verifier($champs_extras_auteurs));
  }
  if (count($erreurs) AND !isset($erreurs['message_erreur']))
    $erreurs['message_erreur'] = _T('reservation:message_erreur');
  return $erreurs;
}

function formulaires_reservation_traiter_dist($id = '', $id_article = '', $retour = '') {
  if ($retour) {
    refuser_traiter_formulaire_ajax();
  }
  $enregistrer = charger_fonction('reservation_enregistrer', 'inc');
  if (isset($GLOBALS['visiteur_session']['id_auteur']) and $GLOBALS['visiteur_session']['id_auteur'] > 0) {
    $id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
  }

  $retours = $enregistrer($id, $id_article, $id_auteur);

  // Si on demande une redirection
  if ($retour)
    $retours['redirect'] = $retour;

  return $retours;
}
?>