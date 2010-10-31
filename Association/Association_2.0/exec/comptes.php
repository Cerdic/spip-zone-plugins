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
	
function exec_comptes() {

	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();

	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		//debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		
		$url_comptes = generer_url_ecrire('comptes');
		
		if ( isset ($_REQUEST['imputation'] )) { $imputation = $_REQUEST['imputation']; }
		else { $imputation= "%"; }
		
		$annee= intval(_request('annee'));
		if(empty($annee)){$annee = date('Y');}
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo '<p>', _T('asso:en_bleu_recettes_en_rose_depenses'), '</p>'; 
		
		// TOTAUX
		$query = sql_select("sum(recette) AS somme_recettes, sum(depense) AS somme_depenses", 'spip_asso_comptes', "date_format( date, '%Y' ) = $annee AND imputation like '$imputation'");
		while ($data = sql_fetch($query)) {
			$somme_recettes = $data['somme_recettes'];
			$somme_depenses = $data['somme_depenses'];
			$solde= $somme_recettes - $somme_depenses;
			
			echo '<table width="100%">';
			echo '<tr>';
			echo '<td colspan="2"><strong>' . _L('Totaux '.$imputation.' '.$annee).' :</strong></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><strong style="color:blue;">'. _T('asso:entrees') . '</strong></td>';
			echo '<td style="text-align:right;">'.association_nbrefr($somme_recettes).' &euro; </td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><strong style="color:blue;">' . _T('asso:sorties') . '</strong></td>';
			echo '<td style="text-align:right;">'.association_nbrefr($somme_depenses).' &euro;</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td><strong style="color: #9F1C30;">' . _T('asso:solde') . '</strong></td>';
			echo '<td class="impair" style="text-align:right;">'.association_nbrefr($solde).' &euro;</td>';
			echo '</tr>';
			echo '</table>';
		}
		
		echo fin_boite_info(true);	
		
		$url_bilan = generer_url_ecrire('bilan', "annee=$annee");		
		$res = association_icone(_T('Bilan') . " $annee",  $url_bilan, 'finances.jpg')
		. association_icone(_T('asso:ajouter_une_operation'),  generer_url_ecrire('edit_compte'), 'ajout_don.png');

		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:informations_comptables'));
		
		echo "\n<table width='100%'>";
		
		// FILTRES
		echo '<tr>';
		echo '<td>';
		
		$query = sql_select("date_format( date, '%Y' )  AS annee", "spip_asso_comptes", "imputation like '$imputation' ", "annee", "annee");
		
		while ($data = sql_fetch($query)) {
			if ($data['annee']==$annee)	{echo ' <strong>'.$data['annee'].' </strong>';}
			else {echo '<a href="'. generer_url_ecrire('comptes','annee='.$data['annee'].'&imputation='.$imputation).'">'.$data['annee'].'</a> ';}
		}
		echo '</td>';
		
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="'.$url_comptes.'"><div>';
		echo '<select name ="imputation" class="fondl" onchange="form.submit()">';
		echo '<option value="%" ';
		if ($imputation=="%") { echo ' selected="selected"'; }
		echo '>Tous</option>';
		$sql = sqL_select('*', 'spip_asso_plan','', '', "classe,code");
		while ($plan = sql_fetch($sql)) {
			echo '<option value="'.$plan['code'].'" ';
			if ($imputation==$plan['code']) { echo ' selected="selected"'; }
			echo '>'.$plan['classe'].' - '.$plan['intitule'].'</option>';
		}
		echo '</select></div></form></td>';
		echo '</tr></table>';

	//TABLEAU
	$max_par_page=30;
	$debut= intval(_request('debut'));
	$auteurs = comptes_while($annee, $imputation, $debut, $max_par_page);

	if ($auteurs) {

	$table = "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	. "<tr style='background-color: #DBE1C5;'>\n"
	. '<th style="text-align: right;">' . _T('asso:id'). "</th>\n"
	. '<th style="text-align: right;">' . _T('asso:date') . "</th>\n"
	. '<th>' . _T('asso:compte') . "</th>\n"
	. '<th>' . _T('asso:justification') . "</th>\n"
	. '<th style="text-align: right;">' . _T('asso:montant') . "</th>\n"
	. '<th>' . _T('asso:financier') . "</th>\n"
	. '<td colspan="3" style="text-align: center;"><strong>&nbsp;</strong></td>'
	. '</tr>'
	. $auteurs
	. "</table>\n";

	//SOUS-PAGINATION

	$nombre_selection=sql_countsel('spip_asso_comptes', "date_format( date, '%Y' ) = $annee AND imputation like '$imputation'");
	$pages=intval($nombre_selection/$max_par_page) + 1;
	$nav = '';
	if ($pages != 1) { 
		for ($i=0;$i<$pages;$i++) { 
			$position= $i * $max_par_page;
			if ($position == $debut)
			  { $nav .= '<strong>'.$position.' </strong>'; }
			else { $nav .= '<a href="'.generer_url_ecrire('comptes','annee='.$annee.'&imputation='.$imputation.'&debut='.$position).'">'.$position."</a>\n"; }
		}	
	}

	$table2 = "\n<table width='100%'><tr>\n<td>" . $nav . '</td><td style="text-align:right;"><input type="submit" value="' . _L('Valider') . '" class="fondo" /></td></tr></table>';

	echo generer_form_ecrire('action_comptes', $table . $table2);
	fin_cadre_relief();  
	echo fin_page_association(); 
	}
	}
}

function comptes_while($annee, $imputation, $debut, $max_par_page)
{
	$query = sql_select('*', "spip_asso_comptes", "date_format( date, '%Y' ) = $annee AND imputation like '$imputation'", '',  'date DESC', "$debut,$max_par_page");
	$auteurs = '';

	while ($data = sql_fetch($query)) {
		if ($data['recette'] >0) { $class= "pair";}
		else { $class="impair";}	   
		$id = $data['id_compte'];
		$valide = (isset($data['valide']) AND ($data['valide']=='oui'));
		$auteurs .= "\n<tr>"
		. '<td class="'
		. $class. ' border1" style="text-align:right;">'
		. $id
		. "</td>\n<td class=\""
		. $class. ' border1" style="text-align:right;">'
		. association_datefr($data['date'])
		. "</td>\n<td class=\""
		. $class. ' border1">'
		. $data['imputation']
		. "</td>\n<td class=\""
		. $class. ' border1">'
		. propre($data['justification'])
		. "</td>\n<td class=\""
		. $class. ' border1" style="text-align:right;">'
		. association_nbrefr($data['recette']-$data['depense'])
		. "</td>\n<td class=\""
		. $class. ' border1">'
		. $data['journal']
		. '</td>'
		. ($valide ?
			("<td class='$class' colspan='3' style='border-top: 1px solid #CCCCCC;'>&nbsp;</td>\n")
		   :  ("<td class='$class border1' style='text-align: center;'>" . association_bouton(_T('asso:mettre_a_jour'), 'edit-12.gif', 'edit_compte', 'id='.$id) . "</td>\n"
			. "<td class='$class border1' style='text-align: center;'>" . association_bouton(_T('asso:supprimer'), 'poubelle.gif', 'action_comptes', 'id='.$id) . "</td>\n"
		       . "<td class='$class border1' style='text-align: center;'><input name='valide[]' type='checkbox' value='".$data['id_compte']. "' /></td>\n"))
		 . '</tr>';
	}
	return $auteurs;
}
?>
