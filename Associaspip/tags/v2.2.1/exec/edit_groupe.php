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

function exec_edit_groupe() {
	sinon_interdire_acces(autoriser('editer_groupes', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_groupe = association_passeparam_id('groupe');
	if (!$id_groupe)
		$r = array(0, array());
	else
		$r = association_controle_id('groupe', 'asso_groupes');
	if ($r) {
		include_spip ('association_modules');
		list($id_groupe, $groupe) = $r;
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('gestion_groupes', 'adherents');
/// AFFICHAGES_LATERAUX : INTRO : info groupe
		if ($groupe) {
			$infos = sql_countsel('spip_asso_fonctions',"id_groupe=$id_groupe");
			$infos = array('entete_utilise' => _T('asso:nombre_fois', array('nombre'=> $info )));
			echo association_tablinfos_intro($groupe['nom'], 'groupe', $id_groupe, $infos );
			$titre = 'titre_editer_groupe';
		} else
			$titre = 'titre_creer_groupe';
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('groupe_membres', 'grille-24.png', array('membres_groupe', "id=$id_groupe"), array('voir_groupes', 'association') ),
			array('tous_les_groupes', 'annonce.gif', array('groupes', "id=$id_groupe"), array('voir_groupes', 'association', $id_groupe) ),
			array('synchroniser_asso_groupes', 'reload-32.png', array('synchronis_groupe', "id=$id_groupe" ), test_plugin_actif('ACCESRESTREINT')?array('gerer_groupes', 'association', $id_groupe):FALSE ),
		) );
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('annonce.gif', $titre);
/// AFFICHAGES_CENTRAUX : FORMULAIRE
		echo recuperer_fond('prive/editer/editer_asso_groupe', array (
			'id' => $id_groupe,
		));
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>