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
	
	function exec_action_ressources(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_ressources=generer_url_ecrire('action_ressources');
		$action=$_REQUEST['agir'];
		$id_ressource=intval($_REQUEST['id']);
		$id_achat=intval($_POST['id_achat']);
		$code=$_POST['code'];
		$intitule=$_POST['intitule'];
		$date_acquisition=$_POST['date_acquisition'];
		$pu=$_POST['pu'];
		$statut=$_POST['statut'];
		$commentaire=$_POST["commentaire"];
		$url_retour=$_POST['url_retour'];
		
		//SUPPRESSION PROVISOIRE RESSOURCE
		
		if ($action == "supprime") {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_suppression_ressources')) ;
			
			association_onglets();
			
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			$query = sql_select("*", "spip_asso_ressources", "id_ressource=$id_ressource" ) ;
			while ($data = spip_fetch_array($query)) {
				$statut=$data['statut'];
				echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
				echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
				echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
				echo $data['intitule'];
				echo '</p>';
			}
			echo fin_boite_info(true);
			
			
			$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif",false);	
			echo bloc_des_raccourcis($res);
			
			echo debut_droite("",true);
			
			echo debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_suppression_ressources'));
			echo '<p><strong>'._T('asso:ressources_danger_suppression',array('id_ressource' => $id_ressource)).'</strong></p>';
			echo '<form action="'.$url_action_ressources.'&agir=drop"  method="post">';
			echo '<input type=hidden name="id" value="'.$id_ressource.'">';
			echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
			echo '<p style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			 fin_cadre_relief();  
			
			echo fin_gauche(), fin_page();
		}
		
		//  SUPPRESSION DEFINITIVE RESSOURCE
		
		if ($action == "drop") {
			
			spip_query( "DELETE FROM spip_asso_ressources WHERE id_ressource=$id_ressource" );
			header ('location:'.$url_retour);
		}
		
		//  MODIFICATION RESSOURCE
		
		if ($action =="modifie") { 
			spip_query( "UPDATE spip_asso_ressources SET code="._q($code).", intitule="._q($intitule).", date_acquisition="._q($date_acquisition).", id_achat=".$id_achat.", pu="._q($pu).", statut="._q($statut).", commentaire="._q($commentaire)." WHERE id_ressource=$id_ressource" );
			header ('location:'.$url_retour);
		}
		
		//  AJOUT RESSOURCE
		
		if ($action == "ajoute") {
			spip_query( "INSERT INTO spip_asso_ressources (code, intitule, date_acquisition, id_achat, pu, statut, commentaire) VALUES ("._q($code).", "._q($intitule).", "._q($date_acquisition).", ".$id_achat.", "._q($pu).", "._q($statut).", "._q($commentaire)." )" );
			header ('location:'.$url_retour);
		}
	}
?>
