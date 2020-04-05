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

function exec_dons() {
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'dons')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		
		
		echo bloc_des_raccourcis(association_icone(_T('asso:ajouter_un_don'), generer_url_ecrire('edit_don','agir=ajouter'), 'ajout_don.png'));

		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:tous_les_dons'));

		// PAGINATION ET FILTRES
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';

		$annee= intval(_request('annee'));
		if(empty($annee)){$annee = date('Y');}

		$query = sql_select("date_format( date_don, '%Y' )  AS annee", "spip_asso_dons", "", "annee", "annee" );

		while ($data = sql_fetch($query))
		   {
		 	if ($data['annee']==$annee)
			{echo "\n<strong>".$data['annee'].'</strong>';}
			else {echo ' <a href="'. generer_url_ecrire('dons', '&annee='.$data['annee']) .'">'.$data['annee']."</a>\n";}
			}
		echo '</td></tr>';
		echo '</table>';

		//TABLEAU
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>' . _T('asso:id') . "</th>\n";
		echo '<th>' . _T('asso:date') . "</th>\n";
		echo '<th>' . _T('asso:nom') . "</th>\n";
		echo '<th style="text-align:right;">' . _T('asso:argent') . "</th>\n";
		echo '<th>' . _T('asso:colis') . "</th>\n";
		echo '<th style="text-align:right;">' . _T('asso:valeur') . "</th>\n";
		echo '<th>' . _T('asso:contrepartie') . "</th>\n";
		echo '<th colspan="2">' . _T('asso:action') . "</th>\n";
		echo '</tr>';
		$query = sql_select('*', "spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don", 'C.imputation=' . sql_quote($GLOBALS['association_metas']['pc_dons']) . " AND date_format( date_don, '%Y' ) = '$annee'", '',  "id_don" ) ;
		$exec_dons = generer_url_ecrire('dons');
		while ($data = sql_fetch($query)) {
			$id_don = $data['id_don'];

			echo "<tr id='don$id_don' style='background-color: #EEEEEE;'>";
			echo "<td class='arial11 border1'>$id_don</td>\n";
			echo '<td class="arial11 border1">'.association_datefr($data['date_don'])."</td>\n";
			echo '<td class="arial11 border1">'.propre($data['bienfaiteur'])."</td>\n";
			echo '<td class="arial11 border1" style="text-align:right;">'.association_flottant($data['argent']).'&nbsp;&euro;</td>';
			echo '<td class="arial11 border1">'.$data['colis']."</td>\n";
			echo ($data['vu'] ?
				("<td class='$class' colspan='2' style='border-top: 1px solid #CCCCCC;'>&nbsp;</td>\n")
			      : ('<td class="arial11 border1" style="text-align:right;">'.association_flottant($data['valeur']).'&nbsp;&euro;</td>'
				 . '<td class="arial11 border1">'.propre($data['contrepartie'])."</td>\n"));

			echo '<td  class="arial11 border1" style="text-align:center;">' . association_bouton(_T('asso:supprimer_le_don'), 'poubelle-12.gif', 'action_dons', "id=$id_don") . "</td>\n";
			echo '<td class="arial11 border1" style="text-align:center;">' . association_bouton(_T('asso:mettre_a_jour_le_don'), 'edit-12.gif', 'edit_don', "agir=modifier&id=$id_don") . "</td>\n";;
			echo '</tr>';
		}
		echo '</table>';
		
		fin_cadre_relief();  
		echo fin_page_association(); 
	}
}
?>
