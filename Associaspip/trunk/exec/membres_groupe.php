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
	$r = association_controle_id('groupe', 'asso_groupes', 'voir_groupes');
	if ($r) {
		include_spip ('association_modules');
/// INITIALISATIONS
		list($id_groupe, $groupe) = $r;
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets(($id_groupe>=100)?'gestion_groupes':'gerer_les_autorisations', ($id_groupe>=100?'adherents':'association') );
/// AFFICHAGES_LATERAUX : INFO
		if ($id_groupe>=100) {
			$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		}
		if (test_plugin_actif('ACCESRESTREINT') AND $groupe['id_zone'])
			$infos['accesrestreint:page_zones_acces'] = sql_getfetsel('titre', 'spip_zones',"id_zone=$groupe[id_zone]");
		if ($groupe['commentaire'])
			$infos['entete_commentaire'] = $groupe['commentaire'];
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_fonctions',"id_groupe=$id_groupe")) );
		echo '<div class="vcard">'. association_tablinfos_intro( '<div class="org" id="vcard-group'.$groupe['id_groupe'].'"><abbr class="organization-name" title="'.$GLOBALS['association_metas']['nom'].'"></abbr><abbr class="organization-unit" title="'.$groupe['nom'] .'">'. (($id_groupe<100)?_T("asso:groupe_".$id_groupe):$groupe['nom']) .'</abbr></div>', 'groupe', $id_groupe, $infos ) .'</div>';
/// AFFICHAGES_LATERAUX : RACCOURCIS
		$res = array();
		$res[] = array(($id_groupe<100?'les_groupes_dacces':'tous_les_groupes'), 'grille-24.png', array(($id_groupe<100?'association_autorisations':'groupes'), "id=$id_groupe" ), array(($id_groupe<100?'gerer_autorisations':'voir_groupes'), 'association') );
		$res[] = array('editer_groupe', 'edit-24.gif', array(($id_groupe<100?'edit_groupe_autorisations':'edit_groupe'), "id=$id_groupe" ), array(($id_groupe<100?'gerer_autorisations':'editer_groupe'), 'association') );
		if (test_plugin_actif('ACCESRESTREINT') AND $groupe['id_zone']) {
			$res[] = array('accesrestreint:modifier_zone', 'img_pack/zones-acces-24.png', array('zones_edit', "id_zone=$groupe[id_zone]&retour=.%2F%3Fexec%3Dmembres_groupe%26amp%3Bid%3D".$groupe['id_groupe']), array('modifier', 'zone', $groupe['id_zone']), );
			$res[] = array('synchroniser_asso_groupes', 'reload-32.png', array('synchronis_groupe', "id=$id_groupe" ), array('gerer_groupes', 'association', $id_groupe) );
		}
		echo association_navigation_raccourcis($res, $id_groupe<100?10:11);
/// AFFICHAGES_LATERAUX : Forms-PDF
		if ( autoriser('exporter_membres', 'association') ) { // etiquettes
			echo association_form_etiquettes(" g.id_groupe=$id_groupe ", ' LEFT JOIN spip_asso_fonctions AS g ON m.id_auteur=g.id_auteur ', "groupe$id_groupe");
		}
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('annonce.gif', 'groupe_membres');
/// AFFICHAGES_CENTRAUX : TABLEAU
		echo recuperer_fond('modeles/membres_groupe', array(
			'id_groupe' => $id_groupe
		));
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>