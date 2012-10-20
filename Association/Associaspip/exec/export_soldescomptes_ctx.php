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

// Export du Compte de Resultat au format CTX
// http://www.creativyst.com/Doc/Std/ctx/ctx.htm
function exec_export_soldescomptes_ctx() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/association_comptabilite');
		$ctx = new ExportComptes_TXT();
		$ctx->exportLignesUniques('|', "\n", array("\r"=>'\r', "\n"=>'\n', "\\"=>'\i', '|'=>'\p'), '', '');
		$ctx->leFichier('ctx');
	}
}

?>