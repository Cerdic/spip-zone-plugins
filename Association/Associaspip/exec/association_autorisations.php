<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_association_autorisations()
{
	if (!autoriser('gerer_autorisations', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('gerer_les_autorisations', 'association');
		// notice
		echo _T('asso:aide_gerer_autorisations');
		// datation et raccourcis
		raccourcis_association('association');
		debut_cadre_association('annonce.gif', 'les_groupes_dacces');
		echo recuperer_fond('prive/contenu/voir_groupes_autorisations', array ());
		fin_page_association();
	}
}

?>
