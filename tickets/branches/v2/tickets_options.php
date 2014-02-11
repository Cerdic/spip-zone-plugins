<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * @package SPIP\Tickets\Options
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Activer le plugin no_spam sur les tickets
 */
$GLOBALS['formulaires_no_spam'][] = 'editer_ticket';
// Liste des pages de configuration dans l'ordre de presentation
define('_TICKETS_PAGES_CONFIG', 'general:autorisations');

?>
