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

function exec_action_relances(){
global $connect_statut, $connect_toutes_rubriques;

debut_page();

$url_asso = generer_url_ecrire('association');
$url_action_relances = generer_url_ecrire('action_relances');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Relance de cotisations'));
	debut_boite_info();

print('<p>Nous sommes le '.date('d-m-Y').'</p>');

//On récupère les données globales
$sujet=$_POST['sujet'];
$message=$_POST['message'] ;
$message=utf8_decode($message) ;
//$message=utf8_decode($_POST['message']) ;
$statut=$_POST['statut'];
$email_tab=(isset($_POST["email"])) ? $_POST["email"]:array();
$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array();
$relance_tab=(isset($_POST["relance"])) ? $_POST["relance"]:array();
$count=count ($email_tab);
$query = spip_query ("SELECT * FROM spip_asso_profil WHERE id_profil=1");

while ($data = mysql_fetch_assoc($query)){
//On prépare le mail et on envoi! On peut modifier le $headers à sa guise
$headers .= "From: ".$data['nom']." <".$data['mail'].">\n";       //expéditeur Association
$headers .= "Reply-To: ".$data['mail']."\n";      // réponse automatique à Association
$headers .= "X-Mailer: PHP/" . phpversion();         // mailer
$headers .= "X-Sender: < ".$data['mail'].">\n";   } 
$headers .= "X-Priority: 1\n";                // Message urgent ! 
$headers .= "X-MSMail-Priority: High\n";         // définition de la priorité
//$headers .= "Return-Path: < webmaster@ >\n"; // En cas d' erreurs 
//$headers .= "Errors-To: < webmaster@ >\n";    // En cas d' erreurs 
//$headers .= "cc:  \n"; // envoi en copie à …
//$headers .= "bcc: \n";          // envoi en copie cachée à …
//$headers .= "Content-Type: text/html; charset=iso-8859-1\n"; // Type Mime pour un message au format HTML

//On envoit le mail aux destinatairex sélectionnés, on affiche le membre relancé et on change le statut de relance

for ( $i=0 ; $i < $count ; $i++ )
{
$email = $email_tab[$i];
$statut = $statut_tab[$i];
$relance = $relance_tab[$i];

	if ( isset ( $relance ) )
	{
	mail($email, $sujet, $message, $headers);

	if ($statut=="echu"){
	spip_query("UPDATE spip_asso_adherents SET statut='relance' WHERE id_adherent = '$relance' ");
	}
	}
}

echo '<div><br><strong>'.$count;
if ($count=1)
{ echo ' relance effectu&eacute;e';}
else
{ echo ' relances effectu&eacute;es';}
echo '</strong></div>';

	
	//remettre le champ 0 à 1 et réactualiser la date
//spip_query("UPDATE spip_adherents SET regle_le='relance',date_jour=NOW() WHERE id_ad=$id");	
	
fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();
}
?>
