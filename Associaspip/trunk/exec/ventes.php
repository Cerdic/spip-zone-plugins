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

function exec_ventes() {
	if (!autoriser('voir_ventes', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
/// INITIALISATIONS
		$id_vente = association_passeparam_id('vente');
		list($id_periode, $critere_periode) = association_passeparam_annee('vente', 'asso_ventes', $id_vente);
		if ($id_vente) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$etat = '';
			$suffixe_pdf = "vente$id_vente";
		} else { // on peut prendre en compte les filtres ; on recupere les parametres de :
			$etat = _request('etat'); // etat d'avancement de la commande
			$suffixe_pdf = "ventes_$id_periode".'_'.($etat?$etat:'tous');
		}
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
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('titre_onglet_ventes', 'ventes');
/// AFFICHAGES_LATERAUX : TOTAUX : nombre de ventes selon etat de livraison
		echo association_tablinfos_effectifs('ventes', array(
			'pair' => array( 'ventes_enregistrees', sql_countsel('spip_asso_ventes', "date_envoi<date_vente AND  $critere_periode"), ),
			'impair' => array( 'ventes_expediees', sql_countsel('spip_asso_ventes', "date_envoi>=date_vente AND  $critere_periode"), ),
		));
/// AFFICHAGES_LATERAUX : STATS sur les paniers/achats/commandes
		echo association_tablinfos_stats('paniers/commandes', 'ventes', array('entete_quantite'=>'quantite','entete_montant'=>'prix_unitaire*quantite',), $critere_periode);
/// AFFICHAGES_LATERAUX : TOTAUX : montants des ventes et des frais de port
		$data = sql_fetsel('SUM(prix_unitaire*quantite) AS somme_ventes, SUM(frais_envoi) AS somme_frais', 'spip_asso_ventes', $critere_periode);
		echo association_tablinfos_montants($id_periode, $data['somme_ventes']+$data['somme_frais'], $data['somme_frais']); // les frais de port etant facturees a l'acheteur, ce sont bien des recettes... mais ces frais n'etant (normalement) pas refacturees (et devant meme etre transparents) ils n'entrent pas dans la marge (enfin, facon de dire car les couts d'acquisition ne sont pas pris en compte... le "solde" ici est le montant effectif des ventes.)
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('ajouter_une_vente', 'ajout-24.png', array('edit_vente'), array('gerer_ventes', 'association') ),
		), 3);
/// AFFICHAGES_LATERAUX : Forms-PDF
		if ( autoriser('exporter_membres', 'association') ) { // etiquettes
			echo association_form_etiquettes($q_where, ' LEFT JOIN spip_asso_ventes AS v ON m.id_auteur=v.id_auteur ', $suffixe_pdf);
		}
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('ventes.gif', 'toutes_les_ventes');
/// AFFICHAGES_CENTRAUX : FILTRES
		$filtre_statut = '<select name="etat" onchange="form.submit()">';
		$filtre_statut .= '<option value="">' ._T('asso:entete_tous') .'</option>';
		$filtre_statut .= '<option value="encours"';
		$filtre_statut .= ($etat=='encours'?' selected="selected"':'');
		$filtre_statut .= '>'. _T('asso:ventes_enregistrees') .'</option>';
		$filtre_statut .= '<option value="traites"';
		$filtre_statut .= ($etat=='traites'?' selected="selected"':'');
		$filtre_statut .= '>'. _T('asso:ventes_expediees') .'</option>';
		$filtre_statut .= '</select>';
		echo association_form_filtres(array(
			'periode' => array($id_periode, 'asso_ventes', 'vente'),
#			'id' => $id_vente,
		), 'ventes', array(
			'etat' => $filtre_statut,
		));
/// AFFICHAGES_CENTRAUX : TABLEAU
		echo association_bloc_listehtml2('asso_ventes',
			sql_select('*, CASE WHEN date_envoi<date_vente THEN 0 ELSE 1 END AS statut_vente', 'spip_asso_ventes', $q_where, '',  'id_vente DESC'),
			array(
				'id_vente' => array('asso:entete_id', 'entier'),
				'date_vente' => array('asso:ventes_entete_date_vente', 'date', 'dtstart'),
				'date_envoie' => array('asso:ventes_entete_date_envoi', 'date', 'dtend'),
				'article' => array('asso:entete_intitule', 'texte', 'propre', 'n'),
				'code' => array('asso:entete_code', 'code', 'x-spip_asso_ventes'),
				'id_auteur' => array('asso:entete_nom', 'idnom', array('spip_asso_ventes', 'nom', 'id_auteur'), 'membre'),
				'quantite' => array('asso:entete_quantite', 'nombre', 2, 'quantity'),
				'prix_unitaire' => array('asso:entete_montant', 'prix', 'purchase cost offer'),
//				'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
			), // entetes et formats des donnees
			autoriser('editer_ventes', 'association') ? array(
				array('suppr', 'vente', 'id=$$'),
				array('edit', 'vente', 'id=$$'),
			) : array(), // boutons d'action
			'id_vente', // champ portant la cle des lignes et des boutons
			array('pair hproduct', 'impair hproduct'), 'statut_vente', $id_vente // rel="purchase"
		);
/// AFFICHAGES_CENTRAUX : PAGINATION
		echo association_form_souspage(array('spip_asso_ventes', $q_where), 'ventes', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode".($etat?"&etat='$etat'":'') );
		fin_page_association();
	}
}

?>