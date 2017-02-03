<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'urls_pages_nom' => 'URLs Pages Personnalisées',
	'urls_pages_slogan' => 'Gestion des URLs de type « page »',
	'urls_pages_description' => 'Ce plugin étend la gestion des URLs personnalisées aux « pages »,
	ces squelettes ne correspondant pas aux objets éditoriaux, et pris en charge par la balise #URL_PAGE.

	Ainsi, {{spip.php?page=truc}} pourra devenir l\'URL de votre choix : {{url-personnalisee-pour-la-page-truc}}.

	Il faut que la « gestion avancée des URLs » soit activée, via le menu {{Configuration → Configurer les URLs}}.
	Rendez-vous ensuite dans le menu [{{Publication &rarr; Gestion des URLs &rarr; URLs des pages->?exec=controler_urls_pages]}}.'

);

?>
