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

function exec_edit_don() {
	$r = association_controle_id('auteur', 'asso_membres', 'editer_dons');
	if ($r) {
		list($id_auteur, $membre) = $r;
		exec_edit_don_args($id_auteur, $membre, association_passeparam_id('don'));
	}
}

function exec_edit_don_args($id_auteur, $membre, $id_don) {
	include_spip ('association_modules');
	echo association_navigation_onglets('titre_onglet_dons', 'dons');
	// INTRO : resume don
	echo association_totauxinfos_intro('', 'don', $id_don);
	// datation et raccourcis
	echo association_navigation_raccourcis(array(
		'tous_les_dons' => array('grille-24.png', array('dons', "id=$id_don"), array('voir_dons', 'association') ),
	) );
	debut_cadre_association('dons-24.gif', $id_don ? 'dons_titre_mise_a_jour' : 'ajouter_un_don');
	echo recuperer_fond('prive/editer/editer_asso_dons', array (
		'id_don' => $id_don,
		'id_auteur' => $id_auteur,
		'editable' => autoriser('editer_compta', 'association')
	));
	fin_page_association();
}

?>