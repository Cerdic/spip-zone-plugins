<?php
/**
 * Options du plugin Réservation Événements
 *
 * @plugin     Réservation Événements
 * @copyright  2013 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Active no spam sur le formulaire reservation.
$GLOBALS['formulaires_no_spam'][] = 'reservation';

// Ajoute le plugin aux promotions.
$GLOBALS['promotion_plugin']['reservation_evenement'] = _T('reservation_evenement:reservation_evenement_titre');
