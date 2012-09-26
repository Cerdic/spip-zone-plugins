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

function exec_edit_ressource()
{
	if (!autoriser('associer', 'ressources')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('titre_onglet_prets', 'ressources');
		$id_ressource = association_passeparam_id('ressource');
		// INTRO : resume ressource
		$infos['ressource_pretee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_prets', "id_ressource=$id_ressource"), ));
		echo association_totauxinfos_intro(sql_getfetsel('intitule', 'spip_asso_ressources', "id_ressource=$id_ressource" ), 'ressource', $id_ressource, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('pret-24.gif', 'ressources_titre_edition_ressources');
		echo recuperer_fond('prive/editer/editer_asso_ressources', array (
			'id_ressource' => $id_ressource
		));
		fin_page_association();
	}
}

?>