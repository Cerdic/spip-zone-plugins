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
	
	function exec_comptes() {
		global $connect_statut, $connect_toutes_rubriques, $table_prefix;
		
		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
		
		debut_page(_T('Gestion pour  Association'), "", "");
		
		$url_comptes = generer_url_ecrire('comptes');
		$url_ajout_compte = generer_url_ecrire('edit_compte','action=ajoute');
		$url_edit_compte = generer_url_ecrire('edit_compte','action=modifie');
		$url_action_comptes = generer_url_ecrire('action_comptes');
		$url_bilan = generer_url_ecrire('bilan');
		
		if ( isset ($_REQUEST['imputation'] )) { $imputation = $_REQUEST['imputation']; }
		else { $imputation= "%"; }
		
		$annee=$_GET['annee'];
		if(empty($annee)){$annee = date('Y');}
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		echo '<p>En bleu : Recettes<br />En rose : D&eacute;penses</p>'; 
		
		// TOTAUX
		$query = spip_query( "SELECT sum(recette) AS somme_recettes, sum(depense) AS somme_depenses FROM ".$table_prefix."_asso_comptes 		WHERE date_format( date, '%Y' ) = $annee AND imputation like '$imputation' ");
		while ($data = spip_fetch_array($query)) {
			$somme_recettes = $data['somme_recettes'];
			$somme_depenses = $data['somme_depenses'];
			$solde= $somme_recettes - $somme_depenses;
			
			echo '<table width="100%">';
			echo '<tr>';
			echo '<td colspan="2"><strong>Totaux '.$imputation.' '.$annee.' :</strong></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><font color="blue"><strong>Entr&eacute;es :</strong></td>';
			echo '<td style="text-align:right;">'.number_format($somme_recettes, 2, ',', ' ').' &euro; </td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><font color="blue"><strong>Sorties :</strong></td>';
			echo '<td style="text-align:right;">'.number_format($somme_depenses, 2, ',', ' ').' &euro;</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><font color="#9F1C30"><strong>Solde :</strong></td>';
			echo '<td class="impair" style="text-align:right;">'.number_format($solde, 2, ',', ' ').' &euro;</td>';
			echo '</tr>';
			echo '</table>';
		}
		
		fin_boite_info();	
		
		debut_raccourcis();
		echo '<p>';
		icone_horizontale(_T('Bilan'), $url_bilan, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/finances.jpg','rien.gif');
		icone_horizontale(_T('Ajouter une op&eacute;ration'), $url_ajout_compte, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','creer.gif' );
		echo '</p>';
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Informations comptables'));
		
		echo '<table width="100%">';
		
		// FILTRES
		echo '<tr>';
		echo '<td>';
		
		$query = spip_query ("SELECT date_format( date, '%Y' )  AS annee FROM spip_asso_comptes WHERE imputation like '$imputation' GROUP BY annee ORDER by annee");
		
		while ($data = spip_fetch_array($query)) {
			if ($data['annee']==$annee)	{echo ' <strong>'.$data['annee'].' </strong>';}
			else {echo '<a href="'.$url_comptes.'&annee='.$data['annee'].'&imputation='.$imputation.'">'.$data['annee'].'</a> ';}
		}
		echo '</td>';
		
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="'.$url_comptes.'">';
		echo '<select name ="imputation" class="fondl" onchange="form.submit()">';
		echo '<option value="%" ';
		if ($imputation=="%") { echo ' selected="selected"'; }
		echo '>Tous</option>';
		$sql = spip_query ("SELECT * FROM spip_asso_plan ORDER BY classe,code");
		while ($plan = spip_fetch_array($sql)) {
			echo '<option value="'.$plan['code'].'" ';
			if ($imputation==$plan['code']) { echo ' selected="selected"'; }
			echo '>'.$plan['classe'].' - '.$plan['intitule'].'</option>';
		}
		echo '</select></td>';
		echo '</form>';
		echo '</tr></table>';

	//TABLEAU
	echo '<form method="post" action="'.$url_action_comptes.'">';
	echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
	echo '<tr bgcolor="#DBE1C5">';
	echo '<td style="text-align:right;"><strong>ID</strong></td>';
	echo '<td style="text-align:right;"><strong>Date</strong></td>';
	echo '<td><strong>Compte</strong></td>';
	echo '<td><strong>Justification</strong></td>';
	echo '<td style="text-align:right;"><strong>Recette</strong></td>';
	echo '<td style="text-align:right;"><strong>D&eacute;pense</strong></td>';
		echo '<td><strong>Financier</strong></td>';
	echo '<td colspan="3" style="text-align:center;"><strong>Action</strong></td>';
	echo '</tr>';

	$max_par_page=30;
	$debut=$_GET['debut'];

	if (empty($debut)) {$debut=0;}

	$query = spip_query ("SELECT * FROM ".$table_prefix."_asso_comptes WHERE date_format( date, '%Y' ) = $annee AND imputation like '$imputation' ORDER BY date DESC LIMIT $debut,$max_par_page");

	while ($data = spip_fetch_array($query)) {
		if ($data['recette'] >0) { $class= "pair";}
		else { $class="impair";}	   
		
		$somme_recettes += $data['recette'];
		$somme_depenses += $data['depense'];
		
		echo '<tr> ';
		echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_compte'].'</td>';
		echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($data['date']).'</td>';
		echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['imputation'].'</td>';
		echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['justification'].'</td>';
		echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($data['recette'], 2, ',', ' ').'</td>';
		echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($data['depense'], 2, ',', ' ').'</td>';
		echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['journal'].'</td>';
		if($data['valide']=='oui') {echo '<td class ='.$class.' colspan=3 style="border-top: 1px solid #CCCCCC;">&nbsp;</td>';}
		else {
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_edit_compte.'&id='.$data['id_compte'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Mettre &agrave; jour"></a></td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_action_comptes.'&action=supprime&id='.$data['id_compte'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;;text-align:center"><input name="valide[]" type="checkbox" value='.$data['id_compte'].'></td>';
		}
		echo '</tr>';
	}
	echo '</table>';

	echo '<table width="100%">';
	echo '<tr>';

	//SOUS-PAGINATION
	echo '<td>';
	$query = spip_query( "SELECT * FROM ".$table_prefix."_asso_comptes WHERE date_format( date, '%Y' ) = $annee AND imputation like '$imputation' ");
	$nombre_selection=spip_num_rows($query);
	$pages=intval($nombre_selection/$max_par_page) + 1;

	if ($pages == 1) { echo '';}
	else {
		for ($i=0;$i<$pages;$i++) { 
			$position= $i * $max_par_page;
			if ($position == $debut) 	{ echo '<strong>'.$position.' </strong>'; }
			else { echo '<a href="'.$url_comptes.'&annee='.$annee.'&debut='.$position.'&imputation='.$imputation.'">'.$position.'</a> '; }
		}	
	}
	echo '</td>';
	echo '<td  style="text-align:right;">';
	echo '<input type="submit" name="Submit" value="Valider" class="fondo">';
	echo '</td>';
	echo '</table>';
	echo '</form>';

	fin_cadre_relief();  
	fin_page();
}
?>

