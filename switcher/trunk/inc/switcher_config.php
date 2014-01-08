<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

global $repertoire_squelettes_alternatifs;
global $styleListeSwitcher;

include_spip('inc/config');
// Repertoire contenant les repertoires squelettes a tester
if (!function_exists('lire_config') || !$repertoire_squelettes_alternatifs= lire_config('switcher/repertoire')) {
    if (defined('SWITCHER_REPERTOIRE_SQUELETTES_ALTERNATIFS')) {
	    $repertoire_squelettes_alternatifs = SWITCHER_REPERTOIRE_SQUELETTES_ALTERNATIFS;
    } else {
	    $repertoire_squelettes_alternatifs ='squelettes-test';
    }
}

if ( ! defined('SWITCHER_DOSSIERS_SQUELETTES')) {
    define('SWITCHER_DOSSIERS_SQUELETTES', 'squelettes,squelettes-dist'.(function_exists('lire_config') ? ','.lire_config('switcher/dossiers_squelettes') : ''));
}

// Style css associe a la liste deroulante
$styleListeSwitcher="font-size: 10px;background-color: #FFF;color: #0C479D;border-top: 1px solid #CECECE; border-bottom: 2px solid #4A4A4A; border-left: 1px solid #CECECE; border-right: 1px solid #CECECE;margin:2px .5em;";

// Liste des squelettes definie dans mes_options avec
// define('SWITCHER_SQUELETTES','rep1:rep2:rep3');

// Booleen pour determiner qui a le droit de jouer ; par defaut, les admins.
if (!defined('SWITCHER_AFFICHER')) // true ou false
  define('SWITCHER_AFFICHER',
    $GLOBALS['auteur_session']['statut'] == '0minirezo'
  );

?>
