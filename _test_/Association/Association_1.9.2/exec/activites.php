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

	function exec_activites(){
		global $connect_statut, $connect_toutes_rubriques, $table_prefix;
		
		include_spip ('inc/acces_page');
		
		$url_articles = generer_url_ecrire('articles');
		$url_activites = generer_url_ecrire('activites');
		$url_ajout_activite = generer_url_ecrire('edit_activite','action=ajoute');
		$url_edit_activites = generer_url_ecrire('edit_activite','action=modifie');
		$url_voir_activites = generer_url_ecrire('voir_activites');
		
		debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_toutes_activites'));
		
		// FILTRES
		if ( isset($_REQUEST['mot']) ) { $mot = $_REQUEST['mot']; } 
		else { $mot= "%"; }
		
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';
		$annee=$_GET['annee'];
		if(empty($annee)){$annee = date('Y');}
		
		$query = spip_query ("SELECT date_format( date_debut, '%Y' )  AS annee FROM spip_evenements GROUP BY annee ORDER by annee");
		while ($data = spip_fetch_array($query)) {
			if ($data['annee']==$annee) { echo ' <strong>'.$data['annee'].'</strong>'; }
			else { echo '<a href="'.$url_activites.'&annee='.$data['annee'].'&mot='.$mot.'">'.$data['annee'].'</a>';}
		}
		echo '</td>';
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="'.$url_activites.'">';
		echo '<select name ="mot" class="fondl" onchange="form.submit()">';
		echo '<option value="%"';
		if ($mot=="%") { echo ' selected="selected"'; }
		echo '> Toutes</option>';
		$query = spip_query("SELECT * FROM spip_mots WHERE type='Evènements'");
		while($data = spip_fetch_array($query)) {
			echo '<option value="'.$data["titre"].'"';
			if ($mot==$data["titre"]) { echo ' selected="selected"'; }
			echo '> '.$data["titre"].'</option>';
		}
		echo '</select>';
		echo '</form>';
		echo '</table>';
		
		//TABLEAU
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td style="text-align:right;"><strong>ID</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_date').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_heure').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_intitule').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_lieu').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_inscrits').'</strong></td>';
		echo '<td colspan="3" style="text-align:center;"><strong>'._T('asso:activite_entete_action').'</strong></td>';
		echo '</tr>';
		
		$max_par_page=30;
		$debut=$_GET['debut'];
		
		if (empty($debut)) { $debut=0; }
		
		$query = spip_query ("SELECT *, spip_evenements.id_evenement, spip_evenements.titre AS intitule, spip_mots.titre AS motact  FROM ".$table_prefix."_evenements LEFT JOIN spip_mots_evenements ON  spip_mots_evenements.id_evenement=spip_evenements.id_evenement LEFT JOIN spip_mots ON spip_mots_evenements.id_mot=spip_mots.id_mot WHERE date_format( date_debut, '%Y' ) = $annee AND (spip_mots.titre like '$mot' OR spip_mots.titre IS NULL) ORDER BY date_debut DESC LIMIT $debut,$max_par_page");
		while ($data = spip_fetch_array($query)) {
			$date = substr($data['date_debut'],0,10);
			$heure = substr($data['date_debut'],10,6);
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_evenement'].'</td>';
			//echo '<td >'.$jour.'-'.$mois. '-'.$annee.'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($date).'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$heure.'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;">'.$data['intitule'].'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;">'.$data['lieu'].'</td>';
			$sql = spip_query("SELECT sum(inscrits) AS total FROM spip_asso_activites WHERE id_evenement=".$data['id_evenement']);
			while ($inscrits = spip_fetch_array($sql)) { echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$inscrits['total'].'</td>'; }
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_articles.'&id_article='.$data['id_article'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:activite_bouton_modifier_article').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_ajout_activite.'&id='.$data['id_evenement'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/creer-12.gif" title="'._T('asso:activite_bouton_ajouter_inscription').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_voir_activites.'&id='.$data['id_evenement'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/voir-12.gif" title="'._T('asso:activite_bouton_voir_liste_inscriptions').'"></a></td>';
			echo '</tr>';
		}
		echo '</table>';
		
		echo '<table width="100%">';
		echo '<tr>';
		
		//SOUS-PAGINATION
		echo '<td>';
		$query = spip_query("SELECT * FROM ".$table_prefix."_asso_comptes WHERE date_format( date, '%Y' ) = $annee AND imputation like '$imputation' ");
		$nombre_selection=spip_num_rows($query);
		$pages=intval($nombre_selection/$max_par_page) + 1;
		
		if ($pages == 1) { echo ''; }
		else {
			for ($i=0;$i<$pages;$i++) { 
				$position= $i * $max_par_page;
				if ($position == $debut) { echo '<strong>'.$position.' </strong>'; }
				else { echo '<a href="'.$url_comptes.'&annee='.$annee.'&debut='.$position.'&imputation='.$imputation.'">'.$position.'</a> '; }
			}
		}
		echo '</td>';
		echo '</table>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>

