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

function exec_edit_categorie()
{
	if (!autoriser('editer_profil', 'association')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		$id_categorie = association_passeparam_id('categorie');
		onglets_association('categories_de_cotisations', 'association');
		// INTRO : resume ressource
		$infos['entete_utilisee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_membres', "categorie=$id_categorie"), ));
		echo association_totauxinfos_intro('', 'categorie', $id_categorie, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('cotisation.png', 'categories_de_cotisations');
		echo recuperer_fond('prive/editer/editer_asso_categories', array (
			'id_categorie' => $id_categorie
		));
		fin_page_association();
	}
}

?>