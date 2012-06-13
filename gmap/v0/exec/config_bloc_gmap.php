<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de param�trage du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Ex�cution de la configuration d'un des �l�ments de la page configurer_gmap
function exec_config_bloc_gmap_dist($class = null)
{
	$configuration = charger_fonction(_request('configuration'), 'configuration', true);
	include_spip('inc/actions');
	ajax_retour($configuration ? $configuration() : 'configure quoi?');
}

?>
