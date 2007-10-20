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

	function exec_action_ventes(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$url_action_ventes=generer_url_ecrire('action_ventes');
		
		$id_vente=$_REQUEST['id'];
		$action=$_REQUEST['action'];
		$url_retour=$_POST['url_retour'];
		
		$date_vente=$_POST['date_vente'];
		$article=$_POST['article'];
		$code=$_POST['code'];
		$acheteur=$_POST['acheteur'];
		$quantite=$_POST['quantite'];
		$date_envoi=$_POST['date_envoi'];
		$frais_envoi=$_POST['frais_envoi'];
		$prix_vente=$_POST['prix_vente'];
		$journal=$_POST['journal'];
		$justification='vente n&deg; '.$id_vente.' - '.$article;
		$commentaire=$_POST['commentaire'];
		$recette=$quantite*$prix_vente;
		
		//AJOUT VENTE
		if ($action=="ajoute"){
			spip_query( "INSERT INTO spip_asso_ventes (date_vente, article, code, acheteur, quantite, date_envoi, frais_envoi, don, prix_vente, commentaire) VALUES ("._q($date_vente).", "._q($article).", "._q($code).", "._q($acheteur).", "._q($quantite).", "._q($date_envoi).", "._q($frais_envoi).", "._q($don).", "._q($prix_vente).", "._q($commentaire)." )");
			$query=spip_query( "SELECT MAX(id_vente) AS id_vente FROM spip_asso_ventes");
			while ($data = spip_fetch_array($query)) {
				$id_vente=$data['id_vente'];
				$justification='vente n&deg; '.$id_vente.' - '.$article;
			}
			spip_query( "INSERT INTO spip_asso_comptes (date, journal,recette,depense,justification,imputation,id_journal) VALUES ("._q($date_vente).","._q($journal).","._q($recette).","._q($frais_envoi).","._q($justification).",".lire_config('association/pc_ventes').","._q($id_vente).")" );
			header ('location:'.$url_retour);
			exit;
		}
		
		//MODIFICATION VENTE
		if ($action=="modifie"){
			spip_query( "UPDATE spip_asso_ventes SET date_vente="._q($date_vente).", article="._q($article).", code="._q($code).", acheteur="._q($acheteur).", quantite="._q($quantite).", date_envoi="._q($date_envoi).", frais_envoi="._q($frais_envoi).", don="._q($don).", prix_vente="._q($prix_vente).", commentaire="._q($commentaire)." WHERE id_vente='$id_vente' " );
			spip_query( "UPDATE spip_asso_comptes SET date="._q($date_vente).", journal="._q($journal).",recette="._q($recette).", depense="._q($frais_envoi).", justification="._q($justification)." WHERE id_journal=$id_vente AND imputation="lire_config('association/pc_ventes') );
			header ('location:'.$url_retour);
			exit;
		}
		
		//SUPPRESSION PROVISOIRE ADHERENT	
		if (isset($_POST['delete'])) {
		
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
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
			
			debut_cadre_relief(  "", false, "", $titre = _T('Action sur les ventes associatives'));
			
			echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer '.$count;
			if ($count==1){echo ' vente !';} else {echo ' ventes !';}
			echo '</strong></p>';
			echo '<table>';
			echo '<form action="'.$url_action_ventes.'"  method="post">';
			for ( $i=0 ; $i < $count ; $i++ ) {	
				$id = $delete_tab[$i];
				echo '<input type=hidden name="drop[]" value="'.$id.'" checked>';
			}	
			echo '<tr>';
			echo '<td><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></td></tr>';	
			echo '</form>';
			echo '</table>';
			fin_cadre_relief();  
			fin_page();
		}
		
		//  SUPPRESSION DEFINITIVE ADHERENTS	
		if (isset($_POST['drop'])) {
			
			$url_retour = $_SERVER["HTTP_REFERER"];
			$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
			$count=count ($drop_tab);
			
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = $drop_tab[$i];
				spip_query("DELETE FROM spip_asso_ventes WHERE id_vente='$id' " );
				spip_query("DELETE FROM spip_asso_comptes WHERE id_journal='$id' AND imputation="lire_config('association/pc_ventes'));
			}
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>