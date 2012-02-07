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
		if(!$annee) $annee = date('Y');
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_ventes'));
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		echo '<p>', _T('asso:en_rose_vente_enregistree_en_bleu_vente_expediee') .'</p>';
		// TOTAUX
		$association_imputation = charger_fonction('association_imputation', 'inc');
		$critere = $association_imputation('pc_ventes');
		if ($critere) $critere .= ' AND ';
		$query = sql_select('imputation, sum(recette) AS somme_recettes, sum(depense) AS somme_depenses', 'spip_asso_comptes', $critere . "DATE_FORMAT(date, '%Y')=$annee", "imputation");
		while ($data = sql_fetch($query)) {
			$solde = $data['somme_depenses']+$data['somme_recettes'];
			$imputation = $data['imputation'];
			echo '<table width="100%">' .'<caption>'.
	  _T('asso:totaux_titre', array('titre'=>$annee)) .'</caption><tbody>';
			echo '<tr class="'.($solde>0?'impair':'pair').'">';
			echo '<th class="solde">'.  _T('asso:entete_solde') . '</th>';
			echo '<td class="decimal">'.association_prixfr($solde).'</td>';
			echo '</tr>';
			echo '</tbody></table>';
		}
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res=association_icone(_T('asso:ajouter_une_vente'),  generer_url_ecrire('edit_vente'), 'ajout_don.png');
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
		echo '<th>'. _T('asso:vente_entete_article') .'</th>';
		echo '<th>'. _T('asso:entete_code') .'</th>';
		echo '<th>'. _T('asso:vente_entete_acheteur') .'</th>';
		echo '<th>'. _T('asso:vente_entete_quantite') . '</th>';
		echo '<th>'. _T('asso:entete_montant') .'</th>';
		echo '<th colspan="2" class="actions">&nbsp;</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_ventes', "DATE_FORMAT(date_vente, '%Y')=$annee", '',  'id_vente DESC') ;
		while ($data = sql_fetch($query)) {
			$class = ' class="'. ($data['date_envoi']=='0000-00-00' ?'pair':'impair') . '"';
			$id = $data['id_vente'];
			echo "<tr$class'>";
			echo '<td class="integer">'.$id.'</td>';
			echo '<td class="date">'. association_datefr($data['date_vente'],'dtstart') .'</td>';
			echo '<td class="text">'.propre($data['article']).'</td>';
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