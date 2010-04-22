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

	function exec_action_categorie(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_categorie=generer_url_ecrire('action_categorie');
		$url_retour = $_POST['url_retour'];
		
		$action=$_REQUEST['agir'];
		$id_categorie=$_REQUEST['id'];
		
		$libelle=$_POST['libelle'];
		$valeur=$_POST['valeur'];
		$duree=$_POST['duree'];
		$montant=$_POST['montant'];
		$commentaires=$_POST['commentaires'];
		
		
		//SUPPRESSION PROVISOIRE CATEGORIE
		if ($action == "supprime") {
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			//debut_page(_T(), "", "");
			$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Cat&eacute;gories de cotisation')) ;
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			
			$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif",false);	
			echo bloc_des_raccourcis($res);
			
			echo debut_droite("",true);
			
			echo debut_cadre_relief(  "", false, "", $titre = _T('Toutes les cat&eacute;gories de cotisation'));
			
			echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer le cat&eacute;gorie n&deg; '.$id.' !</strong></p>';
			echo '<form action="'.$url_action_categorie.'"  method="post">';
			
			echo '<input type=hidden name="agir" value="drop">';
			echo '<input type=hidden name="id" value="'.$id_categorie.'">';
			echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
			
			echo '<p><input name="submit" type="submit" value="Confirmer" class="fondo"></p>';
			echo '</form>';
			
			fin_cadre_relief();  
			 echo fin_gauche(),fin_page(); 
			exit;
		}
		
		//  SUPPRESSION DEFINITIVE CATEGORIE
		if ($action == "drop") {
			spip_query( "DELETE FROM spip_asso_categories WHERE id_categorie='$id_categorie' " );
			header ('location:'.$url_retour);
			exit;
		}
		
		//  MODIFICATION CATEGORIE
		if ($action =="modifie") { 
			spip_query( "UPDATE spip_asso_categories SET libelle="._q($libelle).", valeur="._q($valeur).", duree="._q($duree).", cotisation="._q($montant).", commentaires="._q($commentaires)." WHERE id_categorie='$id_categorie' " );
			header ('location:'.$url_retour);
			exit;
		}
		
		//  AJOUT CATEGORIE	
		if ($action == "ajoute") {
			spip_query( "INSERT INTO spip_asso_categories (libelle, valeur, duree, cotisation, commentaires) VALUES ("._q($libelle).", "._q($valeur).", "._q($duree).", "._q($montant).", "._q($commentaires)." )" );
			header ('location:'.$url_retour);
			exit;
		}
	}
?>
