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
	//---------------------------- 
	//  DEBUT MODIF DES ADRESSES 
	//---------------------------- 

	include_spip('inc/presentation');

	function exec_edit_banniere(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('ban:gestion_bannieres'), "", "");

		
		// LES URL'S
		$url_upload=generer_url_ecrire('upload');
		$url_action_bannieres=generer_url_ecrire('action_bannieres');
		$url_retour = $_SERVER['HTTP_REFERER'];

		debut_gauche();
		
		debut_boite_info();
		echo '<p>'._T('ban:info_edition').'</p>';
		fin_boite_info();
		
		if ($connect_statut == '0minirezo') {
			debut_raccourcis();
			icone_horizontale("Retour", $url_retour,_DIR_PLUGIN_BANNIERES."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
		}		
		debut_droite();
		debut_cadre_relief ( "../"._DIR_PLUGIN_BANNIERES."/img_pack/bannieres.png", false, "", $titre = 'FICHE BANNIERE');
		
		$action=$_GET['action'];
		$id_banniere= $_GET['id'];
		
		$query = spip_query( "SELECT * FROM spip_bannieres where id_banniere='$id_banniere' " );
		
		while($data = spip_fetch_array($query)) {
			$nom=$data['nom'];
			$email=$data['email'];
			$site=$data['site'];
			$image=$data['image'];
			$alt=$data['alt'];
			$ext=$data['ext'];
			$debut=$data['debut'];
			$fin=$data['fin'];
			$commentaire=$data['commentaire'];
		}
		echo '<form action="'.$url_action_bannieres.'" method="post" enctype="multipart/form-data">';	
		
		#Identification
		if ($action=="modifie"){echo '<p><img src="../IMG/ban_'.$id_banniere.'.'.$ext.'" width="100%" /></p>';}
		echo '<label for="nom"><strong>Campagne :</strong></label>';
		echo '<input name="nom" type="text" value="'.$nom.'" id="nom" class="formo" />';
		echo '<label for="email"><strong>Email :</strong></label>';
		echo '<input name="email" type="text" value="'.$email.'" id="email" class="formo" />';
		echo '<label for="site"><strong>Site :</strong></label>';
		echo '<input name="site" type="text" value="';
		if ($action=="ajoute"){echo 'http://';} else {echo $site;}
		echo '" id="site" class="formo" />';
		echo '<label for="debut"><strong>Date de d&eacute;marrage (AAAA-MM-JJ):</strong></label>';
		echo '<input name="debut" type="text" value="'.$debut.'" id="debut" class="formo" />';
		echo '<label for="fin"><strong>Date de fin (AAAA-MM-JJ) :</strong>';
		echo '<input name="fin" type="text" value="'.$fin.'" id="fin" class="formo" />';
		echo '<label for="commentaire"><strong>Commentaire :</strong>';  
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea><br />';
		echo '<label for="image"><strong>';
		if($action=="modifie"){ echo 'Modifier la banni&egrave;re';} else {echo 'Charger la banni&egrave;re';}
		echo '</strong> :';
		echo '<input type=file name="image" id="image" class="formo" />';
		echo '<label for="alt"><strong>Texte alternatif :</strong>';
		echo '<input type=text name="alt" value="'.$alt.'" id="alt" class="formo" />';
		echo '<p style="text-align:center;"><input name="submit" type="submit" value="Envoyer" class="fondo"></p>';
		echo '<input name="id" type="hidden" value="'.$id_banniere.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		echo '<input type="hidden" name="action" value="'.$action.'">';
		echo '</form>';
		
		// ON FERME TOUT
		fin_cadre_relief();
		fin_page();
	}
?>

