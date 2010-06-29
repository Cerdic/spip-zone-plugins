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

include_spip('inc/bigbrother');

if(!defined('_DIR_LIB_FLOT')){
	define('_DIR_LIB_FLOT','lib/flot');
}

define('_CACHE_CONTEXTES_AJAX',true);
?>