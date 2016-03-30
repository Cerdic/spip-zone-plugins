<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * Distribue sous licence GNU/GPL
 *
 * Options spécifiques à Diogene
 * 
 * @package SPIP\Diogene\Options
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * On désactive les puce rapide dans le public
 * Demande trop de css et autres trucs pour être sûr que ce soit correct partout
 */
if(!test_espace_prive() && !defined(_ACTIVER_PUCE_RAPIDE))
	define(_ACTIVER_PUCE_RAPIDE,false);


?>