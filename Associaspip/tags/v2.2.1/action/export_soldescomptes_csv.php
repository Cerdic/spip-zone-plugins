<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Export du Compte de Resultat au format CSV
// http://fr.wikipedia.org/wiki/Comma-separated_values
// (forme commune de base : champs separes par une virgule et point decimal !)
function action_export_soldescomptes_csv() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(\d+)-(\w+)-(\w+)(-(\w+))?$,', $arg, $r)) {
		spip_log("action_export_soldescomptes incompris: " . $arg);
	} else {
		include_spip('inc/association_comptabilite');
		$csv = new ExportComptes_TXT();
		$csv->exportLignesUniques(',', "\n", array('"'=>'""'), '"', '"');
		$csv->leFichier('csv');
	}
}

?>