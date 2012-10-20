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

// Export du Compte de Resultat au format LaTeX
// http://fr.wikipedia.org/wiki/LaTeX
function exec_export_soldescomptes_tex() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		exec_export_soldescomptes_latex();
	}
}

?>