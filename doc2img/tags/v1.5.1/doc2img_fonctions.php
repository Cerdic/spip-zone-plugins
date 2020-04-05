<?php
/**
 * Plugin Doc2img
 * Fichier contenant les fonctions
 * 
 * @package SPIP\Doc2img\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Inclusion de inc/securiser_action pour le bouton de téléchargement de fichiers
 */
if (!function_exists('calculer_cle_action'))
	include_spip("inc/securiser_action");
?>