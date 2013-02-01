<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010-2012 - Distribue sous licence GNU/GPL
 *
 * Options spécifiques à Diogene
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

if(!test_espace_prive() && !defined(_ACTIVER_PUCE_RAPIDE)){
	define(_ACTIVER_PUCE_RAPIDE,false);
}

?>