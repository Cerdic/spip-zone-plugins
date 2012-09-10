<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function exec_adherent(){
	$id_auteur = intval(_request('id'));
	$full = autoriser('associer', 'adherents');
	if (!autoriser('voir_membres', 'association', $id_auteur)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$data = sql_fetsel('m.sexe, m.nom_famille, m.prenom, m.validite, m.id_asso, c.libelle','spip_asso_membres as m LEFT JOIN spip_asso_categories as c ON m.categorie=c.id_categorie', "m.id_auteur=$id_auteur");
		include_spip('inc/association_coordonnees');
		$nom_membre = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
		$validite = $data['validite'];
		$adresses = association_formater_adresses(array($id_auteur));
		$emails = association_formater_emails(array($id_auteur));
		$telephones = association_formater_telephones(array($id_auteur));
		$categorie = $data['libelle']?$data['libelle']:_T('asso:pas_de_categorie_attribuee');
		$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
		switch($statut)	{
			case '0minirezo':
				$statut='auteur'; break;
			case '1comite':
				$statut='auteur'; break;
			default :
				$statut='visiteur'; break;
		}
		onglets_association('titre_onglet_membres');
		// INFOS
		if ($full) {
			$infos['adherent_libelle_categorie'] = $categorie;
		}
		$infos['adherent_libelle_validite'] = association_formater_date($data['validite']);
		if ($GLOBALS['association_metas']['id_asso']) {
			$infos['adherent_libelle_reference_interne'] = ($data['id_asso']?_T('asso:adherent_libelle_reference_interne').'<br/>'.$data['id_asso']:_T('asso:pas_de_reference_interne_attribuee')) ;
		}
		if ($adresses[$id_auteur])
			$infos['adresses'] = $adresses[$id_auteur];
		if ($emails[$id_auteur])
			$infos['emails'] = $emails[$id_auteur];
		if ($telephones[$id_auteur])
			$infos['numeros'] =  $telephones[$id_auteur];
		echo '<div class="vcard">'. association_totauxinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos, 'asso', 'asso_membre') .'</div>';
		// datation et raccourcis
		if ($full)
			$res['adherent_label_modifier_membre'] = array('edit-24.gif', 'edit_adherent', "id=$id_auteur");
		include_spip('inc/texte'); // pour nettoyer_raccourci_typo
		$res["adherent_label_modifier_$statut"] = array('membre_infos.png', 'auteur_infos', "id_auteur=$id_auteur");
		raccourcis_association('', $res);
		debut_cadre_association('annonce.gif', 'membre', $nom_membre);
		if ($full)
			echo propre($data['commentaire']);
		$query_groupes = sql_select('g.id_groupe as id_groupe, g.nom as nom', 'spip_asso_groupes g LEFT JOIN spip_asso_groupes_liaisons l ON g.id_groupe=l.id_groupe', 'g.id_groupe>=100 AND l.id_auteur='.$id_auteur, '', 'g.nom'); // Liste des groupes (on ignore les groupes d'id <100 qui sont dedies a la gestion des autorisations)
		if (sql_count($query_groupes)) {
			echo debut_cadre_relief('', true, '', _T('asso:groupes_membre') );
			echo association_bloc_listehtml(
				$query_groupes, // requete
				array(
					'id_groupe' => array('asso:entete_id', 'entier'),
					'nom' => array('asso:groupe', 'texte'),
					'fonction' => array('asso:fonction', 'texte'),
				), // entetes et formats des donnees
				array(
					array('faire', 'membres', 'voir-12.gif', 'membres_groupe', 'id=$$'),
				), // boutons d'action
				'id_groupe' // champ portant la cle des lignes et des boutons
			);
			echo fin_cadre_relief(true);
		}
		if (test_plugin_actif('fpdf') AND $GLOBALS['association_metas']['recufiscal']) { // JUSTIFICATIFS : afficher le lien vers les justificatifs seulemeunt si active en configuration et si FPDF est actif
			echo debut_cadre_relief('', true, '', _T('asso:liens_vers_les_justificatifs') );
			$data = array_map('array_shift', sql_allfetsel("DATE_FORMAT(date, '%Y')  AS annee", 'spip_asso_comptes', "id_journal=$id_auteur", 'annee', 'annee ASC') );
			foreach($data as $k => $annee) {
				echo '<a href="'. generer_url_ecrire('pdf_fiscal', "id=$id_auteur&annee=$annee") .'">'.$annee.'</a> ';
			}
			echo fin_cadre_relief(true);
		}
		if ($GLOBALS['association_metas']['pc_cotisations']){ // HISTORIQUE COTISATIONS
			echo debut_cadre_relief('', true, '', _T('asso:adherent_titre_historique_cotisations') );
			if ($full) { // si on a l'autorisation admin, on ajoute un bouton pour ajouter une cotisation
				echo '<p><a href="' .generer_url_ecrire('ajout_cotisation', "id=$id_auteur").'">' . _T('asso:adherent_label_ajouter_cotisation') .'</a></p>';
			}
			$association_imputation = charger_fonction('association_imputation', 'inc');
			$critere = $association_imputation('pc_cotisations');
			if ($critere)
				$critere .= ' AND ';
			echo voir_adherent_paiements(
				array('id_compte, recette AS montant, date, justification, journal', 'spip_asso_comptes', "$critere id_journal=$id_auteur", '', 'date DESC, id_compte DESC', '0,10' ),
				$full,
				'cotisation'
			);
			echo fin_cadre_relief(true);
		}
		if ($GLOBALS['association_metas']['activites']){ // HISTORIQUE ACTIVITES
			echo debut_cadre_relief('', true, '', _T('asso:adherent_titre_historique_activites') );
			echo association_bloc_listehtml(
				array('*', 'spip_asso_activites As a INNER JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement', "id_adherent=$id_auteur", '', 'date_debut DESC, date_fin DESC', '0,10'), // requete
				array(
					'id_activite' => array('asso:entete_id', 'entier'),
					'date_debut' => array('asso:entete_date', 'date'),
					'titre' => array('asso:adherent_entete_activite', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
					'inscrits' => array('asso:adherent_entete_inscrits', 'entier'),
					'montant' => array('asso:entete_montant', 'prix'),
				), // entetes et formats des donnees
				$full ? array(
					array('faire', 'adherent_bouton_maj_inscription', 'edit-12.gif', 'edit_activite', 'id=$$'),
				) : array(), // boutons d'action
				'id_activite' // champ portant la cle des lignes et des boutons
			);
			echo fin_cadre_relief(true);
		}
		if ($GLOBALS['association_metas']['ventes']){ // HISTORIQUE VENTES
			echo debut_cadre_relief('', true, '', _T('asso:adherent_titre_historique_ventes') );
			echo association_bloc_listehtml(
				array('*', 'spip_asso_ventes', "id_acheteur=$id_auteur", '', 'date_vente DESC', '0,10'), // requete
				array(
					'id_vente' => array('asso:entete_id', 'entier'),
					'date_vente' => array('asso:entete_date', 'date'),
					'article' => array('asso:entete_article', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
					'quantite' => array('asso:entete_quantite', 'nombre'),
					'date_envoie' => array('asso:vente_entete_date_envoi', 'date'),
				), // entetes et formats des donnees
				$full ? array(
					array('faire', 'adherent_bouton_maj_vente', 'edit-12.gif', 'edit_vente', 'id=$$'),
				) : array(), // boutons d'action
				'id_vente' // champ portant la cle des lignes et des boutons
			);
			echo fin_cadre_relief(true);
		}
		if ($GLOBALS['association_metas']['dons']){ // HISTORIQUE DONS
			echo debut_cadre_relief('', true, '', _T('asso:adherent_titre_historique_dons') );
			echo association_bloc_listehtml(
				array('*', 'spip_asso_dons', "id_adherent=$id_auteur", '', 'date_don DESC', '0,10'), // requete
				array(
					'id_don' => array('asso:entete_id', 'entier'),
					'date_don' => array('asso:entete_date', 'date'),
					'argent' => array('asso:entete_montant', 'prix'),
					'colis' => array('asso:colis', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
//					'valeur' => array('asso:vente_entete_date_envoi', 'prix'),
				), // entetes et formats des donnees
				$full ? array(
					array('faire', 'adherent_bouton_maj_don', 'edit-12.gif', 'edit_don', 'id=$$'),
				) : array(), // boutons d'action
				'id_don' // champ portant la cle des lignes et des boutons
			);
/*
			$association_imputation = charger_fonction('association_imputation', 'inc');
			$critere = $association_imputation('pc_dons');
			if ($critere)
				$critere .= ' AND ';
			echo voir_adherent_paiements(
				array('D.id_don AS id, D.argent AS montant, D.date_don AS date, justification, journal, id_compte', 'spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don', "$critere id_adherent=$id_auteur",'D.date_don DESC', '0,10'),
				$full,
				'don'
			);
*/
			echo fin_cadre_relief(true);
		}
		if ($GLOBALS['association_metas']['prets']){ // HISTORIQUE PRETS
			echo debut_cadre_relief('', true, '', _T('asso:adherent_titre_historique_prets') );
			echo association_bloc_listehtml(
				array('*', 'spip_asso_prets AS P LEFT JOIN spip_asso_ressources AS R ON P.id_ressource=R.id_ressource', "id_emprunteur=$id_auteur", '', 'id_pret DESC', '0,10'), // requete
				array(
					'id_pret' => array('asso:entete_id', 'entier'),
					'date_sortie' => array('asso:prets_entete_date_sortie', 'date', 'dtstart'),
					'intitule' => array('asso:entete_article', 'texte', $full?'propre':'nettoyer_raccourcis_typo', ),
//					'duree' => array('asso:entete_duree', 'duree'),
					'date_retour' => array('asso:prets_entete_date_retour', 'date', 'dtend'),
				), // entetes et formats des donnees
				$full ? array(
					array('faire', 'adherent_bouton_maj_operation', 'edit-12.gif', 'edit_pret', 'id=$$'),
				) : array(), // boutons d'action
				'id_pret' // champ portant la cle des lignes et des boutons
			);
			echo fin_cadre_relief(true);
		}
		fin_page_association();
	}
}

function voir_adherent_paiements($data, $lien)
{
	return association_bloc_listehtml(
		$data, // requete
		array(
			'id_compte' => array('asso:entete_id', 'entier'),
			'date' => array('asso:entete_date', 'date'),
			'journal' => array('asso:adherent_entete_journal', 'texte'),
			'justification' => array('asso:adherent_entete_justification', 'texte', $lien?'propre':'nettoyer_raccourcis_typo', ),
			'montant' => array('asso:entete_montant', 'prix'),
		),
		$lien ? array(
			array('faire', 'adherent_label_voir_operation', 'voir-12.png', 'comptes', 'id_compte=$$'),
		) : array(), // boutons d'action : pas plutot edit_compte ? (a propos, il faudrait carrement un voir_compte pour ne pas risquer de modifier ainsi une operation marquee "vu" et donc archivee/verouillee)
		'id_compte' // champ portant la cle des lignes et des boutons
	);
}

?>