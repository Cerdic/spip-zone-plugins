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

function exec_edit_groupe_autorisations() {
	$r = association_controle_id('groupe', 'asso_groupes', 'gerer_autorisations');
	if ($r) {
		include_spip ('inc/navigation_modules');
		list($id_groupe, $groupe) = $r;
		onglets_association('gerer_les_autorisations', 'association');
		// INFO
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
		echo association_totauxinfos_intro(_T("asso:groupe_".$id_groupe), 'groupe', $id_groupe, $infos );
		// datation et raccourcis
		raccourcis_association('association_autorisations');
		debut_cadre_association('annonce.gif', 'titre_editer_groupe');
		echo recuperer_fond('prive/editer/editer_asso_groupes', array (
			'id' => $id_groupe
		));
		fin_page_association();
	}
}

?>
