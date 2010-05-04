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

	function exec_activites(){
		global  $table_prefix;
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_articles = generer_url_ecrire('articles');
		$url_activites = generer_url_ecrire('activites');
		$url_ajout_activite = generer_url_ecrire('edit_activite','agir=ajoute');
		$url_edit_activites = generer_url_ecrire('edit_activite','agir=modifie');
		$url_voir_activites = generer_url_ecrire('voir_activites');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
			$res= association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
		echo bloc_des_raccourcis ($res);
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_toutes_activites'));
		
		// FILTRES
		if ( isset($_REQUEST['mot']) ) { $mot = $_REQUEST['mot']; } 
		else { $mot= "%"; }
		
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';
		$annee=$_GET['annee'];
		if(empty($annee)){$annee = date('Y');}
		
		$query = sql_select("date_format( date_debut, '%Y' )  AS annee", "spip_evenements", "", "annee", "annee");
		while ($data = sql_fetch($query)) {
			if ($data['annee']==$annee) { echo ' <strong>'.$data['annee'].'</strong> '; }
			else { echo '<a href="'.$url_activites.'&annee='.$data['annee'].'&mot='.$mot.'">'.$data['annee'].'</a> ';}
		}
		echo '</td>';
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="'.$url_activites.'">';
		echo '<select name ="mot" class="fondl" onchange="form.submit()">';
		echo '<option value="%"';
		if ($mot=="%") { echo ' selected="selected"'; }
		echo '> Toutes</option>';
		$query = sql_select("*", "spip_mots", "type='Evènements'");
		while($data = sql_fetch($query)) {
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
		$debut=intval($_GET['debut']);
		if (!$debut) { $debut=0; }
		
		$query = sql_select('*, E.id_evenement, E.titre AS intitule, M.titre AS motact', 'spip_evenements AS E LEFT JOIN spip_mots_evenements AS A ON  A.id_evenement=E.id_evenement LEFT JOIN spip_mots AS M ON A.id_mot=M.id_mot', "date_format( date_debut, '%Y' ) = $annee AND (M.titre like '$mot' OR M.titre IS NULL)", '', "date_debut DESC",  "$debut,$max_par_page");
		while ($data = sql_fetch($query)) {
			$date = substr($data['date_debut'],0,10);
			$heure = substr($data['date_debut'],10,6);
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_evenement'].'</td>';
			//echo '<td >'.$jour.'-'.$mois. '-'.$annee.'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($date).'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$heure.'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;">'.$data['intitule'].'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;">'.$data['lieu'].'</td>';
			$sql = sql_select("sum(inscrits) AS total", "spip_asso_activites", "id_evenement=".$data['id_evenement']);
			while ($inscrits = sql_fetch($sql)) { echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$inscrits['total'].'</td>'; }
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_articles.'&id_article='.$data['id_article'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="'._T('asso:activite_bouton_modifier_article').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_ajout_activite.'&id='.$data['id_evenement'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'creer-12.gif" title="'._T('asso:activite_bouton_ajouter_inscription').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_voir_activites.'&id='.$data['id_evenement'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'voir-12.png" title="'._T('asso:activite_bouton_voir_liste_inscriptions').'"></a></td>';
			echo '</tr>';
		}
		echo '</table>';
		
		echo '<table width="100%">';
		echo '<tr>';
		
		//SOUS-PAGINATION
		echo '<td>';
		$nombre_selection=sql_countsel("spip_evenements", "date_format( date_debut, '%Y' ) = $annee");

		$pages=ceil($nombre_selection/$max_par_page);
		
		if ($pages == 1) { echo ''; }
		else {
			for ($i=0;$i<$pages;$i++) { 
				$position= $i * $max_par_page;
				if ($position == $debut) 
				  { echo ' <strong>'.$position.' </strong> '; }
				else { echo '<a href="'.$url_activites.'&annee='.$annee.'&debut='.$position.'&imputation='.$imputation.'">'.$position.'</a>  '; }
			}
		}
		echo '</td>';
		echo '</table>';
		
		fin_cadre_relief();  
		 echo fin_gauche(),fin_page(); 
	}
?>
