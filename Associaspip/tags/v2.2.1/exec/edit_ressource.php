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

function exec_edit_ressource() {
	sinon_interdire_acces(autoriser('editer_ressources', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_ressource = association_passeparam_id('ressource');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_prets', 'ressources');
/// AFFICHAGES_LATERAUX : INTRO : info ressource
	$infos['ressource_pretee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_prets', "id_ressource=$id_ressource"), ));
	echo association_tablinfos_intro(sql_getfetsel('intitule', 'spip_asso_ressources', "id_ressource=$id_ressource" ), 'ressource', $id_ressource, $infos );
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('titre_onglet_prets', 'grille-24.png', array('ressources', "id=$id_ressource"), array('voir_ressources', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('pret-24.gif', 'ressources_titre_edition_ressources');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_ressource', array (
		'id_ressource' => $id_ressource
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>