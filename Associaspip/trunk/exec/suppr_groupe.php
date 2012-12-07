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

function exec_suppr_groupe() {
	$r = association_controle_id('groupe', 'asso_groupes', 'editer_groupes');
	if ($r) {
		include_spip ('inc/navigation_modules');
		list($id_groupe, $groupe) = $r;
		onglets_association('gestion_groupes', 'adherents');
		// INFO
		$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		$infos['entete_commentaire'] = $groupe['commentaire'];
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
		echo association_totauxinfos_intro($groupe['nom'], 'groupe', $id_groupe, $infos );
		// datation et raccourcis
		echo association_navigation_raccourcis('');
		debut_cadre_association('annonce.gif', 'suppression_de_groupe');
		echo association_bloc_suppression('groupe', $id_groupe);
		fin_page_association();
	}
}

?>
