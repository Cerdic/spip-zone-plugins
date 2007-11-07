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

	function exec_action_adherents() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$id_auteur=$_POST['id'];
		if (lire_config('association/indexation')=="id_asso"){ $id_asso=$_POST['id_asso'];}
		$categorie=$_POST['categorie'];
		$validite=$_POST['validite'];
		$utilisateur1=$_POST['utilisateur1'];
		$utilisateur2=$_POST['utilisateur2'];
		$utilisateur3=$_POST['utilisateur3'];
		$utilisateur4=$_POST['utilisateur4'];
		$statut_interne=$_POST['statut_interne'];
		$action=$_POST['action'];
		$url_retour=$_POST['url_retour'];
		
		//MODIFICATION ADHERENT
		if ($action=="modifie") {
			$query=spip_query("SELECT * FROM spip_asso_adherents WHERE id_auteur=$id_auteur");
			if($query) {
				spip_query("UPDATE spip_asso_adherents SET id_asso="._q($id_asso).", utilisateur1="._q($utilisateur1).", utilisateur2="._q($utilisateur2).", utilisateur3="._q($utilisateur3).", utilisateur4="._q($utilisateur4).", validite="._q($validite)." WHERE id_auteur="._q($id_auteur) );			
			} else{
				spip_query("INSERT INTO spip_asso_adherents (id_auteur, id_asso, utilisateur1, utilisateur2, utilisateur3, utilisateur4, validite) VALUES ("._q($id_auteur).", "._q($id_asso).", "._q($utilisateur1).", "._q($utilisateur2).", "._q($utilisateur3).", "._q($utilisateur4).", "._q($validite));
			}
			spip_query("UPDATE spip_auteurs_elargis SET categorie="._q($categorie).", statut_interne="._q($statut_interne)." WHERE id_auteur="._q($id_auteur) );
			header ('location:'.$url_retour);
			exit;
		}
		
		//SUPPRESSION PROVISOIRE ADHERENT
		if (isset($_POST['delete'])) {
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
			echo '<p>'. _T('asso:adherent_message_confirmer_suppression').' : <br>';
			echo '<table>';
			echo '<form action="'.$url_action_adherents.'"  method="post">';
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = $delete_tab[$i];
				$query = spip_query( "SELECT * FROM spip_asso_adherents where id_adherent='$id' " );
				while($data = spip_fetch_array($query)) {
					echo '<tr>';
					echo '<td><strong>'.$data['nom'].' '.$data['prenom'].'</strong>';
					echo '<td>';
					echo '<input type=checkbox name="drop[]" value="'.$id.'" checked>';
				}
			}
			echo '<tr>';
			echo '<td colspan="2"><input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<input name="submit" type="submit" value="'._T('asso:adherent_bouton_confirmer').'" class="fondo"></td></tr>';
			echo '<table>';
			echo '</p>';
			
		}

	//  SUPPRESSION DEFINITIVE ADHERENTS
	//---------------------------- 
	if (isset($_POST['drop'])) {

		$url_retour=$_POST['url_retour'];

		$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
		$count=count ($drop_tab);

		for ( $i=0 ; $i < $count ; $i++ ) {
			$id = $drop_tab[$i];
			spip_query("DELETE FROM spip_asso_adherents WHERE id_adherent='$id'");
		}
		echo '<p><strong>'._T('asso:adherent_message_suppression_faite').'</strong></p>';

		echo '<p>';
		icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
	}

	fin_boite_info();
  
	fin_cadre_relief();

	fin_page();
	} 
?>