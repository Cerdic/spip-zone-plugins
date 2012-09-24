<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_ventes()
{
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		$id_vente = association_passeparam_id('vente');
		list($annee, $critere_periode) = association_passeparam_annee('vente', 'asso_ventes', $id_vente);
		if ($id_vente) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$etat = '';
		} else { // on peut prendre en compte les filtres ; on recupere les parametres de :
			$etat = _request('etat'); // etat d'avancement de la commande
		}
		onglets_association('titre_onglet_ventes', 'ventes');
		// INTRO : nom du module et annee affichee
		echo association_totauxinfos_intro('','ventes',$annee);
		// TOTAUX : nombre de ventes selon etat de livraison
		echo association_totauxinfos_effectifs('ventes', array(
			'pair' => array( 'ventes_enregistrees', sql_countsel('spip_asso_ventes', "date_envoi<date_vente AND  $critere_periode"), ),
			'impair' => array( 'ventes_expediees', sql_countsel('spip_asso_ventes', "date_envoi>=date_vente AND  $critere_periode"), ),
		));
		// STATS sur les paniers/achats/commandes
		echo association_totauxinfos_stats('paniers/commandes', 'ventes', array('entete_quantite'=>'quantite','entete_montant'=>'prix_vente*quantite',), $critere_periode);
		// TOTAUX : montants des ventes et des frais de port
/* Il est interessant d'interroger le livre comptable pour des cas complexes et si on sait recuperer les achats-depenses liees aux ventes(c'est faisable s'ils ne concerne qu'un ou deux comptes) ; mais ici, les montant etant dupliques dans la table des ventes autant faire simple...
		$data1 = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT(date, '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_ventes']) );
		$data2 = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT(date, '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_frais_envoi']) );
		echo association_totauxinfos_montants($annee, $data1['somme_recettes']-$data1['somme_depenses']+$data2['somme_recettes']-$data2['somme_depenses']);
*/
		$data = sql_fetsel('SUM(prix_vente*quantite) AS somme_ventes, SUM(frais_envoi) AS somme_frais', 'spip_asso_ventes', $critere_periode);
		echo association_totauxinfos_montants($annee, $data['somme_ventes']+$data['somme_frais'], $data['somme_frais']); // les frais de port etant facturees a l'acheteur, ce sont bien des recettes... mais ces frais n'etant (normalement) pas refacturees (et devant meme etre transparents) ils n'entrent pas dans la marge (enfin, facon de dire car les couts d'acquisition ne sont pas pris en compte... le "solde" ici est le montant effectif des ventes.)
		// datation et raccourcis
		raccourcis_association(array(), array(
			'ajouter_une_vente' => array('ajout-24.png', 'edit_vente'),
		) );
		debut_cadre_association('ventes.gif', 'toutes_les_ventes');
		// FILTRES
		$filtre_statut = '<select name="etat" onchange="form.submit()">';
		$filtre_statut .= '<option value="">' ._T('asso:entete_tous') .'</option>';
		$filtre_statut .= '<option value="encours"';
		$filtre_statut .= ($etat=='encours'?' selected="selected"':'');
		$filtre_statut .= '>'. _T('asso:ventes_enregistrees') .'</option>';
		$filtre_statut .= '<option value="traites"';
		$filtre_statut .= ($etat=='traites'?' selected="selected"':'');
		$filtre_statut .= '>'. _T('asso:ventes_expediees') .'</option>';
		$filtre_statut .= '</select>';
		filtres_association(array(
			'annee' => array($annee, 'asso_ventes', 'vente'),
#			'id' => $id_vente,
		), 'ventes', array(
			'etat' => $filtre_statut,
		));
		// TABLEAU
		$q_where = $critere_periode;
		switch ($etat) {
			case 'encours' :
				$q_where .= ' AND date_envoi<date_vente';
				break;
			case 'traites' :
				$q_where .= ' AND date_envoi>=date_vente';
				break;
			default :
				break;
		}
		echo association_bloc_listehtml(
			array('*, CASE WHEN date_envoi<date_vente THEN 0 ELSE 1 END AS statut_vente', 'spip_asso_ventes', $q_where, '',  'id_vente DESC'), // requete
			array(
				'id_vente' => array('asso:entete_id', 'entier'),
				'date_vente' => array('asso:ventes_entete_date_vente', 'date', 'dtstart'),
				'date_envoie' => array('asso:ventes_entete_date_envoi', 'date', 'dtend'),
				'article' => array('asso:entete_intitule', 'texte', 'propre', 'n'),
				'code' => array('asso:entete_code', 'code', 'x-spip_asso_ventes'),
				'id_acheteur' => array('asso:entete_nom', 'idnom', array('spip_asso_ventes', 'acheteur', 'id_acheteur'), 'membre'),
				'quantite' => array('asso:entete_quantite', 'nombre', 2, 'quantity'),
				'prix_vente' => array('asso:entete_montant', 'prix', 'purchase cost offer'),
//				'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
			), // entetes et formats des donnees
			array(
				array('suppr', 'vente', 'id=$$'),
				array('edit', 'vente', 'id=$$'),
			), // boutons d'action
			'id_vente', // champ portant la cle des lignes et des boutons
			array('pair hproduct', 'impair hproduct'), 'statut_vente', $id_vente // rel="purchase"
		);
		echo association_selectionner_souspage(array('spip_asso_ventes', $q_where), 'ventes', "annee=$annee".($etat?"&etat='$etat'":'') );
		fin_page_association();
	}
}

?>