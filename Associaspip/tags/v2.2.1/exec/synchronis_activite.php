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

function exec_synchronis_activite() {
	sinon_interdire_acces(autoriser('gerer_activites', 'association'));
	include_spip('association_modules');
/// INITIALISATIONS
	$id_evenement = association_passeparam_id('evenement');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('synchroniser_asso_membres', 'activites');
/// AFFICHAGES_LATERAUX : INTRO : Rappel Infos Evenement
	$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement") ;
	$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
	$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
	$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
	$infos['agenda:evenement_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
	echo '<div class="vevent">'. association_tablinfos_intro('<span class="summary">'.$evenement['titre'].'</span>', 'evenement', $id_evenement, $infos, 'evenement') .'</div>';
	$reponses = sql_allfetsel('reponse, COUNT(*) AS nombre', 'spip_evenements_participants', "id_evenement=$id_evenement", 'reponse', 'reponse DESC');
	foreach ($reponses as $num=>$rep ) { // re-normaliser le tableau des reponses
		switch ( $rep['reponse'] ) { // mettre la l'identifiant de la reponse en cle et rajouter au debut du tableau la chaine de langue
			case 'oui' :
				$reponses['oui'] = array('agenda:label_reponse_jyparticipe', $rep['nombre'], );
				break;
			case 'non' :
				$reponses['non'] = array('agenda:label_reponse_jyparticipe_pas', $rep['nombre'], );
				break;
			case '?' :
				$reponses['nsp'] = array('agenda:label_reponse_jyparticipe_peutetre', $rep['nombre'], );
				break;
			default : // autres (rajouts en dehors du plugin Agenda 2)
				$reponses['reponse_'.$rep['reponse']] = array('reponse_'.$rep['reponse'], $rep['nombre'], );
				break;
		}
		unset($reponses[$num]); // supprimer l'ancienne entree (le tableau final aura le meme nombre d'elements)
	}
/// AFFICHAGES_LATERAUX : TOTAUX : nombres d'inscrits par reponse
	echo association_tablinfos_effectifs('inscriptions',  $reponses);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('activite_titre_inscriptions_activites', 'grille-24.png', array('inscrits_activite', "id=$id_evenement"), array('voir_inscriptions', 'association') ),
		array('agenda:liste_inscrits', 'grille-24.png', array('agenda_inscriptions', 'id_evenement='.$evenement['id_evenement']), sql_countsel('spip_evenements_participants', "id_evenement=$id_evenement") AND $GLOBALS['auteur_session']['statut']=='0minirezo' AND $evenement['inscription'] ),
		array('agenda:telecharger', 'images/synchro-24.gif', array('agenda_inscriptions', 'format=csv&id_evenement='.$evenement['id_evenement']), (sql_countsel('spip_evenements_participants', "id_evenement=$id_evenement") && $GLOBALS['auteur_session']['statut']=='0minirezo' && $evenement['inscription'])?TRUE:FALSE ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('reload-32.png', 'options_synchronisation');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/synchroniser_asso_activite', array (
		'id_evenement' => $id_evenement,
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>