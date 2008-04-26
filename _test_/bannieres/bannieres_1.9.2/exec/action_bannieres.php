<?php
	/**
	* Plugin Bannières
	*
	* Copyright (c) 2008
	* François de Montlivault
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	function exec_action_bannieres() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$id_banniere=$_REQUEST['id'];
		$action=$_REQUEST['action'];
		
		$nom=$_POST['nom'];
		$email=$_POST['email'];
		$site=$_POST['site'];
		$debut=$_POST['debut'];
		$fin=$_POST['fin'];
		$commentaire=$_POST['commentaire'];
		$alt=$_POST['alt'];	
		$chemin_destination = '../IMG/';
		$tmp_file = $_FILES['image']['tmp_name'];
		$ext=substr($_FILES['image']['name'], -3);	
		$infos_img = getimagesize($_FILES['image']['tmp_name']); 
		$width_max  = lire_config('bannieres/largeur');        // Largeur max de l'image en pixels 
		$height_max = lire_config('bannieres/hauteur');        // Hauteur max de l'image en pixels 
		
		$url_edit_banniere=generer_url_ecrire('edit_banniere','action=modifie&id='.$id_banniere);
		$url_action_bannieres=generer_url_ecrire('action_bannieres');
		$url_retour=$_POST['url_retour'];
		
		//SUPPRESSION PROVISOIRE BANNIERE
		if ($action=="supprime") {
			
			debut_page(_T('ban:gestion_bannieres'), "", "");
			
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('ban:bouton_retour'), $url_retour, _DIR_PLUGIN_BANNIERES."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_libelle_suppression'));
							
			echo '<p>'. _T('asso:adherent_message_confirmer_suppression').'</p>';
			echo '<form action="'.$url_action_bannieres.'"  method="post">';
			echo '<input type=hidden name="action" value="drop">';
			echo '<input type=hidden name="id" value="'.$id_banniere.'">';
			echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<input name="submit" type="submit" value="'._T('asso:adherent_bouton_confirmer').'" class="fondo"></td></tr>';
			
			fin_cadre_relief();
			
			fin_page();
			exit;
		}
		
		//  SUPPRESSION DEFINITIVE BANNIERE
		if ( $action=="drop") {
			spip_query("DELETE FROM spip_bannieres WHERE id_banniere='$id_banniere'");
			header ('location:'.$url_retour);
			exit;
		}
		
		//On vérifie les dimensions et taille de l'image 
        if($infos_img[0] > $width_max) { $width_err="Banni&egrave;re trop large !"; }
		if($infos_img[1] > $height_max) {$height_err="Banni&egrave;re trop haute !";}
		if($ext IN lire_config('bannieres/formats')) {$ext_err="Mauvais format d'image !";}
		$errors = array($width_err,$height_err, $ext_err);
		if ($width_err || $height_err || $ext_err) {
			header ('location:'.$url_edit_banniere.'&messages='.$errors);
			exit;
		}
			
		//AJOUT BANNIERE
		if ($action=="ajoute") {		
			spip_query(" INSERT INTO spip_bannieres (nom,email,site,debut,fin,commentaire,alt,creation) VALUES("._q($nom).", "._q($email).", "._q($site).", "._q($debut).", "._q($fin).", "._q($commentaire).", "._q($alt).", CURRENT_DATE() ) ");
			$query= spip_query("SELECT max(id_banniere) AS id_ban FROM spip_bannieres");
			$data=spip_fetch_array($query);
			$id_banniere=$data['id_ban'];
		}
		
		//MODIFICATION BANNIERE
		if ($action=="modifie") {		
			spip_query("UPDATE spip_bannieres SET nom="._q($nom).", email="._q($email).", site="._q($site).", debut="._q($debut).", fin="._q($fin).", commentaire="._q($commentaire).", alt="._q($alt)." WHERE id_banniere="._q($id_banniere) );
		}
		
		//UPLOAD BANNIERE
		if ($_FILES['image']['error'] == 0) {
			move_uploaded_file($tmp_file, $chemin_destination."ban_".$id_banniere.".".$ext);
			spip_query( "UPDATE spip_bannieres SET ext="._q($ext)." WHERE id_banniere="._q($id_banniere) );
		}
		
		header ('location:'.$url_retour);
		exit;
	} 
?>