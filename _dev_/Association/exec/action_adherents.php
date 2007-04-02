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
include_spip('inc/filtres');

function exec_action_adherents() {
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

	$url_action_adherents=generer_url_ecrire('action_adherents');

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_action_membres_actifs'));
	debut_boite_info();

	print association_date_du_jour();

	$id_adherent=$_POST['id_adherent'];
	$nom=addslashes($_POST['nom']);
	$prenom=$_POST['prenom'];
	$sexe=$_POST['sexe'];
	$categorie=addslashes($_POST['categorie']);
	$fonction=addslashes($_POST['fonction']);
	$email=$_POST['email'];
	$numero=$_POST['numero'];
	$rue=addslashes($_POST['rue']);
	$cp=$_POST['cp'];
	$ville=addslashes($_POST['ville']);
	$telephone=$_POST['telephone'];
	$portable=$_POST['portable'];
	//$divers=$_POST['divers'];
	$remarques=$_POST['remarques'];
	$id_asso=$_POST['id_asso'];
	$naissance=$_POST['naissance'];
	$profession=addslashes($_POST['profession']);
	$societe=addslashes($_POST['societe']);
	//$identifiant=$_POST['identifiant'];
	//$passe=$_POST['passe'];
	$secteur=$_POST['secteur'];
	$publication=$_POST['publication'];
	$utilisateur1=$_POST['utilisateur1'];
	$utilisateur2=$_POST['utilisateur2'];
	$utilisateur3=$_POST['utilisateur3'];
	$utilisateur4=$_POST['utilisateur4'];
	$validite=$_POST['validite'];
	$statut=$_POST['statut'];

	$rue=nl2br($rue); 
	$remarques=nl2br($remarques); 

	$action=$_POST['action'];
	$url_retour=$_POST['url_retour'];

//---------------------------- 
//AJOUT ADHERENT
//---------------------------- 
	
	if ($action=="ajoute"){

// Inscription adherent
		spip_query("INSERT INTO spip_asso_adherents (nom, prenom, sexe, email, numero, rue, cp, ville, telephone, portable, remarques, id_asso, naissance, profession, societe, secteur, publication, utilisateur1, utilisateur2, utilisateur3, utilisateur4, categorie, statut, creation) VALUES ('$nom', '$prenom', '$sexe', '$email', '$numero', '$rue', '$cp', '$ville', '$telephone', '$portable', ".spip_abstract_quote($remarques).", '$id_asso', '$naissance', '$profession', '$societe', '$secteur', '$publication', '$utilisateur1', '$utilisateur2', '$utilisateur3', '$utilisateur4', '$categorie', 'prospect', CURRENT_DATE() )");

//Validation email si il existe
		if( $email=email_valide($email) || empty($email) ){

			echo '<p><strong>'._T('asso:adherent_message_ajout_adherent',array('prenom' => $prenom, 'nom' => $nom)).'</strong>';

// Inscription visiteur
			$pass = creer_pass_aleatoire(8, $email);
			$nom_inscription =  association_cree_login($email);                                  
			$login = association_cree_login($email);
			$mdpass = md5($pass);
			$htpass = generer_htpass($pass);
			$statut = '6forum' ;
			$cookie = creer_uniqid();
			$query = spip_query("SELECT * FROM spip_auteurs WHERE email='$email'");          
			if (!spip_fetch_array($query))  {   
				$query = spip_query("INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, cookie_oubli) VALUES ('$nom_inscription', '$email', '$login', '$mdpass', '$statut', '$htpass', '$cookie') ");
				if ($query) { echo _T('asso:adherent_message_ajout_adherent_suite'); }
			}
			//on met a jour  les id_auteur pour tous les adherents
			spip_query("UPDATE spip_asso_adherents INNER JOIN spip_auteurs ON spip_asso_adherents.email=spip_auteurs.email SET spip_asso_adherents.id_auteur= spip_auteurs.id_auteur WHERE spip_asso_adherents.email<>'' ");

			echo '</p>';
			echo '<p>';
			icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
			echo '</p>';
		}
		else{
			echo '<p><strong>'._T('asso:adherent_message_email_invalide').'</strong></p>';
			echo '<p>';
			icone(_T('asso:bouton_retour'), 'javascript:history.go(-1)', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
			echo '</p>';
		}
	}

//---------------------------- 
//MODIFICATION ADHERENT
//---------------------------- 

	if ($action=="modifie") {

		spip_query("UPDATE spip_asso_adherents SET nom='$nom', prenom='$prenom', sexe='$sexe', categorie='$categorie', fonction='$fonction', email='$email', numero='$numero', rue='$rue', cp='$cp', ville='$ville', telephone='$telephone', portable='$portable', remarques='$remarques', id_asso='$id_asso', naissance='$naissance', profession='$profession',societe='$societe', secteur='$secteur', publication='$publication', utilisateur1='$utilisateur1', utilisateur2='$utilisateur2', utilisateur3='$utilisateur3', utilisateur4='$utilisateur4', statut='$statut', validite='$validite' WHERE id_adherent='$id_adherent'");
		//on met a jour  les id_auteur pour tous les adherents
		spip_query("UPDATE spip_asso_adherents INNER JOIN spip_auteurs ON spip_asso_adherents.email=spip_auteurs.email SET spip_asso_adherents.id_auteur= spip_auteurs.id_auteur WHERE spip_asso_adherents.email<>'' ");

		echo '<p><strong>'._T('asso:adherent_message_maj_adherent',array('prenom' => $prenom, 'nom' => $nom)).'</strong></p>';
		echo '<p>';
		icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
	}

//---------------------------- 
//SUPPRESSION PROVISOIRE ADHERENT
//---------------------------- 
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

		echo '<p>';
		icone(_T('asso:bouton_retour'),$url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
	}

//---------------------------- 
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
      
