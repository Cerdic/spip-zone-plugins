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
	
	function exec_action_plan(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$id_plan=$_REQUEST['id'];
		$action=$_REQUEST['action'];
		
		$code=$_POST['code'];
		$intitule=$_POST['intitule'];
		$classe=$_POST['classe'];
		$reference=$_POST['reference'];
		$solde_anterieur=$_POST['solde_anterieur'];
		$date_anterieure=$_POST['date_anterieure'];
		$actif=$_POST['actif'];
		$commentaire=$_POST['commentaire'];
		
		$url_retour=$_POST['url_retour'];
		
		//SUPPRESSION PROVISOIRE COMPTE
		if ($action == "supprime") {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			debut_page(_T('Suppression de compte'), "", "");
			
			association_onglets();
			
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('Suppression de compte'));
			echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer le compte '.$code.' !</strong></p>';
			echo '<form action="'.$url_action_plan.'"  method="post">';
			echo '<input type="hidden" name="action" value="drop">';
			echo '<input type="hidden" name="id" value="'.$id_plan.'">';
			echo '<input type="hidden" name="url_retour" value="'.$url_retour.'">';
			echo '<div style="text-align:right;"><input name="submit" type="submit" value="Confirmer" class="fondo"></div>';
			fin_cadre_relief();  
			
			fin_page();
		}
		
		//  SUPPRESSION DEFINITIVE COMPTE
		if ($action == "drop") {
			spip_query( "DELETE FROM spip_asso_plan WHERE id_plan='$id_plan' " );
			header ('location:'.$url_retour);
			exit;
		}
		
		//  MODIFICATION  COMPTE
		if ($action =="modifie") { 
			spip_query( "UPDATE spip_asso_plan SET code="._q($code).", intitule="._q($intitule).", classe="._q($classe).", reference="._q($reference).", solde_anterieur="._q($solde_anterieur).", date_anterieure="._q($date_anterieure).", actif="._q($actif).", commentaire="._q($commentaire)." WHERE id_plan='$id_plan' ");
			header ('location:'.$url_retour);
			exit;
		}

		//  AJOUT  COMPTE
		if ($action == "ajoute") {
			spip_query( "INSERT INTO spip_asso_plan (code, intitule, classe, reference, solde_anterieur, date_anterieure, actif, commentaire) VALUES ("._q($code).", "._q($intitule).", "._q($classe).", "._q($reference).", "._q($solde_anterieur).", "._q($date_anterieure).", "._q($actif).", "._q($commentaire)." )");
			header ('location:'.$url_retour);
			exit;
		}	
	}
?>
