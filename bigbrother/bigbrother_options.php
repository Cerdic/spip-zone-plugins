<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

/**
 * Déclaration des pipelines du plugin
 */
$GLOBALS['spip_pipeline']['bigbrother_journaliser']="";

// Chargement de la librairie de fonctions
include_spip('inc/bigbrother');


// Si la config est ok, à chaque hit, on teste s'il faut enregistrer la visite ou pas
if (lire_config('bigbrother/enregistrer_visite') == 'oui')
	bigbrother_tester_la_visite_du_site();

if(!defined('_DIR_LIB_FLOT')){
	define('_DIR_LIB_FLOT','lib/flot');
}
?>
