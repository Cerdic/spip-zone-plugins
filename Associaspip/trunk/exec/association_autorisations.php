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

function exec_association_autorisations() {
	if (!autoriser('gerer_autorisations', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
		echo association_navigation_onglets('gerer_les_autorisations', 'association');
		// notice
		echo _T('asso:aide_gerer_autorisations');
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			array('association_infos_contacts', 'assoc_qui.png', array('association'), array('voir_profil', 'association') ),
		), 02);
		debut_cadre_association('annonce.gif', 'les_groupes_dacces');
		echo recuperer_fond('prive/contenu/voir_groupes_autorisations', array ());
		fin_page_association();
	}
}

?>