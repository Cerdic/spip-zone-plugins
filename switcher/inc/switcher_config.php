<?php
global $repertoire_squelettes_alternatifs;
global $styleListeSwitcher;

// Repertoire contenant les repertoires squelettes a tester
$repertoire_squelettes_alternatifs ='squelettes-test';

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