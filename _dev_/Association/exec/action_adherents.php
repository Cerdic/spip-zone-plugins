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
include_spip('inc/acces');

if(!function_exists(_q)){
		function _q($a) {
			return (is_int($a)) ? strval($a) : ("'" . addslashes($a) . "'");
			}
		}

function exec_action_adherents() {
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

	$url_action_adherents=generer_url_ecrire('action_adherents');

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_action_membres_actifs'));
	debut_boite_info();

	print association_date_du_jour();

	$id_asso=$_POST['id_asso'];
	$id_adherent=$_POST['id_adherent'];
	$nom=addslashes($_POST['nom']);
	$prenom=$_POST['prenom'];
	$naissance=$_POST['naissance'];
	$sexe=$_POST['sexe'];
	$email=$_POST['email'];
	$rue=addslashes($_POST['rue']);
	$numero=addslashes($_POST['numero']);
	$ville=addslashes($_POST['ville']);
	$cp=$_POST['cp'];
	$telephone=$_POST['telephone'];
	$portable=$_POST['portable'];
	$profession=addslashes($_POST['profession']);
	$societe=addslashes($_POST['societe']);
	$secteur=$_POST['secteur'];
	
	$categorie=addslashes($_POST['categorie']);
	$fonction=addslashes($_POST['fonction']);
	$publication=$_POST['publication'];
	$validite=$_POST['validite'];
	$utilisateur1=$_POST['utilisateur1'];
	$utilisateur2=$_POST['utilisateur2'];
	$utilisateur3=$_POST['utilisateur3'];
	$utilisateur4=$_POST['utilisateur4'];
	$remarques=$_POST['remarques'];	
	$remarques=nl2br($remarques); 
	//$numero=$_POST['numero'];
	//$divers=$_POST['divers'];
	//$identifiant=$_POST['identifiant'];
	//$passe=$_POST['passe'];
	$statut=$_POST['statut'];

	$action=$_POST['action'];
	$url_retour=$_POST['url_retour'];
	$rue=nl2br($rue); 

//---------------------------- 
//AJOUT ADHERENT
//---------------------------- 
	
	if ($action=="ajoute"){

// Inscription adherent
		$query=spip_query("INSERT INTO spip_asso_adherents (nom, prenom, sexe, email, rue, numero, cp, ville, telephone, portable, remarques, id_asso, naissance, 
		profession, societe, secteur, publication, utilisateur1, utilisateur2, utilisateur3, utilisateur4, categorie, statut, creation, validite, fonction) 
		VALUES ('$nom', '$prenom', '$sexe', '$email', '$rue', '$numero', '$cp', '$ville', '$telephone', '$portable', ".spip_abstract_quote($remarques).", 
		'$id_asso', '$naissance', '$profession', '$societe', '$secteur', 
		'$publication', '$utilisateur1', '$utilisateur2', '$utilisateur3', '$utilisateur4', 
		'$categorie', 'prospect', CURRENT_DATE(), '$validite', '$fonction' )");
		if ($query) { 
			echo '<p><strong>'._T('asso:adherent_message_ajout_adherent',array('prenom' => $prenom, 'nom' => $nom)).'</strong></p>';
		}
		
// Inscription visiteur
		if( email_valide($email) ){
			$pass = creer_pass_aleatoire(8, $email);
			$nom_inscription =  $prenom.' '.$nom;                                  
			$login = association_cree_login($nom);
			$mdpass = md5($pass);
			$htpass = generer_htpass($pass);
			$statut = '6forum' ;
			$cookie = creer_uniqid();
			$query = spip_query("SELECT * FROM spip_auteurs WHERE email='$email'");          
			if (!spip_fetch_array($query))  {   
				$sql=spip_query("INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, cookie_oubli) VALUES ('$nom_inscription', '$email', '$login', '$mdpass', '$statut', '$htpass', '$cookie') ");
				if ($sql) { echo '<p><strong>'._T('asso:adherent_message_ajout_adherent_suite').'</strong></p>'; }
			}			
			//on enregistre l'id_auteur
			$query=spip_query("SELECT * FROM spip_auteurs WHERE email='$email' ");
			while ($data=spip_fetch_array($query)) {
				$id_auteur=$data['id_auteur'];
				$email=$data['email'];
				spip_query("UPDATE spip_asso_adherents SET id_auteur=$id_auteur  WHERE email='$email' AND email <>'' ");
			}
			echo '<p>';
			icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
			echo '</p>';
		}
		else {
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
		
		spip_query("UPDATE spip_asso_adherents SET 
		nom="._q($nom).", prenom="._q($prenom).", sexe="._q($sexe).", categorie="._q($categorie).", 
		fonction="._q($fonction).", email="._q($email).", numero="._q($numero).", rue="._q($rue).", cp="._q($cp).", ville="._q($ville).", 
		telephone="._q($telephone).", portable="._q($portable).", remarques="._q($remarques).", id_asso="._q($id_asso).", naissance="._q($naissance).", 
		profession="._q($profession).",societe="._q($societe).", secteur="._q($secteur).", publication="._q($publication).", utilisateur1="._q($utilisateur1).", 
		utilisateur2="._q($utilisateur2).", utilisateur3="._q($utilisateur3).", utilisateur4="._q($utilisateur4).", statut="._q($statut).", validite="._q($validite)." 
		WHERE id_adherent="._q($id_adherent));
		//on enregistre l'id_auteur
		$query=spip_query("SELECT * FROM spip_auteurs WHERE email="._q($email));
		while ($data=spip_fetch_array($query)) {
			$id_auteur=$data['id_auteur'];
			$email=$data['email'];
			spip_query("UPDATE spip_asso_adherents SET id_auteur=$id_auteur  WHERE email="._q($email)." AND email <>'' ");
		}

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
      
