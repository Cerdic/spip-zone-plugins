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
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function exec_ventes()
{
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$annee = intval(_request('annee'));
		if(!$annee)
			$annee = date('Y');
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_ventes'));
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		// INTRO : nom du module et annee affichee
		echo totauxinfos_intro('','ventes',$annee);
		// STATS sur les paniers/achats/commandes
		echo totauxinfos_stats('paniers/commandes', 'ventes', array('entete_quantite'=>'quantite','entete_montant'=>'prix_vente*quantite',), "DATE_FORMAT(date_vente, '%Y')=$annee");
		// TOTAUX : nombre de ventes selon etat de livraison
		$liste_libelles = array('pair'=>'ventes_enregistrees', 'impair'=>'ventes_expediees', );
		$liste_effectifs['pair'] = sql_countsel('spip_asso_ventes', "date_envoi<date_vente AND  DATE_FORMAT(date_vente, '%Y')=$annee ");
		$liste_effectifs['impair'] = sql_countsel('spip_asso_ventes', "date_envoi>=date_vente AND  DATE_FORMAT(date_vente, '%Y')=$annee ");
		echo totauxinfos_effectifs('ventes', $liste_libelles, $liste_effectifs);
		// TOTAUX : montants des ventes et des frais de port
/* Il est interessant d'interroger le livre comptable pour des cas complexes et si on sait recuperer les achats-depenses liees aux ventes(c'est faisable s'ils ne concerne qu'un ou deux comptes) ; mais ici, les montant etant dupliques dans la table des ventes autant faire simple...
		$data1 = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT(date, '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_ventes']) );
		$data2 = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT(date, '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_frais_envoi']) );
		echo totauxinfos_montants($annee, $data1['somme_recettes']-$data1['somme_depenses']+$data2['somme_recettes']-$data2['somme_depenses']);
*/
		$data = sql_fetsel('SUM(prix_vente*quantite) AS somme_ventes, SUM(frais_envoi) AS somme_frais', 'spip_asso_ventes', "DATE_FORMAT(date_vente, '%Y')=$annee" );
		echo totauxinfos_montants($annee, $data['somme_ventes']+$data['somme_frais'], $data['somme_frais']); // les frais de port etant facturees a l'acheteur, ce sont bien des recettes... mais ces frais n'etant (normalement) pas refacturees (et devant meme etre transparents) ils n'entrent pas dans la marge (enfin, facon de dire car les couts d'acquisition ne sont pas pris en compte... le "solde" ici est le montant effectif des ventes.)
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone(_T('asso:ajouter_une_vente'),  generer_url_ecrire('edit_vente'), 'ajout_don.png');
		echo bloc_des_raccourcis($res);
		echo debut_droite('',true);
		debut_cadre_relief('', false, '', $titre = _T('asso:toutes_les_ventes'));
		// PAGINATION ET FILTRES
		echo "\n<table><tr><td>";
		$query = sql_select("DATE_FORMAT(date_vente, '%Y')  AS annee", 'spip_asso_ventes', '', 'annee', 'annee');
		while ($data = sql_fetch($query)) {
			$a = $data['annee'];
			if ($a==$annee)	{echo ' <strong>'.$a.'</strong>';}
			else {echo ' <a href="'. generer_url_ecrire('ventes','annee='.$a).'">'.$a.'</a>';}
		}
		echo "</td></tr></table>\n";
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
			$class = ' class="'. ($data['date_envoi']<$data['date_vente'] ?'pair':'impair') . '"';
			$id = $data['id_vente'];
			echo "<tr$class>";
			echo '<td class="integer">'.$id.'</td>';
			echo '<td class="date">'. association_datefr($data['date_vente'],'dtstart') .'</td>';
			echo '<td class="text">'. (test_plugin_actif('CATALOGUE') && (intval($data['article'])==$data['article']) ? association_calculer_lien_nomid('',$data['article'],'article') : propre($data['article']) ) .'</td>';
			echo '<td class="texte">'.$data['code'].'</td>';
			echo '<td class="text">'. association_calculer_lien_nomid($data['acheteur'],$data['id_acheteur']) .'</td>';
			echo '<td class="decimal">'.$data['quantite'].'</td>';
			echo '<td class="decimal">'
			. association_prixfr($data['quantite']*$data['prix_vente']).'</td>';
			echo '<td class="actions">'
			. association_bouton('mettre_a_jour_la_vente', 'edit-12.gif', 'edit_vente',"id=$id") . '</td>';
			echo '<td class="actions"><input name="delete[]" type="checkbox" value="'.$id.'" /></td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		echo generer_form_ecrire('action_ventes', $corps, '', _T('asso:bouton_supprimer'));
		echo fin_cadre_relief();
		echo fin_page_association();
	}
}

?>