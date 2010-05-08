<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_ventes(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_asso = generer_url_ecrire('association');
		$url_agir_ventes = generer_url_ecrire('agir_ventes');
		$url_ajout_vente=generer_url_ecrire('edit_vente','agir=ajoute');
		
		$annee=intval(_request('annee'));
		if(!$annee) $annee = date('Y');
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo '<p>', _T('asso:en_rose_vente_enregistree_en_bleu_vente_expediee') . '</p>'; 
		
		// TOTAUX
		$critere = lire_config('association/pc_ventes');
		if ($critere) $critere = " AND imputation=". sql_quote($critere);
		$query = sql_select('imputation, sum(recette) AS somme_recettes, sum(depense) AS somme_depenses', 'spip_asso_comptes', "date_format( date, '%Y' ) =$annee$critere");
		while ($data = sql_fetch($query)) {
			$solde= $data['somme_depenses'] + $data['somme_recettes'];
			$imputation = $data['imputation'];
			echo '<table width="100%">';
			echo '<tr>';
			echo '<td colspan="2"><strong>Totaux '.$imputation.' '.$annee.' :</strong></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><strong style="color: #9F1C30">' . _T('asso:solde') . '</strong></td>';
			echo '<td class="impair" style="text-align:right;">'.association_nbrefr($solde).' &euro;</td>';
			echo '</tr>';
			echo '</table>';
		}		
		echo fin_boite_info(true);
		
	
		$res=association_icone(_T('asso:ajouter_une_vente'),  $url_ajout_vente, 'ajout_don.png');
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:toutes_les_ventes'));
		
		// PAGINATION ET FILTRES
		echo '<table>';
		echo '<tr>';
		echo '<td>';
		
		$query = sql_select("date_format( date_vente, '%Y' )  AS annee", "spip_asso_ventes", "", "annee", "annee");
		while ($data = sql_fetch($query)) {
			$a = $data['annee'];
			if ($a==$annee)	{echo ' <strong>'.$a.'</strong>';}
			else {echo ' <a href="'. generer_url_ecrire('ventes','annee='.$a).'">'.$a.'</a>';}
		}
		echo '</td>';
		echo '</table>';
		
		//TABLEAU
		echo '<form action="'.$url_agir_ventes.'" method="post">';
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td style="text-align:right"><strong>' . _T('asso:id') . '</strong></td>';
		echo '<td style="text-align:right"><strong>' . _T('asso:date') . '</strong></td>';
		echo '<td><strong>' . _T('asso:article') . '</strong></td>';
		echo '<td><strong>' . _T('asso:code') . '</strong></td>';
		echo '<td><strong>' . _T('asso:acheteur') . '</strong></td>';
		echo '<td><strong>' . _T('asso:membre') . '</strong></td>';
		echo '<td style="text-align:right"><strong>' . _T('asso:qte') . '</strong></td>';
		echo '<td style="text-align:right"><strong>' . _T('asso:montant') . '</strong></td>';
		echo '<td colspan="2" style="text-align:center"><strong>&nbsp;</strong></td>';
		echo '</tr>';
		$ventes = '';
		$query = sql_select('*', 'spip_asso_ventes', "date_format( date_vente, '%Y' )=$annee", '',  "id_vente DESC") ;
		while ($data = sql_fetch($query)) {
			$class = " class='border1 " . ($data['date_envoi'] ? "pair" : "impair") . "'";
			$id = $data['id_vente'];
			$q = $data['quantite'];
			$ventes .= '<tr> '
			. "\n<td$class style='text-align:right'>".$id.'</td>'
			. "\n<td$class style='text-align:right'>"
			. association_datefr($data['date_vente']).'</td>'
			. "\n<td$class>".$data['article'].'</td>'
			. "\n<td$class>".$data['code'].'</td>'
			. "\n<td$class>".$data['acheteur'].'</td>'
			. "\n<td$class>".$data['id_acheteur'].'</td>'
			. "\n<td$class style='text-align:right'>".$q.'</td>'
			. "\n<td$class style='text-align:right'>"
			. association_nbrefr($q*$data['prix_vente']).'</td>'
			. "\n<td$class style='text-align:center'><a href='"
			. generer_url_ecrire('edit_vente',"agir=modifie&id=$id")
			. "'><img src='"._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif\' title="' . _T('asso:mettre_a_jour_la_vente') . '"></a>'
			."\n<td$class style='text-align:center'><input name='delete[]' type='checkbox' value='$id' /></td>"
			.'</tr>';
		}     
		echo $ventes, '</table>';
		
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td  style="text-align:right;">';
		echo !$ventes ? '' : ('<input type="submit" value="'._T('asso:bouton_supprimer').'" class="fondo">');
		echo '</table>';
		echo '</form>';
		
		fin_cadre_relief();  
		 echo fin_gauche(),fin_page(); 
	}
}
?>
