<?php
/**
 * Fonctions utiles au plugin Réservation Événements
 *
 * @plugin     Réservation Événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION'))
  return;

function champs_extras_reservation() {
  //les champs extras auteur
  include_spip('cextras_pipelines');

  if (function_exists('champs_extras_objet')) {
    //Charger les définitions pour la création des formulaires
    $champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));

  }

  return $champs_extras_auteurs;
}

function nom_statuts($statuts) {
  $liste_objets = lister_tables_objets_sql();

  $statuts_selectionnees = array();

  if (is_array($statuts)) {
    foreach ($liste_objets['spip_reservations']['statut_textes_instituer'] AS $statut => $label) {
      if (in_array($statut, $statuts))
        $statuts_selectionnees[$statut] = _T($label);
    }
  }
  return $statuts_selectionnees;
}

//retourne les statuts qui définissent si un événement est complet
function statuts_complet() {
  $statuts_complets = charger_fonction('complet', 'inc/statuts');
  $statuts = $statuts_complets();
  return $statuts;
}

/**
 * Cherche le label d'un champ extra
 *
 * @param  string $nom Le nom du champ.
 * @param  array $champs_extras Les champs extras.
 *
 * @return string Le label.
 */
function chercher_label($nom, $champs_extras = '') {
  $label = $nom;

  if (!$champs_extras) {
    //les champs extras auteur
    include_spip('cextras_pipelines');

    if (function_exists('champs_extras_objet')) {
      //Charger les définitions pour la création des formulaires
      $champs_extras = champs_extras_objet(table_objet_sql('auteur'));
    }
  }

  foreach ($champs_extras as $value) {
    if (isset($value['options']['nom']) and $value['options']['nom'] == $nom) {
      $label = $value['options']['label'];
    }
  }

  return $label;

}

/**
 * Cherche les infos d'un client
 *
 * @param  string $email L'email du client.
 * @param  string $champ Un champ spécifique.
 * @param  bin $retour_vide TRUE/FALSE.
 *
 * @return mixed La valeur du champ ou un tableau avec tous les champs.
 */
function infos_client($email, $champ='', $retour_vide = TRUE) {
  // Si on trouve un auteur spip on le prend, sinon on cherche dans les réservations
  if (!$client = sql_fetsel('*','spip_auteurs','email=' . sql_quote($email)))
    !$client  = sql_fetsel('*','spip_reservations','email=' . sql_quote($email),'','id_reservation DESC');
  
  // Si on a des informations on retrourne la valeur d'un champ
  // ou le tableau des infos selon ce qui es demandé.
  // sinon on ne retourne rien.
  if ($client) {
    if ($champ AND isset($client[$champ])) $infos= $client[$champ];
    else $infos= $client;
  }
  elseif($retour_vide) $infos = '';
  else $infos = $email;
  
  return $infos;
}

/*
 * Formater un nombre pour l'afficher comme un prix avec une devise
 *
 * @param float $prix Valeur du prix à formater
 * @param string $devise devise
 * @return string Retourne une chaine contenant le prix formaté avec une devise (par défaut l'euro)
 */
function prix_formater_devise($montant,$devise){ 
  include_spip('inc/config');
  include_spip('inc/cookie');

  $montant = number_format($montant, 2);

  // Si prix objets installés on recupère ses configs
  $config = lire_config('prix_objets');
  $devises = isset($config['devises']) ? $config['devises'] : array();

  //Si il y a un cookie 'geo_devise' et qu'il figure parmis les devises diponibles on le prend
  if (!$devise) {
    if (isset($_COOKIE['geo_devise']) AND in_array($_COOKIE['geo_devise'], $devises))
      $devise = $_COOKIE['geo_devise'];
    // Sinon on regarde si il ya une devise defaut valable
    elseif ($config['devise_default'] AND in_array($config['devise_default'], $devises))
      $devise = $config['devise_default'];
    // Sinon on prend la première des devises choisies
    elseif (isset($devises[0]))
      $devise = $devises[0];
    // Sinon on met l'Euro
    elseif(! defined('DEVISE_DEFAUT')) {
       $devise = 'EUR';
      }
    else $devise = DEVISE_DEFAUT;
     

    //On met le cookie
    spip_setcookie('geo_devise', $devise, time() + 3660 * 24 * 365, '/');
    define('DEVISE_DEFAUT',$devise);
  }

  //On détermine la langue du contexte
  $lang = $GLOBALS['spip_lang'];

  // Si PECL intl est présent on dermine le format de l'affichage de la devise selon la langue du contexte
  if (function_exists('numfmt_create')) {
    $fmt = numfmt_create($lang, NumberFormatter::CURRENCY);
    $montant = numfmt_format_currency($fmt, $montant, $devise);
  }
  //Sinon on formate à la française
  elseif (function_exists('traduire_devise'))
    $montant = $montant . '&nbsp;' . traduire_devise($devise);
  else
    $montant = $montant . '&nbsp;' . $devise;
  
  return $montant; 
}
