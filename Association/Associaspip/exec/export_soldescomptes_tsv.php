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

// Export du Compte de Resultat au format TSV
// http://fr.wikipedia.org/wiki/Format_TSV
function exec_export_soldescomptes_tsv() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/association_comptabilite');
		$tsv = new ExportComptes_TXT();
		$tsv->exportLignesUniques("\t", "\n", array("\t"=>'\t',"\n"=>'\n'), '"', '"');
		$tsv->leFichier('tsv');
	}
}

?>