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

	function exec_action_comptes(){
		global $connect_statut, $connect_toutes_rubriques;
		
		$url_action_comptes=generer_url_ecrire('action_comptes');
		
		$id_compte=$_REQUEST['id'];
		$date=$_POST['date'];
		$imputation=$_POST['imputation'];
		$recette=$_POST['recette'];
		$depense=$_POST['depense'];
		$justification=$_POST['justification'];
		$journal=$_POST['journal'];
		
		$action = $_REQUEST['action'];
		$url_retour=$_POST['url_retour'];
		
		//AJOUT OPERATION
		if ($action=="ajoute") {
			spip_query( "INSERT INTO spip_asso_comptes (date, imputation, recette, depense, journal, justification) VALUES ('$date', '$imputation' ,'$recette', '$depense', '$journal', '$justification')");
			header ('location:'.$url_retour);
			exit;
		}
		
		//MODIFICATION OPERATION
		if ($action =="modifie") { 
			spip_query( " UPDATE spip_asso_comptes SET date='$date', recette='$recette', depense='$depense', justification='$justification', journal='$journal' WHERE id_compte='$id_compte' " );
			header ('location:'.$url_retour);
			exit;
		}
		
		//SUPPRESSION PROVISOIRE OPERATION
		if ($action == "supprime") {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			debut_page(_T('Gestion pour  Association'), "", "");
			association_onglets();
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('Op&eacute;rations comptables'));
			echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer la ligne de compte n&deg; '.$id_compte.' !</strong></p>';
			echo '<form action="'.$url_action_comptes.'"  method="post">';
			echo '<input type=hidden name="action" value="drop">';
			echo '<input type=hidden name="id" value="'.$id_compte.'">';
			echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
			echo '<p style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			fin_cadre_relief();  
			
			fin_page();
			exit;
		}
		
		//---------------------------- 
		//  SUPPRESSION DEFINITIVE OPERATION
		//---------------------------- 		
		if ($action == "drop") {
			
			$url_retour=$_POST['url_retour'];
			
			spip_query( "DELETE FROM spip_asso_comptes WHERE id_compte='$id_compte' " );
			header ('location:'.$url_retour);
			exit;
		}
		
		//---------------------------- 
		//VALIDATION PROVISOIRE COMPTE		
		if (isset($_POST['valide'])) {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$valide_tab=(isset($_POST["valide"])) ? $_POST["valide"]:array();
			$count=count ($valide_tab);
			
			debut_page(_T('Gestion pour  Association'), "", "");
			
			association_onglets();
			
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('Op&eacute;rations comptables'));
			echo '<p>Vous vous appr&ecirc;tez &agrave; valider les op&eacute;rations  : <br>';
			echo '<table>';
			echo '<form action="'.$url_action_comptes.'"  method="post">';
			for ( $i=0 ; $i < $count ; $i++ ) {	
				$id = $valide_tab[$i];
				$query = spip_query("SELECT * FROM spip_asso_comptes where id_compte='$id'");
				while($data = spip_fetch_array($query)) {
					echo '<tr>';
					echo '<td><strong>'.association_datefr($data['date']).'</strong>';
					echo '<td><strong>'.$data['justification'].'</strong>';
					echo '<td>';
					echo '<input type=checkbox name="definitif[]" value="'.$id.'" checked>';
				}	
			}
			echo '</table>';
			echo '<p>Apr&egrave;s confirmation vous ne pourrez plus modifier ces op&eacute;rations !</p>';
			
			echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
			
			echo '<p style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			fin_cadre_relief();  
			
			fin_page();
			exit;
		}

		//---------------------------- 
		//  VALIDATION DEFINITIVE COMPTES
		//---------------------------- 		
		if (isset($_POST['definitif'])) {
			
			$url_retour=$_POST['url_retour'];
			
			$definitif_tab=(isset($_POST["definitif"])) ? $_POST["definitif"]:array();
			$count=count ($definitif_tab);
			
			for ( $i=0 ; $i < $count ; $i++ ) {	
				$id = $definitif_tab[$i];
				spip_query( "UPDATE spip_asso_comptes SET valide='oui' WHERE id_compte='$id' " );
			}
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>