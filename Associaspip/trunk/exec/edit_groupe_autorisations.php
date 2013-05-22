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
		list($id_groupe, $groupe) = $r;
		include_spip ('association_modules');
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('gerer_les_autorisations', 'association');
/// AFFICHAGES_LATERAUX : INTRO : info groupe
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_fonctions',"id_groupe=$id_groupe")) );
		echo association_tablinfos_intro(_T("asso:groupe_".$id_groupe), 'groupe', $id_groupe, $infos );
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('groupe_membres', 'grille-24.png', array('membres_groupe', "id=$id_groupe"), array('voir_groupes', 'association') ),
			array('les_groupes_dacces', 'annonce.gif', array('association_autorisations', "id=$id_groupe"), array('gerer_autorisations', 'association', $id_groupe) ),
			array('synchroniser_asso_membres', 'reload-32.png', array('synchronis_groupe', "id=$id_groupe" ), test_plugin_actif('ACCESRESTREINT')?array('gerer_groupes', 'association', $id_groupe):FALSE ),
		) );
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('annonce.gif', 'titre_editer_groupe');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
		echo recuperer_fond('prive/editer/editer_asso_groupe', array (
			'id' => $id_groupe,
		));
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>