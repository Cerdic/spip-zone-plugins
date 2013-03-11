<?php

// Activer le mode serveur du plugin Boussole en définissant son alias.
// -- Attention, ne pas utiliser l'alias "spip" réservé au serveur de la Galaxie SPIP.
if (!defined('_BOUSSOLE_ALIAS_SERVEUR'))
	define('_BOUSSOLE_ALIAS_SERVEUR', '');

// Liste des serveurs disponibles pour le client du plugin Boussole
// -- Par défaut, le serveur de la Galaxie SPIP est toujours disponible
$GLOBALS['client_serveurs_disponibles']['spip'] = array(
	'api' => 'http://boussole.spip.net/spip.php?action=[action][arguments]');

// Liste des boussoles "manuelles" (non fournies sous forme de plugin) disponibles sur le serveur
// -- par défaut le serveur n'en contient aucune
isset($GLOBALS['serveur_boussoles_disponibles']) AND $GLOBALS['serveur_boussoles_disponibles']
	? $GLOBALS['serveur_boussoles_disponibles']
	: array();
?>
