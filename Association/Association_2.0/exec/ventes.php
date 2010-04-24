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
			exit;
		}
		
		$url_asso = generer_url_ecrire('association');
		$url_ventes = generer_url_ecrire('ventes');
		$url_agir_ventes = generer_url_ecrire('agir_ventes');
		$url_edit_vente=generer_url_ecrire('edit_vente','agir=modifie');
		$url_ajout_vente=generer_url_ecrire('edit_vente','agir=ajoute');
		
		$annee=$_GET['annee'];
		if(empty($annee)){$annee = date('Y');}
		
		  $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo '<p>En rose : Vente enregistr&eacute;e<br />En bleu : Vente exp&eacute;di&eacute;e</p>'; 
		
		// TOTAUX
		$query = spip_query( "SELECT sum(recette) AS somme_recettes, sum(depense) AS somme_depenses FROM spip_asso_comptes WHERE date_format( date, '%Y' ) = $annee AND imputation ='".lire_config('association/pc_ventes')."' ");
		while ($data = spip_fetch_array($query)) {
			$somme_recettes = $data['somme_recettes'];
			$somme_depenses = $data['somme_depenses'];
			$solde= $somme_recettes - $somme_depenses;
			
			echo '<table width="100%">';
			echo '<tr>';
			echo '<td colspan="2"><strong>Totaux '.$imputation.' '.$annee.' :</strong></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><font color="#9F1C30"><strong>Solde :</strong></td>';
			echo '<td class="impair" style="text-align:right;">'.association_nbrefr($solde).' &euro;</td>';
			echo '</tr>';
			echo '</table>';
		}		
		echo fin_boite_info(true);
		
	
		$res=icone_horizontale(_T('Ajouter une vente'), $url_ajout_vente, _DIR_PLUGIN_ASSOCIATION_ICONES.'ajout_don.png','rien.gif',false);
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('Toutes les ventes'));
		
		// PAGINATION ET FILTRES
		echo '<table>';
		echo '<tr>';
		echo '<td>';
		
		$query = spip_query ("SELECT date_format( date_vente, '%Y' )  AS annee FROM spip_asso_ventes GROUP BY annee ORDER BY annee");
		while ($data = spip_fetch_array($query)) {
			if ($data['annee']==$annee)	{echo ' <strong>'.$data['annee'].'</strong>';}
			else {echo ' <a href="'.$url_ventes.'&annee='.$data['annee'].'">'.$data['annee'].'</a>';}
		}
		echo '</td>';
		echo '</table>';
		
		//TABLEAU
		echo '<form action="'.$url_agir_ventes.'" method="POST">';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td style="text-align:right"><strong>ID</strong></td>';
		echo '<td style="text-align:right"><strong>Date</strong></td>';
		echo '<td><strong>Article</strong></td>';
		echo '<td><strong>Code</strong></td>';
		echo '<td><strong>Acheteur</strong></td>';
		echo '<td><strong>Membre</strong></td>';
		echo '<td style="text-align:right"><strong>Qt&eacute;</strong></td>';
		echo '<td style="text-align:right"><strong>Montant</strong></td>';
		echo '<td colspan="2" style="text-align:center"><strong>&nbsp;</strong></td>';
		echo '</tr>';
		
		$query = spip_query ("SELECT * FROM spip_asso_ventes WHERE date_format( date_vente, '%Y' ) = '$annee'  ORDER by id_vente DESC") ;
		while ($data = spip_fetch_array($query)) {
			if(isset($data['date_envoi'])) { $class= "pair"; }
			else {$class="impair";}   
			echo '<tr> ';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">'.$data['id_vente'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">'.association_datefr($data['date_vente']).'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['article'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['code'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['acheteur'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['id_acheteur'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">'.$data['quantite'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">'.association_nbrefr($data['quantite']*$data['prix_vente']).'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_edit_vente.'&id='.$data['id_vente'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="Mettre &agrave; jour la vente"></a>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:center"><input name="delete[]" type="checkbox" value='.$data['id_vente'].'></td>';
			echo '</tr>';
		}     
		echo '</table>';
		
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td  style="text-align:right;">';
		echo '<input type="submit" name="Submit" value="'._T('asso:bouton_supprimer').'" class="fondo">';
		echo '</table>';
		echo '</form>';
		
		fin_cadre_relief();  
		 echo fin_gauche(),fin_page(); 
	}
?>
