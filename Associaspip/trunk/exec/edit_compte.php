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

function exec_edit_compte() {
	if (!autoriser('editer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
		$id_compte = association_passeparam_id('compte');
		echo association_navigation_onglets('titre_onglet_comptes', 'comptes');
		// INTRO : resume compte
		echo association_totauxinfos_intro('', 'compte', $id_compte);
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			'informations_comptables' => array('grille-24.png', array('comptes', "id=$id_compte"), array('gerer_compta', 'association') ),
		) );
		debut_cadre_association('compts.gif', 'modification_des_comptes');
		echo recuperer_fond('prive/editer/editer_asso_comptes', array (
			'id_compte' => $id_compte
		));
		fin_page_association();
	}
}

?>
