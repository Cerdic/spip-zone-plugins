<?php
/**
	 * Kayé
	 * Le cahier de texte électronique spip spécial primaire
	 * Copyright (c) 2007
	 * Cédric Couvrat
	 * http://alecole.ac-poitiers.fr/
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
**/
	 
	include_spip('inc/presentation');


	function exec_kaye() {
  		global $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
		
				if($_POST['bt_modifier'] == 'modifier'){
			echo 'je modifie', $_POST['id_coche'];
			}    
		else if ($_POST['bt_supprimer'] == 'supprimer'){
			$sup = $_POST['id_coche'];
			echo 'Devoir n&deg;', $_POST['id_coche'];
			echo ' supprim&eacute;';
				//$query = "SELECT * FROM spip_kaye";
				//$val = spip_query (${query}) ;
				$sql = "DELETE FROM spip_kaye WHERE id_kaye=$sup";
				$req = spip_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
				mysql_close();
			}
		
		//requete de recup du nom classe
		$queryclasse = "SELECT id_classe FROM spip_classekaye WHERE id_auteur=$connect_id_auteur";
		$valclasse = spip_query (${queryclasse}) ;
		$rep = mysql_fetch_assoc($valclasse);
		$classe = $rep['id_classe'];
		
		$query = "SELECT * FROM spip_kaye WHERE id_classe=$classe";
		//$query = "SELECT * FROM spip_kaye";
		$val = spip_query (${query}) ;
		$nbval = spip_num_rows (${query}) ;
		debut_page(_T('livre:cahier de texte'), "naviguer", "kaye");

		debut_gauche();
		
			debut_cadre_formulaire();
			gros_titre(_T('Cahier de texte'));
			fin_cadre_formulaire();

		debut_raccourcis();
		
		if (!$val)  {
	
		icone_horizontale(_T('livre:cr&eacute;er_les_tables'), generer_url_ecrire("table"), '../'._DIR_PLUGIN_KAYE.'/img_pack/sql.png', 'creer.gif');
		icone_horizontale(_T('livre:effacer_les_tables'), generer_url_ecrire("efface"), '../'._DIR_PLUGIN_KAYE.'/img_pack/nosql.png', 'creer.gif');
		echo '<br>';
		}
		icone_horizontale(_T('livre:G&eacute;rer_les_classes'), generer_url_ecrire("gerer_classe"), '../'._DIR_PLUGIN_KAYE.'/img_pack/gerer_ref.gif', 'creer.gif');
		fin_raccourcis();	
		

    	debut_droite();

		debut_cadre_formulaire();
		gros_titre(_T('Liste des devoirs'));
		fin_cadre_formulaire();
		echo '<br>';		
		if (!$val)
			{ 
			echo 'Il n\'y a pas de devoirs pour votre classe'; //à vérifier
			}
		else
			{
			echo '<table width="100%" border="1" id="tableau_tout" class="tablesorter">
				<thead>
		 			<tr>
						<td></td>
						<td>MATIERE</td>
						<td>TEXTE</td>
						<td>DONNE LE</td>
						<td>POUR LE</td>
				  	</tr>
				</thead>';
				//$action = generer_action_auteur('supprimer_devoir','supprimer',generer_url_ecrire("kaye"));
				//echo $action;
				echo "<form action='$action' method='POST'>";
				//echo form_hidden($action);
			while ($data = mysql_fetch_assoc($val))
				{	
					
					echo '<tr>';
					echo '<td><input name="id_coche" type="radio" value="', $data['id_kaye'],'" /></td>';
					echo '<td>', $data['discipline'],'</td>';
					echo '<td>', propre($data['descriptif']),'</td>';
					list($annee, $mois, $jour) = explode("-", $data['date_jour']);
					$date_jour= $jour.'-'.$mois.'-'.$annee;
					echo '<td>',$date_jour,'</td>';
					list($annee, $mois, $jour) = explode("-", $data['date_echeance']);
					$date_echeance= $jour.'-'.$mois.'-'.$annee;
					echo '<td>', $date_echeance,'</td>';
	  				echo '</tr>';
					
				}
				
			echo '</table>';
			echo '<br>';
			//echo '<input name="bt_modifier" type="submit" value="modifier" /><input name="bt_supprimer" type="submit" value="supprimer" />';
			echo '<input name="bt_supprimer" type="submit" value="supprimer" />';

			echo'</form>';
			}
		
		echo '<br />';
		

		fin_page();

	}

?>
