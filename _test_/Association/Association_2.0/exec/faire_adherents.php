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
	
	function exec_faire_adherents() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$id_auteur=$_POST['id'];
		if (lire_config('association/indexation')=="id_asso"){ $id_asso=$_POST['id_asso'];}
		$categorie=$_POST['categorie'];
		$validite=$_POST['validite'];
		$commentaire=$_POST['commentaire'];
		$statut_interne=$_POST['statut_interne'];
		$faire=$_POST['faire'];
		$url_retour=$_POST['url_retour'];
		
		//MODIFICATION ADHERENT
		if ($faire=="modifie") {		
			spip_query("UPDATE spip_auteurs_elargis SET id_asso="._q($id_asso).", commentaire="._q($commentaire).", validite="._q($validite).", categorie="._q($categorie).", statut_interne="._q($statut_interne)." WHERE id_auteur="._q($id_auteur) );
			header ('location:'.$url_retour);
			exit;
		}
		
		//SUPPRESSION PROVISOIRE ADHERENT
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
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_libelle_suppression'));
			
		
		if (isset($_POST['delete'])) {
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
			
			echo '<p>'. _T('asso:adherent_message_confirmer_suppression').' : <br>';
			echo '<table>';
			echo '<form faire="#"  method="post">';
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = $delete_tab[$i];
				$query = spip_query( "SELECT * FROM spip_auteurs_elargis where id_auteur='$id' " );
				while($data = spip_fetch_array($query)) {
					echo '<tr>';
					echo '<td><strong>'.$data['nom_famille'].' '.$data['prenom'].'</strong>';
					echo '<td>';
					echo '<input type=checkbox name="drop[]" value="'.$id.'" checked>';
				}
			}
			echo '<tr>';
			echo '<td colspan="2"><input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<input name="submit" type="submit" value="'._T('asso:adherent_bouton_confirmer').'" class="fondo"></td></tr>';
			echo '<table>';
			echo '</p>';
			fin_cadre_relief();
			fin_page();
			exit;
		}
		
		//  SUPPRESSION DEFINITIVE ADHERENTS
		//---------------------------- 
		if (isset($_POST['drop'])) {
			
			$url_retour=$_POST['url_retour'];
			
			$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
			$count=count ($drop_tab);
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = $drop_tab[$i];
				spip_query("DELETE FROM spip_auteurs_elargis WHERE id_auteur='$id'");
				spip_query("DELETE FROM spip_auteurs WHERE id_auteur='$id'");
			}
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>