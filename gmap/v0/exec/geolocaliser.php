<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de paramétrage du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Redirection vers le contenu d'un formulaire ajax pour une édition dans la partie privée
// _request('edition') contient la fonction éditer
function exec_geolocaliser_dist($class = null)
{
	$formulaire = charger_fonction('geolocaliser', 'formulaires', true);
	ajax_retour($formulaire ? $formulaire() : 'editer quoi?');
}

?>
