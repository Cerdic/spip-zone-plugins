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

	function exec_action_categorie(){
		global $connect_statut, $connect_toutes_rubriques;
		
		
		$url_asso = generer_url_ecrire('association');
		$url_ajouter = generer_url_ecrire('ajouter');
		$url_relance = generer_url_ecrire('essai');
		$url_bienfaiteur = generer_url_ecrire('bienfaiteur');
		$url_vente = generer_url_ecrire('ventes');
		$url_banque = generer_url_ecrire('banque');
		$url_delete = generer_url_ecrire('delete_membre');
		$url_categories=generer_url_ecrire('categories');
		
		
		$action=$_REQUEST['action'];
		$id_categorie=$_REQUEST['id'];
		
		$libelle=$_POST['libelle'];
		$valeur=$_POST['valeur'];
		$duree=$_POST['duree'];
		$montant=$_POST['montant'];
		$commentaires=$_POST['commentaires'];
		
		//SUPPRESSION PROVISOIRE CATEGORIE
		if ($action == "supprime") {
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
			
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('Toutes les cat&eacute;gories de cotisation'));
			
			echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer le cat&eacute;gorie n&deg; '.$id.' !</strong></p>';
			echo '<form action="'.$url_action_categories.'"  method="post">';
			
			echo '<input type=hidden name="action" value="drop">';
			echo '<input type=hidden name="id" value="'.$id_categorie.'">';
			echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
			
			echo '<p><input name="submit" type="submit" value="Confirmer" class="fondo"></p>';
			echo '</form>';
			
			fin_cadre_relief();  
			fin_page();
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
