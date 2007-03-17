﻿<?php
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
include_spip('inc/acces');
include_spip('association_mes_options');

function exec_action_adherents(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

$url_action_adherents=generer_url_ecrire('action_adherents');


include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Action sur les membres actifs'));
	debut_boite_info();

	print('Nous sommes le '.date('d-m-Y').'');
	
$id_adherent=$_POST['id_adherent'];
$nom=addslashes($_POST['nom']);
$prenom=$_POST['prenom'];
$sexe=$_POST['sexe'];
$categorie=$_POST['categorie'];
$fonction=addslashes($_POST['fonction']);
$email=$_POST['email'];
$numero=$_POST['numero'];
$rue=addslashes($_POST['rue']);
$cp=$_POST['cp'];
$ville=addslashes($_POST['ville']);
$telephone=$_POST['telephone'];
$portable=$_POST['portable'];
//$divers=$_POST['divers'];
$remarques=addslashes($_POST['remarques']);
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
		$sql="INSERT INTO spip_asso_adherents (nom, prenom, sexe, email, numero, rue, cp, ville, telephone, portable, remarques, id_asso, naissance, profession, societe, secteur, publication, utilisateur1, utilisateur2, utilisateur3, utilisateur4, categorie, statut, creation) VALUES ('$nom', '$prenom', '$sexe', '$email', '$numero', '$rue', '$cp', '$ville', '$telephone', '$portable', '$remarques', '$id_asso', '$naissance', '$profession', '$societe', '$secteur', '$publication', '$utilisateur1', '$utilisateur2', '$utilisateur3', '$utilisateur4', '$categorie', 'prospect', CURRENT_DATE() )";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		echo '<p><strong>'.$prenom.' '.$nom.' a &eacute;t&eacute; ajout&eacute; dans le fichier';
		
//Validation email 	si il existe	
		if( validation_email($email) ){
		
// Inscription visiteur
		$pass = creer_pass_aleatoire(8, $email);
    		$nom_inscription =  cree_login($email);                                  
		$login = cree_login($email);
		$mdpass = md5($pass);
		$htpass = generer_htpass($pass);
		$statut = '6forum' ;
		$cookie = creer_uniqid();
		$query = "SELECT * FROM spip_auteurs WHERE email='$email'";
		$resulta = spip_query($query);                
			if (!spip_fetch_array($resulta))  {   
			$query = "INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, cookie_oubli) VALUES ('$nom_inscription', '$email', '$login', '$mdpass', '$statut', '$htpass', '$cookie') ";
			$maj = spip_query($query); 			
			if ($maj){echo ' et enregistr&eacute; comme visiteur';}	  
			//on met a jour  les id_auteur pour tous les adherents
			$query = "UPDATE spip_asso_adherents INNER JOIN spip_auteurs ON spip_asso_adherents.email=spip_auteurs.email SET spip_asso_adherents.id_auteur= spip_auteurs.id_auteur WHERE spip_asso_adherents.email<>'' ";	
			$maj = spip_query($query);   			
			}
}
		echo '</strong></p>';
		echo '<p>';
		icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
}

//---------------------------- 
//MODIFICATION ADHERENT
//---------------------------- 	

if ($action=="modifie"){

	$sql = "UPDATE spip_asso_adherents SET nom='$nom', prenom='$prenom', sexe='$sexe', categorie='$categorie', fonction='$fonction', email='$email', numero='$numero', rue='$rue', cp='$cp', ville='$ville', telephone='$telephone', portable='$portable', remarques='$remarques', id_asso='$id_asso', naissance='$naissance', profession='$profession',societe='$societe', secteur='$secteur', publication='$publication', utilisateur1='$utilisateur1', utilisateur2='$utilisateur2', utilisateur3='$utilisateur3', utilisateur4='$utilisateur4', statut='$statut'  WHERE id_adherent='$id_adherent'";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	//on met a jour  les id_auteur pour tous les adherents	
	$sql = "UPDATE spip_asso_adherents INNER JOIN spip_auteurs ON spip_asso_adherents.email=spip_auteurs.email SET spip_asso_adherents.id_auteur= spip_auteurs.id_auteur WHERE spip_asso_adherents.email<>'' ";	
	$req = spip_query($query);   
	
	echo '<p><strong>Les donn&eacute;es de '.$prenom.' '.$nom.' ont &eacute;t&eacute; mises &agrave; jour !</strong></p>';
	echo '<p>';
	icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
	echo '</p>';
}
//---------------------------- 
//SUPPRESSION PROVISOIRE ADHERENT
//---------------------------- 		
if (isset($_POST['delete'])) {

$url_retour = $_SERVER['HTTP_REFERER'];

$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
$count=count ($delete_tab);

echo '<p>Vous vous appr&ecirc;tez &agrave; effacer les membres  : <br>';
echo '<table>';
echo '<form action="'.$url_action_adherents.'"  method="post">';
for ( $i=0 ; $i < $count ; $i++ )
{	$id = $delete_tab[$i];
	$sql = "SELECT * FROM spip_asso_adherents where id_adherent='$id'";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());  
	while($val = mysql_fetch_assoc($req)) 
	{
echo '<tr>';
echo '<td><strong>'.$val['nom'].' '.$val['prenom'].'</strong>';
echo '<td>';
echo '<input type=checkbox name="drop[]" value="'.$id.'" checked>';
	}	
}
echo '<tr>';
echo '<td colspan="2"><input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="submit" type="submit" value="Confirmer" class="fondo"></td></tr>';	
echo '<table>';
echo '</p>';

echo '<p>';
icone(_T('asso:Retour'),$url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
echo '</p>';
}

//---------------------------- 
//  SUPPRESSION DEFINITIVE ADHERENTS
//---------------------------- 		
if (isset($_POST['drop'])) {

$url_retour=$_POST['url_retour'];

$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
$count=count ($drop_tab);

for ( $i=0 ; $i < $count ; $i++ )
{	$id = $drop_tab[$i];
	$sql = "DELETE FROM spip_asso_adherents WHERE id_adherent='$id'";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());  
}
echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';	

echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
echo '</p>';
}

fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>
      