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
		include_spip ('association_modules');
/// INITIALISATIONS
		list($id_groupe, $groupe) = $r;
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('gestion_groupes', 'adherents');
/// AFFICHAGES_LATERAUX : INFO
		$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		$infos['entete_commentaire'] = $groupe['commentaire'];
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_fonctions',"id_groupe=$id_groupe")) );
		echo association_tablinfos_intro($groupe['nom'], 'groupe', $id_groupe, $infos );
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('groupe_membres', 'grille-24.png', array('membres_groupe', "id=$id_groupe"), array('voir_groupes', 'association') ),
			array('tous_les_groupes', 'annonce.gif', array('groupes', "id=$id_groupe"), array('voir_groupes', 'association') ),
		) );
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('annonce.gif', 'suppression_de_groupe');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
		echo association_form_suppression('groupe', $id_groupe);
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>