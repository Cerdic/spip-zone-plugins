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
		$url_edit_vente=generer_url_ecrire('edit_vente','agir=modifie');
		$url_ajout_vente=generer_url_ecrire('edit_vente','agir=ajoute');
		
		$annee=intval(_request('annee'));
		if(!$annee) $annee = date('Y');
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo '<p>', _L('En rose : Vente enregistr&eacute;e<br />En bleu : Vente exp&eacute;di&eacute;e') . '</p>'; 
		
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
			echo '<td><strong style="color: #9F1C30">' . _L('Solde :') . '</strong></td>';
			echo '<td class="impair" style="text-align:right;">'.association_nbrefr($solde).' &euro;</td>';
			echo '</tr>';
			echo '</table>';
		}		
		echo fin_boite_info(true);
		
	
		$res=association_icone(_T('Ajouter une vente'),  $url_ajout_vente, 'ajout_don.png');
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('Toutes les ventes'));
		
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
		echo '<form action="'.$url_agir_ventes.'" method="POST">';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td style="text-align:right"><strong>' . _L('ID') . '</strong></td>';
		echo '<td style="text-align:right"><strong>' . _L('Date') . '</strong></td>';
		echo '<td><strong>' . _L('Article') . '</strong></td>';
		echo '<td><strong>' . _L('Code') . '</strong></td>';
		echo '<td><strong>' . _L('Acheteur') . '</strong></td>';
		echo '<td><strong>' . _L('Membre') . '</strong></td>';
		echo '<td style="text-align:right"><strong>' . _L('Qt&eacute;') . '</strong></td>';
		echo '<td style="text-align:right"><strong>' . _L('Montant') . '</strong></td>';
		echo '<td colspan="2" style="text-align:center"><strong>&nbsp;</strong></td>';
		echo '</tr>';
		$ventes = '';
		$query = sql_select('*', 'spip_asso_ventes', "date_format( date_vente, '%Y' )=$annee", '',  "id_vente DESC") ;
		while ($data = sql_fetch($query)) {
			if(isset($data['date_envoi'])) { $class= "pair"; }
			else {$class="impair";}   
			$ventes .= '<tr> ';
			$ventes .= '<td class="'.$class. ' border1" style="text-align:right">'.$data['id_vente'].'</td>';
			$ventes .= '<td class="'.$class. ' border1" style="text-align:right">'.association_datefr($data['date_vente']).'</td>';
			$ventes .= '<td class="'.$class. ' border1">'.$data['article'].'</td>';
			$ventes .= '<td class="'.$class. ' border1">'.$data['code'].'</td>';
			$ventes .= '<td class="'.$class. ' border1">'.$data['acheteur'].'</td>';
			$ventes .= '<td class="'.$class. ' border1">'.$data['id_acheteur'].'</td>';
			$ventes .= '<td class="'.$class. ' border1" style="text-align:right">'.$data['quantite'].'</td>';
			$ventes .= '<td class="'.$class. ' border1" style="text-align:right">'.association_nbrefr($data['quantite']*$data['prix_vente']).'</td>';
			$ventes .= '<td class="'.$class. ' border1" style="text-align:center"><a href="'.$url_edit_vente.'&id='.$data['id_vente'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="' . _L('Mettre &agrave; jour la vente') . '"></a>';
			$ventes .= '<td class="'.$class. ' border1" style="text-align:center"><input name="delete[]" type="checkbox" value='.$data['id_vente'].'></td>';
			$ventes .= '</tr>';
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
