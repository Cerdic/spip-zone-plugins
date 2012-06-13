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

// Redirection vers le contenu d'un formulaire ajax pour une �dition dans la partie priv�e
// _request('edition') contient la fonction �diter
function exec_geolocaliser_dist($class = null)
{
	$formulaire = charger_fonction('geolocaliser', 'formulaires', true);
	ajax_retour($formulaire ? $formulaire() : 'editer quoi?');
}

?>
