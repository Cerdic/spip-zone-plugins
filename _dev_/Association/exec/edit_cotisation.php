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
function exec_edit_cotisation(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Enregistrement des cotisations'));
	debut_boite_info();

print('Nous sommes le '.date('d-m-Y').'');	

echo'<div align="center">';
$id_adherent= $_POST['id_adherent'];
$date= $_POST['date'];
$validite= $_POST['validite'];
$categorie= $_POST['categorie'];
$montant= $_POST['montant'];
$statut= $_POST['statut'];

$sql = "UPDATE spip_asso_adherents SET date='$date', validite='$validite', categorie='$categorie', montant='$montant', statut='$statut'  WHERE id_adherent='$id_adherent'";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	echo "<br>Les cotisations ont &eacute;t&eacute; enregistr&eacute;es";
		
// ON FERME TOUT
fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>

