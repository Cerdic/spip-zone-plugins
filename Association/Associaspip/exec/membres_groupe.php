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

function exec_membres_groupe() {
	$r = association_controle_id('groupe', 'asso_groupes', 'voir_groupes', 'association');
	if ($r) {
		include_spip ('inc/navigation_modules');
		list($id_groupe, $groupe) = $r;
		onglets_association(($id_groupe>=100)?'gestion_groupes':'gerer_les_autorisations', ($id_groupe>=100?'adherents':'association') );
		// INFO
		if ($id_groupe>=100) {
			$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		}
		$infos['entete_commentaire'] = $groupe['commentaire'];
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
		echo '<div class="vcard">'. association_totauxinfos_intro( '<div class="org" id="vcard-group'.$groupe['id_groupe'].'"><abbr class="organization-name" title="'.$GLOBALS['association_metas']['nom'].'"></abbr><abbr class="organization-unit" title="'.$groupe['nom'] .'">'. (($id_groupe<100)?_T("asso:groupe_".$id_groupe):$groupe['nom']) .'</abbr></div>', 'groupe', $id_groupe, $infos ) .'</div>';
		// datation et raccourcis
		raccourcis_association('', array(
			'editer_groupe' => array('edit-24.gif', array(($id_groupe<100?'edit_groupe_autorisations':'edit_groupe'), "id=$id_groupe" ) ),
		) );
		debut_cadre_association('annonce.gif', 'groupe_membres');
		echo recuperer_fond('modeles/asso_membres', array(
			'id_groupe' => $id_groupe
		));
		fin_page_association();
	}
}

?>