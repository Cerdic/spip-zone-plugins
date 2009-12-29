<?php
global $repertoire_squelettes_alternatifs;
global $styleListeSwitcher;

// Repertoire contenant les repertoires themes a tester
if (defined('SWITCHER_REPERTOIRE_SQUELETTES_ALTERNATIFS')) {
	$repertoire_squelettes_alternatifs = SWITCHER_REPERTOIRE_SQUELETTES_ALTERNATIFS;
} else {
	$repertoire_squelettes_alternatifs ='themes';
}

// Style css associe a la liste deroulante
$styleListeSwitcher="font-size: 10px;background-color: #FFF;color: #0C479D;border-top: 1px solid #CECECE; border-bottom: 2px solid #4A4A4A; border-left: 1px solid #CECECE; border-right: 1px solid #CECECE;margin:2px .5em;";

// Liste des squelettes definie dans mes_options avec
// define('SWITCHER_SQUELETTES','rep1:rep2:rep3');


?>