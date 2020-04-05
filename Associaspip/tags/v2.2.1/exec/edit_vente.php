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

function exec_edit_vente() {
	sinon_interdire_acces(autoriser('editer_ventes', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_vente = association_passeparam_id('vente');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_ventes', 'ventes');
/// AFFICHAGES_LATERAUX : INTRO : info vente
	echo association_tablinfos_intro('', 'vente', $id_vente);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('titre_onglet_ventes', 'grille-24.png', array('ventes', "id=$id_vente"), array('voir_ventes', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('ventes.gif', 'ressources_titre_mise_a_jour');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_vente', array (
		'id_vente' => $id_vente
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>