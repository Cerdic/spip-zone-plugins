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

function exec_inscrits_activite() {
	sinon_interdire_acces(autoriser('voir_inscriptions', 'association') AND (test_plugin_actif('AGENDA') OR test_plugin_actif('SIMPLECAL')) );
	include_spip('association_modules');
/// INITIALISATIONS
	$id_evenement = association_passeparam_id('evenement');
	list($id_periode, $critere_periode) = association_passeparam_periode('debut', 'evenements', $id_evenement);
	$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement");
	$statut = association_passeparam_statut();
	$suffixe_pdf = "inscriptions_$id_evenement".'_';
	if ($statut) { // restriction de la selection
		$critereSupplementaire = ' AND '. ($statut>0?"date_paiement<date_inscription ":"date_paiement>=date_inscription ");
		$suffixe_pdf .= ($statut>0?'avecpaie':'sanspaie');
	} else {
		$critereSupplementaire = '';
		$suffixe_pdf .= 'quelpaie';
	}
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_activite', 'activites');
/// AFFICHAGES_LATERAUX : INTRO : Rappel Infos Evenement
	$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
	$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
	$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
	$infos['agenda:evenement_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
	echo '<div class="vevent">'. association_tablinfos_intro('<span class="summary">'.$evenement['titre'].'</span>', 'evenement', $id_evenement, $infos, 'evenement') .'</div>';
/// AFFICHAGES_LATERAUX : TOTAUX : nombres d'inscriptions par etat de paiement
	echo association_tablinfos_effectifs('inscriptions', array(
		'pair' => array( 'asso:activite_entete_validees', array('spip_asso_activites', "id_evenement=$id_evenement AND date_paiement<date_inscription "), ),
		'valide' => array( 'asso:activite_entete_impayees', array('spip_asso_activites', "id_evenement=$id_evenement AND NOT date_paiement<date_inscription "), ),
	));
/// AFFICHAGES_LATERAUX : STATS sur les participations a cette activite (nombre de place et montant paye)
	echo association_tablinfos_stats('inscriptions', 'activites', array('entete_quantite'=>'quantite','entete_montant'=>'prix_unitaire',), "id_evenement=$id_evenement");
/// AFFICHAGES_LATERAUX : TOTAUX : montants des participations
	echo association_tablinfos_montants('inscriptions', array('SUM(prix_unitaire) AS encaisse', 'spip_asso_activites', "id_evenement=$id_evenement " ), NULL);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	$retour = '&retour=.%2F%3Fexec%3Dinscrits_activite%26amp%3Bid%3D'.$evenement['id_evenement']; // URL relative de cette page : parametre d'appel pour d'autres plus loin
	$res[] = array('activite_titre_toutes_activites', 'grille-24.png', array('activites', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode"), array('voir_activites', 'association') );
#	$res[] = array('ecrire:icone_modifier_article', 'edit-24.gif', array('articles_edit', 'id_article='.$evenement['id_article'].$retour), array('modifier', 'article', $evenement['id_article']) );
	$res[] = array('ecrire:icone_retour_article', 'images/article-24.gif', array('articles', 'id_article='.$evenement['id_article']), array('voir', 'article', $evenement['id_article']) );
	$res[] = array('activite_bouton_ajouter_inscription', 'panier_in.gif', array('edit_activite', "id_evenement=$id_evenement"), array('editer_inscriptions', 'association') );
	if ( test_plugin_actif('FPDF') && sql_countsel('spip_asso_activites', "id_evenement=$id_evenement", 'id_auteur') ) { // PDF des inscrits
		$res[] = array('activite_bouton_imprimer_inscriptions', 'print-24.png', generer_action_auteur('pdf_activite', $id_evenement), array('exporter_inscriptions', 'association') );
	}
	if ( test_plugin_actif('AGENDA') && (sql_countsel('spip_evenements_participants', "id_evenement=$id_evenement") OR sql_countsel('spip_asso_activites', "id_evenement=$id_evenement") ) ) {
		$res[] = array('agenda:titre_cadre_modifier_evenement', 'edit-24.gif', array('evenements_edit', 'id_evenement='.$evenement['id_evenement'].$retour), array('modifier', 'evenement', $evenement['id_evenement']) );
		$res[] = array('synchroniser_asso_activites', 'reload-32.png', array('synchronis_activite', "id=$id_evenement"), array('gerer_activites', 'association') );
#		$res[] = array('agenda:liste_inscrits', 'img_pack/agenda-24.png', array('agenda_inscriptions', 'id_evenement='.$evenement['id_evenement'].$retour), $GLOBALS['auteur_session']['statut']=='0minirezo' && $evenement['inscription'] ); // pour exporter, ajouter au lien le parametre : &format=csv
	}
	echo association_navigation_raccourcis($res, 14);
/// AFFICHAGES_LATERAUX : Forms-PDF
	if ( autoriser('exporter_membres', 'association') ) { // etiquettes
		echo association_form_etiquettes("id_evenement=$id_evenement $critereSupplementaire ", ' LEFT JOIN spip_asso_activites AS e ON m.id_auteur=e.id_auteur ', $suffixe_pdf);
	}
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
/// AFFICHAGES_CENTRAUX : FILTRES
	$filtre_statut = '<select name="statut" onchange="form.submit()" id="asso_statutinscription">';
	$filtre_statut .= '<option value="">' ._T('asso:entete_tous') .'</option>';
	$filtre_statut .= '<option value="1"'. (intval($statut)>0?' selected="selected"':'') .'>'. _T('asso:activite_entete_validees') .'</option>';
	$filtre_statut .= '<option value="-1"'. (intval($statut)<0?' selected="selected"':'') .'>'. _T('asso:activite_entete_impayees') .'</option>';
	$filtre_statut .= '</select>';
	echo association_form_filtres(array(
//		'periode' => array($id_periode, 'asso_activites', 'inscription'),
#		'id' => $id_activite,
	), 'inscrits_activite', array(
		'statut' => $filtre_statut,
		0 => '<input type="hidden" name="id" value="'.$id_evenement.'" />',
	));
/// AFFICHAGES_CENTRAUX : TABLEAU
	echo association_bloc_listehtml2('asso_activites',
		sql_select("*, CASE date_paiement WHEN '0000-00-00' THEN 0 ELSE 1 END AS statut_paiement ", 'spip_asso_activites', "id_evenement=$id_evenement $critereSupplementaire ", '', 'id_activite DESC'), // requete
		array(
			'id_activite' => array('asso:entete_id', 'entier'),
			'date_inscription' => array('asso:entete_date', 'date', ''),
//			'date_paiement' => array('asso:entete_date', 'date', ''),
			'id_auteur' => array('asso:entete_nom', 'idnom', array('spip_asso_activites', 'nom', 'id_auteur'), 'membre'),
			'quantite' => array('asso:entete_quantite', 'entier'),
			'prix_unitaire' => array('asso:entete_montant', 'prix', 'fees'),
			'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
		), // entetes et formats des donnees
		autoriser('editer_inscriptions', 'association') ? array(
			array('suppr', 'activite', 'id=$$'),
			array('paye', 'edit_activite', 'id=$$'),
			) : array(), // boutons d'action
		'id_activite', // champ portant la cle des lignes et des boutons
		array('pair', 'valide'), 'statut_paiement'
	);
/// AFFICHAGES_CENTRAUX : PAGINATION
	echo association_form_souspage(array('spip_asso_activites', "id_evenement=$id_evenement $critereSupplementaire "), 'inscrits_activite', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode".($statut?"&etat='$statut'":'') );
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>