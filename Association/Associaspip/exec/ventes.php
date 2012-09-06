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

include_spip ('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function exec_ventes()
{
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_vente = intval(_request('id'));
		if ($id_vente) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$annee = sql_getfetsel("DATE_FORMAT(date_vente, '%Y')",'spip_asso_ventes', "id_vente=$id_vente"); // on recupere l'annee correspondante
		} else {
			$annee = intval(_request('annee')); // on recupere l'annee requetee
			$id_vente = ''; // ne pas afficher ce disgracieux '0'
		}
		if (!$annee) {
			$annee = date('Y'); // par defaut c'est l'annee courante
			$id_vente = ''; // virer l'ID inexistant
		}
		onglets_association('titre_onglet_ventes', 'ventes');
		// INTRO : nom du module et annee affichee
		echo association_totauxinfos_intro('','ventes',$annee);
		// TOTAUX : nombre de ventes selon etat de livraison
		echo association_totauxinfos_effectifs('ventes', array(
			'pair' => array( 'ventes_enregistrees', sql_countsel('spip_asso_ventes', "date_envoi<date_vente AND  DATE_FORMAT(date_vente, '%Y')=$annee "), ),
			'impair' => array( 'ventes_expediees', sql_countsel('spip_asso_ventes', "date_envoi>=date_vente AND  DATE_FORMAT(date_vente, '%Y')=$annee "), ),
		));
		// STATS sur les paniers/achats/commandes
		echo association_totauxinfos_stats('paniers/commandes', 'ventes', array('entete_quantite'=>'quantite','entete_montant'=>'prix_vente*quantite',), "DATE_FORMAT(date_vente, '%Y')=$annee");
		// TOTAUX : montants des ventes et des frais de port
/* Il est interessant d'interroger le livre comptable pour des cas complexes et si on sait recuperer les achats-depenses liees aux ventes(c'est faisable s'ils ne concerne qu'un ou deux comptes) ; mais ici, les montant etant dupliques dans la table des ventes autant faire simple...
		$data1 = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT(date, '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_ventes']) );
		$data2 = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT(date, '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_frais_envoi']) );
		echo association_totauxinfos_montants($annee, $data1['somme_recettes']-$data1['somme_depenses']+$data2['somme_recettes']-$data2['somme_depenses']);
*/
		$data = sql_fetsel('SUM(prix_vente*quantite) AS somme_ventes, SUM(frais_envoi) AS somme_frais', 'spip_asso_ventes', "DATE_FORMAT(date_vente, '%Y')=$annee" );
		echo association_totauxinfos_montants($annee, $data['somme_ventes']+$data['somme_frais'], $data['somme_frais']); // les frais de port etant facturees a l'acheteur, ce sont bien des recettes... mais ces frais n'etant (normalement) pas refacturees (et devant meme etre transparents) ils n'entrent pas dans la marge (enfin, facon de dire car les couts d'acquisition ne sont pas pris en compte... le "solde" ici est le montant effectif des ventes.)
		// datation et raccourcis
		raccourcis_association(array(), array(
			'ajouter_une_vente' => array('ajout-24.png', 'edit_vente'),
		) );
		debut_cadre_association('ventes.gif', 'toutes_les_ventes');
		// FILTRES
		filtres_association(array(
			'annee' => array($annee, 'asso_ventes', 'vente'),
			'id' => $id_vente,
		), 'ventes');
		//TABLEAU
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_ventes'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_date') .'</th>';
		echo '<th>'. _T('asso:entete_intitule') .'</th>';
		echo '<th>'. _T('asso:entete_code') .'</th>';
		echo '<th>'. _T('asso:entete_nom') .'</th>';
		echo '<th>'. _T('asso:entete_quantite') . '</th>';
		echo '<th>'. _T('asso:entete_montant') .'</th>';
		echo '<th colspan="2" class="actions">'._T('asso:entete_action').'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_ventes', "DATE_FORMAT(date_vente, '%Y')=$annee", '',  'id_vente DESC') ;
		while ($data = sql_fetch($query)) {
			echo '<tr class="'. ($data['date_envoi']<$data['date_vente']?'pair':'impair') . (($id_vente==$data['id_vente'])?' surligne':'') .'" id="'.$data['id_vente'].'">';
			echo '<td class="integer">'.$data['id_vente'].'</td>';
			echo '<td class="date">'. association_formater_date($data['date_vente'],'dtstart') .'</td>';
			echo '<td class="text">'
				. (test_plugin_actif('CATALOGUE') && (intval($data['article'])==$data['article'])
					? association_calculer_lien_nomid('',$data['article'],'article')
					: propre($data['article'])
				) .'</td>';
			echo '<td class="texte">'.$data['code'].'</td>';
			echo '<td class="text">'. association_calculer_lien_nomid($data['acheteur'],$data['id_acheteur']) .'</td>';
			echo '<td class="decimal">'.$data['quantite'].'</td>';
			echo '<td class="decimal">'
			. association_formater_prix($data['quantite']*$data['prix_vente']).'</td>';
			echo association_bouton_supprimer('vente', 'id='.$data['id_vente'], 'td');
			echo '<td class="action">'. association_bouton_faire('mettre_a_jour_la_vente', 'edit-12.gif', 'edit_vente','id='.$data['id_vente']) . '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>