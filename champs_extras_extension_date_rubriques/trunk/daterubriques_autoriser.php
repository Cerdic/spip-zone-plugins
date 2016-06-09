<?php
/**
 * Plugin daterubriques
 *
 * @plugin     daterubriques
 * @copyright  2011-2016
 * @author     Touti, Yffic
 * @licence    GPL 3
 * @package    SPIP\daterubriques\autoriser
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function daterubriques_autoriser(){}

include_spip('inc/cextras_autoriser');
include_spip('inc/config');

if ($secteurs = lire_config('daterubriques/secteurs',array(0))
  AND $secteurs = array_filter($secteurs)
  AND count($secteurs)){
	restreindre_extras('rubrique', 'date_utile', $secteurs, 'secteur');
}
