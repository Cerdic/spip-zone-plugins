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

// HISTORIQUE JUSTIFICATIFS 

function voir_adherent_recu_fiscal($id_auteur) {

	$data = array_map('array_shift', sql_allfetsel("DATE_FORMAT(date_operation, '%Y')  AS annee", 'spip_asso_comptes', "id_journal=$id_auteur", 'annee', 'annee ASC') );
	foreach($data as $k => $annee) {
		$data[$k] = '<a href="'. generer_action_auteur('pdf_fiscal', "$id_auteur-$annee") .'">'.$annee.'</a>';
	}
	return join("\n", $data);
}

/// HISTORIQUE COTISATIONS
function voir_adherent_cotisations($id_auteur, $full=false) {
	$association_imputation = charger_fonction('association_imputation', 'inc');
	$where =  $association_imputation('pc_cotisations', $id_auteur);
	$q = sql_select('id_compte, recette AS montant, date_operation, justification, journal', 'spip_asso_comptes', $where, '', 'date_operation DESC, id_compte DESC', '0,10' );
	return voir_adherent_paiements('asso_comptes', $q, $full);
}

/// HISTORIQUE ACTIVITES
function voir_adherent_activites($id_auteur) {

	return association_bloc_listehtml2('spip_asso_activites',
		sql_select('*', 'spip_asso_activites As a INNER JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement', "id_auteur=$id_auteur", '', 'date_debut DESC, date_fin DESC', '0,10'), // requete
		array(
			'id_activite' => array('asso:entete_id', 'entier'),
			'date_debut' => array('asso:entete_date', 'date'),
			'titre' => array('asso:adherent_entete_activite', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
			'quantite' => array('asso:entete_quantite', 'entier'),
			'prix_unitaire' => array('asso:entete_montant', 'prix'),
		), // entetes et formats des donnees
		autoriser('editer_activites', 'association') ? array(
			array('edit', 'activite', 'id=$$'),
		) : array(), // boutons d'action
		'id_activite' // champ portant la cle des lignes et des boutons
	);
}

/// HISTORIQUE VENTES
function voir_adherent_ventes($id_auteur) {

	return association_bloc_listehtml2('asso_ventes',
		sql_select('*', 'spip_asso_ventes', "id_auteur=$id_auteur", '', 'date_vente DESC', '0,10'), // requete
		array(
			'id_vente' => array('asso:entete_id', 'entier'),
			'date_vente' => array('asso:ventes_entete_date_vente', 'date'),
			'article' => array('asso:entete_article', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
			'quantite' => array('asso:entete_quantite', 'nombre'),
			'date_envoie' => array('asso:ventes_entete_date_envoi', 'date'),
		), // entetes et formats des donnees
		autoriser('voir_ventes', 'association') ? array(
			array('list', 'ventes', 'id=$$')
		) : array(), // boutons d'action
		'id_vente' // champ portant la cle des lignes et des boutons
	);
}

/// HISTORIQUE DONS

function voir_adherent_dons($id_auteur, $full=false) {

	return association_bloc_listehtml2('asso_dons',
		sql_select('*', 'spip_asso_dons', "id_auteur=$id_auteur", '', 'date_don DESC', '0,10'),
		array(
			'id_don' => array('asso:entete_id', 'entier'),
			'date_don' => array('asso:entete_date', 'date'),
			'argent' => array('asso:entete_montant', 'prix'),
			'colis' => array('asso:colis', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
		      ), // entetes et formats des donnees
		autoriser('voir_dons', 'association')
		? array(array('list', 'dons', 'id=$$'))
		: array(), // boutons d'action
		'id_don' // champ portant la cle des lignes et des boutons
					);
/*
		$association_imputation = charger_fonction('association_imputation', 'inc');
		$critere = $association_imputation('pc_dons');
		echo voir_adherent_paiements(
				array('D.id_don AS id, D.argent AS montant, D.date_don AS date, justification, journal, id_compte', 'spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don', "$critere AND id_auteur=$id_auteur",'D.date_don DESC', '0,10'),
				$full,
				'don'
			);
*/
}

/// HISTORIQUE DES PRETS

function voir_adherent_prets($id_auteur) {

	return association_bloc_listehtml2('asso_prets',
		sql_select('*', 'spip_asso_prets AS P LEFT JOIN spip_asso_ressources AS R ON P.id_ressource=R.id_ressource', "id_auteur=$id_auteur", '', 'id_pret DESC', '0,10'),
		array(
			'id_pret' => array('asso:entete_id', 'entier'),
			'date_sortie' => array('asso:prets_entete_date_sortie', 'date', 'dtstart'),
			'intitule' => array('asso:entete_article', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
	#		'duree' => array('asso:entete_duree', 'duree'),
					'date_retour' => array('asso:prets_entete_date_retour', 'date', 'dtend'),
		      ), // entetes et formats des donnees
		autoriser('voir_prets', 'association')
		? array(array('list', 'prets', 'id=$$'))
		: array(), // boutons d'action
		'id_pret' // champ portant la cle des lignes et des boutons
					);
}

function voir_adherent_paiements($table, $requete, $lien=false) {

	return association_bloc_listehtml2($table,
		$requete,
		array(
			'id_compte' => array('asso:entete_id', 'entier'),
			'date_operation' => array('asso:entete_date', 'date'),
			'journal' => array('asso:adherent_entete_journal', 'texte'),
			'justification' => array('asso:adherent_entete_justification', 'texte', $lien?'propre':'nettoyer_raccourcis_typo', ),
			'montant' => array('asso:entete_montant', 'prix'),
		),
		autoriser('voir_compta', 'association') ? array(
			array('list', 'comptes', 'id_compte=$$')
		) : array(), // boutons d'action : voir l'operation dans le journal comptable
		'id_compte' // champ portant la cle des lignes et des boutons
	);
}

?>