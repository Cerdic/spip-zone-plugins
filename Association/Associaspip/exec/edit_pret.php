<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_pret() {
	$id_pret = association_passeparam_id('pret');
	if (!autoriser('associer', 'activites')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('titre_onglet_prets', 'ressources');
		if ($id_pret) { // modifier
			$id_ressource = sql_getfetsel('id_ressource', 'spip_asso_prets', "id_pret=$id_pret");
		} else { // ajouter
			$id_ressource = association_passeparam_id('ressource');
		}
		// INTRO : resume ressource
		$infos['ressource_pretee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_prets', "id_ressource=$id_ressource"), ));
		echo association_totauxinfos_intro(sql_getfetsel('intitule', 'spip_asso_ressources', "id_ressource=$id_ressource" ), 'ressource', $id_ressource, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association(($id_pret?'edit-12.gif':'creer-12.gif'), 'prets_titre_edition_prets');
		echo recuperer_fond('prive/editer/editer_asso_prets', array (
			'id_ressource' => $id_ressource,
			'id_pret' => $id_pret,
		));
		fin_page_association();
	}
}

?>