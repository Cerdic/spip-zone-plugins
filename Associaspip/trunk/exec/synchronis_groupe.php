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

function exec_synchronis_groupe() {
	$r = association_controle_id('groupe', 'asso_groupes', 'gerer_groupes');
	include_spip('association_modules');
/// INITIALISATIONS
	if (test_plugin_actif('ACCESRESTREINT') AND $r) {
		list($id_groupe, $groupe) = $r;
		include_spip ('association_modules');
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('synchroniser_asso_membres', ($id_groupe>=100?'adherents':'association') );
/// AFFICHAGES_LATERAUX : INTRO : Infos Groupe
		$infos = array(); // reset...
		if ($id_groupe>=100) {
			$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		}
		$infos['entete_commentaire'] = $groupe['commentaire'];
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_fonctions',"id_groupe=$id_groupe")) );
		echo '<div class="vcard">'. association_tablinfos_intro( '<div class="org" id="vcard-group'.$groupe['id_groupe'].'"><abbr class="organization-name" title="'.$GLOBALS['association_metas']['nom'].'"></abbr><abbr class="organization-unit" title="'.$groupe['nom'] .'">'. (($id_groupe<100)?_T("asso:groupe_".$id_groupe):$groupe['nom']) .'</abbr></div>', 'groupe', $id_groupe, $infos ) .'</div>';
/// AFFICHAGES_LATERAUX : INTRO : Infos Zone
		$infos = array(); // reset...
		$zone = sql_fetsel('*', 'spip_zones', 'id_zone='.$groupe['id_zone']);
		if ($zone['publique'])
			$infos['accesrestreint:publique'] = _T('ecrire:item_'.$zone['publique']); // oui/non
		if ($zone['privee'])
			$infos['accesrestreint:privee'] = _T('ecrire:item_'.$zone['privee']); // oui/non
		if ($zone['descriptif'])
			$infos['accesrestreint:descriptif'] = $zone['descriptif'];
		$desc_table = charger_fonction('trouver_table', 'base');
		if ( $desc_table('spip_zones_rubriques') ) // SPIP2
			$rubs_w = sql_in_select('id_rubrique', 'id_rubrique', 'spip_zones_rubriques', 'id_zone='.$groupe['id_zone']);
		elseif ( $desc_table('spip_zones_liens') ) // SPIP3
			$rubs_w = sql_in_select('id_rubrique', 'id_objet', 'spip_zones_liens', "objet='rubrique' AND id_zone=".$groupe['id_zone']);
		else // ??
			$rubs_w = 'id_rubrique=0';
		$rubs_q = sql_select('id_rubrique, titre', 'spip_rubriques', $rubs_w, 'titre ASC');
		$rubs_l = '';
		while ($rub = sql_fetch($rubs_q) ) { // liste des rubriques...
			$rubs_l .= '<li><a class="spip_in" href="'. generer_url_ecrire('rubrique', 'id_rubrique=$rub[id_rubrique]') .'">'. $rub['titre'] .'</a></li>';
		}
		sql_free($rubs_q);
		$infos['accesrestreint:rubriques_zones_acces'] = $rubs_l ? "<ul class='spip'>$rubs_l</ul>" : _T('accesrestreint:aucune_rubrique');
		echo '<hr class="spip" />'. association_tablinfos_intro($zone['titre'], 'zone', $groupe['id_zone'], $infos);
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('groupe_membres', 'grille-24.png', array('membres_groupe', "id=$id_groupe" ), array('voir_groupes', 'association', $id_groupe) ),
			array(($id_groupe<100?'les_groupes_dacces':'tous_les_groupes'), 'annonce.gif', array(($id_groupe<100?'association_autorisations':'groupes'), "id=$id_groupe" ), array(($id_groupe<100?'gerer_autorisations':'voir_groupes'), 'association') ),
			array('editer_groupe', 'edit-24.gif', array(($id_groupe<100?'edit_groupe_autorisations':'edit_groupe'), "id=$id_groupe" ), array(($id_groupe<100?'gerer_autorisations':'editer_groupe'), 'association') ),
			array('accesrestreint:modifier_zone', 'img_pack/zones-acces-24.png', array('zones_edit', "id_zone=$groupe[id_zone]&retour=.%2F%3Fexec%3Dsynchronis_groupe%26amp%3Bid%3D".$groupe['id_groupe']), array('modifier', 'zone', $groupe['id_zone']) ),
		) );
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('reload-32.png', 'options_synchronisation');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
		echo recuperer_fond('prive/editer/synchroniser_asso_groupe', array (
			'id_groupe' => $id_groupe,
		));
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>