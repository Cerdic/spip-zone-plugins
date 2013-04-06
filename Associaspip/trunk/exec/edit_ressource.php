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
	if (!autoriser('editer_ressources', 'association')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		include_spip ('association_modules');
		echo association_navigation_onglets('titre_onglet_prets', 'ressources');
		$id_ressource = association_passeparam_id('ressource');
		// INTRO : resume ressource
		$infos['ressource_pretee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_prets', "id_ressource=$id_ressource"), ));
		echo association_totauxinfos_intro(sql_getfetsel('intitule', 'spip_asso_ressources', "id_ressource=$id_ressource" ), 'ressource', $id_ressource, $infos );
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			array('titre_onglet_prets', 'grille-24.png', array('ressources', "id=$id_ressource"), array('voir_ressources', 'association') ),
		) );
		debut_cadre_association('pret-24.gif', 'ressources_titre_edition_ressources');
		echo recuperer_fond('prive/editer/editer_asso_ressources', array (
			'id_ressource' => $id_ressource
		));
		fin_page_association();
	}
}

?>