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

function exec_comptes() {
	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	debut_page(_T('Gestion pour  Association'), "", "");

	$url_comptes = generer_url_ecrire('comptes');
	$url_ajout_compte = generer_url_ecrire('ajout_compte');
	$url_edit_compte = generer_url_ecrire('edit_compte');
	$url_action_comptes = generer_url_ecrire('action_comptes');
	$url_bilan = generer_url_ecrire('bilan');

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('Informations comptables'));
	debut_boite_info();

	print association_date_du_jour();

	echo '<table width="70%">';

	// FILTRES
	echo '<tr>';
	echo '<td>';

	if ( isset ($_REQUEST['imputation'] )) {
		$imputation = $_REQUEST['imputation']; 
	}
	else { $imputation= "%"; }

	$annee=$_GET['annee'];
	if(empty($annee)){$annee = date('Y');}

	global $table_prefix;
	$query = spip_query ("SELECT date_format( date, '%Y' )  AS annee FROM ".$table_prefix."_asso_comptes WHERE imputation like '$imputation' GROUP BY annee ORDER by annee");

	while ($data = spip_fetch_array($query)) {
		if ($data['annee']==$annee)	{echo ' <strong>'.$data['annee'].' </strong>';}
		else {echo '<a href="'.$url_comptes.'&annee='.$data['annee'].'&imputation='.$imputation.'">'.$data['annee'].'</a> ';}
	}
	echo '</td>';

	echo '<td style="text-align:right;">';	
	//icone_horizontale(_T('Bilan'), $url_bilan, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/finances.jpg','rien.gif' ); 
	echo '<a href="'.$url_bilan.'">Bilans</a>';
	echo '</td>';
	
	echo '<td style="text-align:right;">';
	echo '<form method="post" action="'.$url_comptes.'">';
	echo '<select name ="imputation" class="fondl" onchange="form.submit()">';
	echo '<option value="%"';
	if ($imputation=="%") {echo ' selected="selected"';}
	echo '> Tous</option>';
	echo '<option value="cotisation"';
	if ($imputation=="cotisation") {echo ' selected="selected"';}
	echo '> Cotisations</option>';
	echo '<option value="vente"';
	if ($imputation=="vente") {echo ' selected="selected"';}
	echo '> Ventes</option>';
	echo '<option value="activite"';
	if ($imputation=="activite") {echo ' selected="selected"';}
	echo '> Activit&eacute;s</option>';
	echo '<option value="achat"';
	if ($imputation=="achat") {echo ' selected="selected"';}
	echo '> Achats</option>';
	echo '<option value="don"';
	if ($imputation=="don") {echo ' selected="selected"';}
	echo '> Dons</option>';
	echo '<option value="divers"';
	if ($imputation=="divers") {echo ' selected="selected"';}
	echo '> Divers</option>';
	echo '</select>';
	echo '</form>';
	echo '</table>';

	//TABLEAU
	echo '<form method="post" action="'.$url_action_comptes.'">';
	echo '<table width="70%">';
	echo '<tr bgcolor="silver">';
	echo '<td style="text-align:right;"><strong>ID</strong></td>';
	echo '<td style="text-align:right;"><strong>Date</strong></td>';
	echo '<td style="text-align:right;"><strong>Recette</strong></td>';
	echo '<td style="text-align:right;"><strong>D&eacute;pense</strong></td>';
	echo '<td><strong>Livre</strong></td>';
	echo '<td><strong>Justification</strong></td>';
	echo '<td><strong>Journal</strong></td>';
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
		echo '<td class ='.$class.' style="text-align:right;">'.$data['id_compte'].'</td>';
		echo '<td class ='.$class.' style="text-align:right;">'.association_datefr($data['date']).'</td>';
		echo '<td class ='.$class.' style="text-align:right;">'.number_format($data['recette'], 2, ',', ' ').'</td>';
		echo '<td class ='.$class.' style="text-align:right;">'.number_format($data['depense'], 2, ',', ' ').'</td>';
		echo '<td class ='.$class.'>'.$data['imputation'].'</td>';
		echo '<td class ='.$class.'>'.$data['justification'].'</td>';
		echo '<td class ='.$class.'>'.$data['journal'].'</td>';
		if($data['valide']=='oui') {echo '<td class ='.$class.' colspan=3>&nbsp;</td>';}
		else {
			echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_compte.'&id='.$data['id_compte'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Mettre &agrave; jour"></a></td>';
			echo '<td class ='.$class.' style="text-align:center;"><a href="'.$url_action_comptes.'&action=supprime&id='.$data['id_compte'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class ='.$class.'><input name="valide[]" type="checkbox" value='.$data['id_compte'].'></td>';
		}
		echo '</tr>';
	}
	echo '</table>';

	echo '<table width="70%">';
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

	echo '<p>En bleu : Recettes - En rose : D&eacute;penses</p>'; 
	echo '<p>';
	icone(_T('Ajouter une op&eacute;ration'), $url_ajout_compte, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','creer.gif' );
	echo '</p>';

	// TOTAUX
	$query = spip_query( "SELECT sum(recette) AS somme_recettes, sum(depense) AS somme_depenses FROM ".$table_prefix."_asso_comptes WHERE date_format( date, '%Y' ) = $annee AND imputation like '$imputation' ");
	while ($data = spip_fetch_array($query)) {
		$somme_recettes = $data['somme_recettes'];
		$somme_depenses = $data['somme_depenses'];
		$solde= $somme_recettes - $somme_depenses;
		
		echo '<table border="0">';
		echo '<tr>';
		echo '<td><font color="blue"><strong>Total des entr&eacute;es :&nbsp;</td>';
		echo '<td style="text-align:right;">'.number_format($somme_recettes, 2, ',', ' ').' &euro; </td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><font color="blue"><strong>Total des sorties :&nbsp;</td>';
		echo '<td style="text-align:right;">'.number_format($somme_depenses, 2, ',', ' ').' &euro;</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><font color="#9F1C30"><strong>Solde "'.$imputation.'" '.$annee.' : </td>';
		echo '<td class="impair" style="text-align:right;">'.number_format($solde, 2, ',', ' ').' &euro;</td>';
		echo '</tr>';
		echo '</table>';
		echo '<br />';
	}

	fin_boite_info();
	fin_cadre_relief();  
	fin_page();
}
?>

