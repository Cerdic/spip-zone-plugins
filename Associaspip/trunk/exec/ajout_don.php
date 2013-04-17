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

function exec_ajout_don() {
	$r = association_controle_id('auteur', 'asso_membres', 'editer_dons');
	if ($r) {
		include_spip('association_modules');
/// INITIALISATIONS
		list($id_auteur, $membre) = $r;
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('titre_onglet_dons', 'dons');
/// AFFICHAGES_LATERAUX : INTRO : resume don
		echo association_tablinfos_intro('', 'don', 0);
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('tous_les_dons', 'grille-24.png', array('dons'), array('voir_dons', 'association') ),
			array('adherent_titre_liste_actifs', 'grille-24.png', array('adherents', "id=$id_auteur"), array('voir_membres', 'association', 0) ),
#			array('adherent_label_modifier_membre', 'edit-24.gif', array('edit_adherent', "id=$id_auteur"), array('editer_membres', 'association') ),
			array('adherent_label_page_du_membre', 'annonce.gif', array('adherent', "id=$id_auteur"), array('voir_membres', 'association', $id_auteur) ),
			array('ecrire:info_informations_personnelles', 'membre_infos.png', array('auteur_infos', "id_auteur=$id_auteur"), autoriser('voir', 'auteur', $id_auteur) ),
		) );
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('dons-24.gif', 'ajouter_un_don');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
		echo recuperer_fond('prive/editer/editer_asso_dons', array (
			'id_don' => 0,
			'id_auteur' => $id_auteur,
			'editable' => autoriser('editer_compta', 'association')
		));
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>