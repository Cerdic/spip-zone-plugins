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
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_dons = generer_url_ecrire('dons');
		$url_ajout_don= generer_url_ecrire('edit_don','agir=ajouter');
		
		//debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		  $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		
		
		$res=association_icone(_T('Ajouter un don'),  $url_ajout_don, 'ajout_don.png');
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('Tous les dons'));

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
			{echo ' <strong>'.$data['annee'].'</strong>';}
			else {echo ' <a href="'.$url_dons.'&annee='.$data['annee'].'">'.$data['annee'].'</a>';}
			}
		echo '</td>';
		echo '</table>';

		//TABLEAU
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>' . _L('ID') . '</strong></td>';
		echo '<td><strong>' . _L('Date') . '</strong></td>';
		echo '<td><strong>' . _L('NOM') . '</strong></td>';
		echo '<td style="text-align:right;"><strong>' . _L('Argent') . '</strong></td>';
		echo '<td><strong>' . _L('Colis') . '</strong></td>';
		echo '<td style="text-align:right;"><strong>' . _L('Valeur') . '</strong></td>';
		echo '<td><strong>' . _L('Contrepartie') . '</strong></td>';
		echo '<td colspan=2><strong>' . _L('Action') . '</strong></td>';
		echo '</tr>';
		$query = sql_select('*', "spip_asso_dons", "date_format( date_don, '%Y' ) = '$annee'", '',  "id_don" ) ;
		while ($data = sql_fetch($query)) {
			$id_don = $data['id_don'];
			$url_edit_don = generer_url_ecrire('edit_don',"agir=modifier&id=$id_don");
			$url_action_dons = generer_url_ecrire('action_dons', "id=$id_don");

			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11 border1">'.$data['id_don'].'</td>';
			echo '<td class="arial11 border1">'.association_datefr($data['date_don']).'</td>';
			echo '<td class="arial11 border1">'.$data['bienfaiteur'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right;">'.number_format($data['argent'], 2, ',', ' ').'&nbsp;&euro;</td>';
			echo '<td class="arial11 border1">'.$data['colis'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right;">'.number_format($data['valeur'], 2, ',', ' ').'&nbsp;&euro;</td>';
			echo '<td class="arial11 border1">'.propre($data['contrepartie']).'</td>';
			echo '<td  class="arial11 border1" style="text-align:center;"><a href="'.$url_action_dons.'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="' . _L('Supprimer le don') . '"></a></td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_edit_don.'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="' . _L('Mettre &agrave; jour le don') . '"></a>';
			echo '</tr>';
		}
		echo '</table>';
		
		fin_cadre_relief();  
		echo fin_gauche(),fin_page(); 
	}
}
?>
