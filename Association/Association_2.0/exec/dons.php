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
			exit;
		}
		
		$url_asso = generer_url_ecrire('association');	
		$url_dons = generer_url_ecrire('dons');
		$url_ajout_don= generer_url_ecrire('edit_don','agir=ajoute');
		$url_edit_don =generer_url_ecrire('edit_don','agir=modifie');
		$url_action_dons = generer_url_ecrire('action_dons');
		
		//debut_page(_T('Gestion pour  Association'), "", "");
		  $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_T('asso:Ajouter un don'), $url_ajout_don, _DIR_PLUGIN_ASSOCIATION_ICONES.'ajout_don.png','rien.gif',false );
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('Tous les dons'));

		// PAGINATION ET FILTRES
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';

		$annee=$_GET['annee'];
		if(empty($annee)){$annee = date('Y');}

		$query = spip_query ( "SELECT date_format( date_don, '%Y' )  AS annee FROM spip_asso_dons GROUP BY annee ORDER BY annee" );

		while ($data = spip_fetch_array($query))
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
		echo '<td><strong>ID</strong></td>';
		echo '<td><strong>Date</strong></td>';
		echo '<td><strong>NOM</strong></td>';
		echo '<td style="text-align:right;"><strong>Argent</strong></td>';
		echo '<td><strong>Colis</strong></td>';
		echo '<td style="text-align:right;"><strong>Valeur</strong></td>';
		echo '<td><strong>Contrepartie</strong></td>';
		echo '<td colspan=2><strong>Action</strong></td>';
		echo '</tr>';
		$query = spip_query ("SELECT * FROM spip_asso_dons WHERE date_format( date_don, '%Y' ) = '$annee'  ORDER by id_don" ) ;
		while ($data = spip_fetch_array($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['id_don'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.association_datefr($data['date_don']).'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['bienfaiteur'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($data['argent'], 2, ',', ' ').'&nbsp;&euro;</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['colis'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($data['valeur'], 2, ',', ' ').'&nbsp;&euro;</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['contrepartie'].'</td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_action_dons.'&agir=supprime&id='.$data['id_don'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="Supprimer le don"></a></td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_edit_don.'&id='.$data['id_don'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="Mettre &agrave; jour le don"></a>';
			echo '</tr>';
		}
		echo '</table>';
		
		fin_cadre_relief();  
		  echo fin_gauche(),fin_page(); 
	}
?>
