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

	function exec_action_ressources(){
		global $connect_statut, $connect_toutes_rubriques;
		
		$url_action_ressources=generer_url_ecrire('action_ressources');
		$action=$_REQUEST['action'];
		$id_ressource=$_REQUEST['id'];
		$code=$_POST['code'];
		$intitule=$_POST['intitule'];
		$date_acquisition=$_POST['date_acquisition'];
		$id_achat=$_POST['id_achat'];
		$pu=$_POST['pu'];
		$statut=$_POST['statut'];
		$commentaire=$_POST["commentaire"];
		$url_retour=$_POST['url_retour'];
		
		//SUPPRESSION PROVISOIRE RESSOURCE
		
		if ($action == "supprime") {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			debut_page(_T('Ressources'), "", "");
			
			debut_gauche();
			
			debut_boite_info();
			echo '<p>';
			icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif','rien.gif' );
			echo '</p>';
			fin_boite_info();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('Effacer une ressource'));
			echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer l\'article "'.$code.' " !</strong></p>';
			echo '<form action="'.$url_action_ressources.'&action=drop"  method="post">';
			echo '<input type=hidden name="id" value="'.$id_ressource.'">';
			echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
			echo '<p style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			fin_cadre_relief();  
			
			fin_page();
		}
		
		//  SUPPRESSION DEFINITIVE RESSOURCE
		
		if ($action == "drop") {
			
			spip_query( "DELETE FROM spip_asso_ressources WHERE id_ressource='$id_ressource' " );
			header ('location:'.$url_retour);
		}
		
		//  MODIFICATION RESSOURCE
		
		if ($action =="modifie") { 
			spip_query( "UPDATE spip_asso_ressources SET code="._q($code).", intitule="._q($intitule).", date_acquisition="._q($date_acquisition).", id_achat="._q($id_achat).", pu="._q($pu).", statut="._q($statut).", commentaire="._q($commentaire)." WHERE id_ressource='$id_ressource' " );
			header ('location:'.$url_retour);
		}
		
		//  AJOUT RESSOURCE
		
		if ($action == "ajoute") {
			spip_query( "INSERT INTO spip_asso_ressources (code, intitule, date_acquisition, id_achat, pu, statut, commentaire) VALUES ("._q($code).", "._q($intitule).", "._q($date_acquisition).", "._q($id_achat).", "._q($pu).", "._q($statut).", "._q($commentaire)." )" );
			header ('location:'.$url_retour);
		}
	}
?>
