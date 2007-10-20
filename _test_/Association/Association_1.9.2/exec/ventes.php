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
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

	function exec_ventes(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$url_asso = generer_url_ecrire('association');
		$url_ventes = generer_url_ecrire('ventes');
		$url_action_ventes = generer_url_ecrire('action_ventes');
		$url_edit_vente=generer_url_ecrire('edit_vente','action=modifie');
		$url_ajout_vente=generer_url_ecrire('edit_vente','action=ajoute');
		
		debut_page(_T('Gestion pour  Association'), "", "");
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('Ajouter une vente'), $url_ajout_vente, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/panier_in.gif','rien.gif' );
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Toutes les ventes'));
		
		// PAGINATION ET FILTRES
		echo '<table>';
		echo '<tr>';
		echo '<td>';
		
		$annee=$_GET['annee'];
		if(empty($annee)){$annee = date('Y');}
		
		$query = spip_query ("SELECT date_format( date_vente, '%Y' )  AS annee FROM spip_asso_ventes GROUP BY annee ORDER BY annee");
		while ($data = spip_fetch_array($query)) {
			if ($data['annee']==$annee)	{echo ' <strong>'.$data['annee'].'</strong>';}
			else {echo ' <a href="'.$url_ventes.'&annee='.$data['annee'].'">'.$data['annee'].'</a>';}
		}
		echo '</td>';
		echo '</table>';
		
		//TABLEAU
		echo '<form action="'.$url_action_ventes.'" method="POST">';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td style="text-align:right"><strong>ID</strong></td>';
		echo '<td style="text-align:right"><strong>Date</strong></td>';
		echo '<td><strong>Article</strong></td>';
		echo '<td><strong>Code</strong></td>';
		echo '<td><strong>Acheteur</strong></td>';
		echo '<td style="text-align:right"><strong>Quantit&eacute;</strong></td>';
		echo '<td style="text-align:right"><strong>Date d\'envoi</strong></td>';
		echo '<td colspan="2" style="text-align:center"><strong>Action</strong></td>';
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
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">'.$data['quantite'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">'.association_datefr($data['date_envoi']).'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_edit_vente.'&id='.$data['id_vente'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Mettre &agrave; jour la vente"></a>';
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
		fin_page();
	}
?>
