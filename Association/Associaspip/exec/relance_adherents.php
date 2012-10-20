<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION"))
	return;

function exec_relance_adherents() {
	if (!autoriser('editer_membres', 'association')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		include_spip('inc/navigation_modules');
		onglets_association('titre_onglet_membres', 'adherents');
		// notice ?
		echo _T('asso:aide_relances');
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('relance-24.png', 'relance_de_cotisations');
		echo recuperer_fond('prive/editer/relancer_adherents');
		fin_page_association();
	}
}

?>