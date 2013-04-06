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

function exec_activites() {
	if (!autoriser('voir_activites', 'association') OR !(test_plugin_actif('AGENDA') OR test_plugin_actif('SIMPLECAL')) ) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		exec_activites_args(association_passeparam_id('evenement'));
	}
}

function exec_activites_args($id_evenement) {
	include_spip ('association_modules');
	list($id_periode, $critere_periode) = association_passeparam_periode('debut', 'evenement', $id_evenement);
	if ($id_evenement) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
		$id_mot = $incription = '';
	} else { // on peut prendre en compte les filtres ; on recupere les parametres de :
		$id_mot = association_recuperer_entier('mot'); // id du mot cle
		$inscription = _request('inscription');
	}
	echo association_navigation_onglets('titre_onglet_activite', 'activites');
	// TOTAUX : nombre d'activites de la periode en cours selon iscriptions
	$avec_inscrits = sql_countsel('spip_asso_activites', sql_in_select('id_evenement', 'id_evenement', 'spip_evenements', $critere_periode), 'id_evenement');
	echo association_totauxinfos_effectifs('activites', array(
		'pair'=>array( 'activites_sans_inscrits', (sql_countsel('spip_evenements', $critere_periode)-$avec_inscrits), ),
		'impair'=>array( 'activites_avec_inscrits', $avec_inscrits, ),
	));
	// STATS : places et participations pour la periode en cours
	echo association_totauxinfos_stats('participations_par_personne_par_activite', 'activites AS a INNER JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement', array('entete_quantite'=>'quantite','entete_montant'=>'prix_unitaire',), $critere_periode);
	// TOTAUX : montants des participations pour la periode
	echo association_totauxinfos_montants('activites', sql_getfetsel('SUM(prix_unitaire) AS somme_recettes', 'spip_asso_activites AS a INNER JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement', $critere_periode), 0);
	// datation et raccourci vers la gestion des evenements
	if ( test_plugin_actif('SIMPLECAL') ) { // gestion des evenements avec Simple Calendrier
		echo association_navigation_raccourcis(array(
			array('evenements', 'simplecal-logo-16.png', array('evenement_tous'), array('menu', 'evenements') ),
		), 4);
	} elseif ( test_plugin_actif('AGENDA') ) { // gestion des evenements avec Agenda 2
		echo association_navigation_raccourcis(array(
			array('evenements', 'img_pack/agenda-24.png', array('agenda_evenements'), array('menu', 'evenements'), ),
		), 70);
	} else { // pas de bloc de raccourcis
		echo association_date_du_jour();
		echo fin_boite_info(TRUE);
	}
	debut_cadre_association('activites.gif', 'activite_titre_toutes_activites');
	// FILTRES
	$filtre_motscles = '<select name="mot" onchange="form.submit()">';
	$filtre_motscles .= '<option value="">' ._T('asso:entete_tous') .'</option>';
	$query_groupes = sql_select('id_groupe, titre', 'spip_groupes_mots', "tables_liees LIKE '%evenements%'");
	while($data_groupes = sql_fetch($query_groupes)) {
		$filtre_motscles .= '<optgroup label="'.$data_groupes['titre'].'">';
		$query_mots = sql_select('id_mot, titre', 'spip_mots', 'id_groupe='.$data_groupes['id_groupe']);
		while($data_mots = sql_fetch($query_mots)) {
			$filtre_motscles .= '<option value="'.$data_mots['id_mot'].'"';
			$filtre_motscles .= ($id_mot==$data_mots['id_mot']?' selected="selected"':'');
			$filtre_motscles .= '>'.$data_mots['titre'].'</option>';
		}
		$filtre_motscles .= '</optgroup>';
	}
	$filtre_motscles .= '</select>';
	$filtre_incrits = '<select name="inscription" onchange="form.submit()">';
	$filtre_incrits .= '<option value="">' ._T('asso:entete_tous') .'</option>';
	$filtre_incrits .= '<option value="avec"';
	$filtre_incrits .= ($inscription=='avec'?' selected="selected"':'');
	$filtre_incrits .= '>'. _T('asso:activites_avec_inscrits') .'</option>';
	$filtre_incrits .= '<option value="sans"';
	$filtre_incrits .= ($inscription=='sans'?' selected="selected"':'');
	$filtre_incrits .= '>'. _T('asso:activites_sans_inscrits') .'</option>';
	$filtre_incrits .= '</select>';
	echo association_bloc_filtres(array(
		'periode' => array($id_periode, 'evenements', 'debut'),
#			'id' => $id_evenement,
	), 'activites', array(
		'mot' => $filtre_motscles,
		'inscription' => $filtre_incrits,
	));
	// TABLEAU
	$q_from = 'spip_evenements AS e LEFT JOIN spip_asso_activites AS a ON e.id_evenement=a.id_evenement';
	$q_where = $critere_periode;
	if ($id_mot) {
		$mc_sel = ', m.id_mot AS motact';
		$q_from .= ' LEFT JOIN spip_mots_evenements AS k ON  k.id_evenement=e.id_evenement LEFT JOIN spip_mots AS m ON k.id_mot=m.id_mot';
		//$q_where .= " AND (m.id_mot=$id_mot OR m.titre LIKE '$mot' OR m.titre IS NULL) ";
		$q_where .= " AND m.id_mot=$id_mot";
	} else {
		$mc_sel = '';
	}
	if ($inscription) {
		$q_having = 'inscriptions'. ($inscription=='avec'?'>':'=') .'0';
	} else {
		$q_having = '';
	}
	$limit = intval(_request('debut')) . "," . _ASSOCIASPIP_LIMITE_SOUSPAGE;
	$q = sql_select("e.id_evenement, e.date_debut, e.date_fin, e.titre  AS intitule, e.lieu,  COUNT(a.id_activite) AS inscriptions, SUM(a.quantite) AS quantites, SUM(a.prix_unitaire) AS montants, CASE COUNT(a.id_activite) WHEN 0 THEN 0 ELSE 1 END AS participations $mc_sel", $q_from, $q_where, 'e.id_evenement', 'date_debut DESC, date_fin DESC', $limit, $q_having);
	echo association_bloc_listehtml2('evenements', $q,
		array(
			'id_evenement' => array('asso:entete_id', 'entier'),
			'date_debut' => array('agenda:evenement_date_du', 'date', 'dtstart'),
			'date_fin' => array('agenda:evenement_date_au', 'date', 'dtend'),
			'intitule' => array('asso:entete_intitule', 'texte', '', '', 'summary'),
			'lieu' => array('agenda:evenement_lieu', 'texte', '', '', 'location'),
			'inscriptions' => array('asso:entete_nombre', 'entier'),
			'quantites' => array('asso:entete_quantite', 'entier'),
			'montants' => array('asso:entete_montant', 'prix', 'fee'),
		), // entetes et formats des donnees
		autoriser('voir_inscriptions', 'association') ? array(
			array('act', 'activite_bouton_ajouter_inscription', 'creer-12.gif', 'edit_activite', 'id_evenement=$$'),
			array('list', 'inscrits_activite', 'id=$$'),
		) : array(), // boutons d'action
		'id_evenement', // champ portant la cle des lignes et des boutons
		array('pair vevent', 'impair vevent'), 'participations', $id_evenement
	);
	//SOUS-PAGINATION
	echo association_selectionner_souspage(array($q_from, $q_where, 'e.id_evenement', $q_having), 'activites&'.($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode".($mot?"&mot=$mot":'').($inscription?"&inscription='$inscription'":'') );
	fin_page_association();
}

?>
